<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductOutlet;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();

            $sales = Sale::with(['outlet', 'customer', 'creator'])
                ->when($user?->outlet_id, fn($query) => $query->where('outlet_id', $user->outlet_id))
                ->latest('sale_date')
                ->latest('id');

            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('outlet_name', fn($row) => $row->outlet->name ?? '-')
                ->addColumn('customer_name', fn($row) => $row->customer->name ?? 'Umum')
                ->addColumn('cashier_name', fn($row) => $row->creator->name ?? '-')
                ->editColumn('sale_date', fn($row) => $row->sale_date ? $row->sale_date->format('d/m/Y') : '-')
                ->editColumn('payment_method', fn($row) => strtoupper($row->payment_method))
                ->editColumn('grand_total', fn($row) => 'Rp ' . number_format($row->grand_total, 0, ',', '.'))
                ->addColumn('status_badge', function ($row) {
                    return match ($row->status) {
                        'paid' => '<span class="badge bg-success">Paid</span>',
                        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                        default => '<span class="badge bg-light text-dark">' . ucfirst($row->status) . '</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);

                    return '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('sales.show', $id) . '" class="btn btn-sm btn-info" data-loading="true">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="' . route('sales.receipt', $id) . '" class="btn btn-sm btn-primary" data-loading="true">
                                <i class="ri-printer-line"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $outlets = Outlet::where('is_active', 1)
            ->with('store')
            ->when($user?->outlet_id, fn($query) => $query->where('id', $user->outlet_id))
            ->orderBy('name')
            ->get();

        $defaultOutletId = old('outlet_id', $user?->outlet_id ?? $outlets->first()?->id);
        $outletTaxRates = $outlets->mapWithKeys(fn($outlet) => [
            $outlet->id => (float) ($outlet->store->tax_rate ?? 0),
        ]);

        $customers = Customer::where('is_active', 1)
            ->when($defaultOutletId, function ($query) use ($defaultOutletId) {
                $query->where(function ($q) use ($defaultOutletId) {
                    $q->whereNull('outlet_id')->orWhere('outlet_id', $defaultOutletId);
                });
            })
            ->orderBy('name')
            ->get();

        return view('sales.index', compact('outlets', 'customers', 'defaultOutletId', 'outletTaxRates'));
    }

    public function history()
    {
        return view('sales.history');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'discount_amount' => $this->normalizeCurrency($request->discount_amount),
            'tax_amount' => $this->normalizeCurrency($request->tax_amount),
            'paid_amount' => $this->normalizeCurrency($request->paid_amount),
            'price' => collect($request->price ?? [])->map(fn($value) => $this->normalizeCurrency($value))->all(),
            'item_discount_amount' => collect($request->item_discount_amount ?? [])->map(fn($value) => $this->normalizeCurrency($value))->all(),
        ]);

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'customer_id' => 'nullable|exists:customers,id',
            'new_customer_name' => 'required_with:new_customer_phone|nullable|string|max:255',
            'new_customer_phone' => 'nullable|string|max:50|unique:customers,phone',
            'sale_date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|min:0',
            'item_discount_amount' => 'nullable|array',
            'item_discount_amount.*' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris,debit,credit',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $outlet = Outlet::findOrFail($request->outlet_id);
            $subtotal = 0;
            $discountAmount = (float) ($request->discount_amount ?? 0);
            $taxRate = (float) ($outlet->store->tax_rate ?? 0);
            $taxAmount = 0;
            $customerId = $request->customer_id;

            if ($request->filled('new_customer_name')) {
                $customer = Customer::create([
                    'store_id' => $outlet->store_id,
                    'outlet_id' => $outlet->id,
                    'code' => $this->generateCustomerCode(),
                    'name' => $request->new_customer_name,
                    'phone' => $request->new_customer_phone,
                    'is_active' => 1,
                ]);

                $customerId = $customer->id;
            }

            $sale = Sale::create([
                'store_id' => $outlet->store_id,
                'outlet_id' => $outlet->id,
                'customer_id' => $customerId,
                'invoice_number' => $this->generateInvoiceNumber(),
                'sale_date' => $request->sale_date,
                'subtotal' => 0,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => 0,
                'paid_amount' => (float) $request->paid_amount,
                'change_amount' => 0,
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->product_id as $key => $productId) {
                $product = Product::findOrFail($productId);
                $qty = (int) $request->qty[$key];
                $price = (float) $request->price[$key];
                $itemDiscount = (float) ($request->item_discount_amount[$key] ?? 0);
                $itemSubtotal = max(($qty * $price) - $itemDiscount, 0);

                $subtotal += $itemSubtotal;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'qty' => $qty,
                    'price' => $price,
                    'discount_amount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $taxableAmount = max($subtotal - $discountAmount, 0);
            $taxAmount = round($taxableAmount * $taxRate / 100);
            $grandTotal = max($taxableAmount + $taxAmount, 0);
            $paidAmount = (float) $request->paid_amount;

            if ($paidAmount < $grandTotal) {
                throw new \Exception('Nominal bayar kurang dari grand total.');
            }

            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'change_amount' => $paidAmount - $grandTotal,
            ]);

            $inventoryService = app(InventoryService::class);

            foreach ($sale->saleDetails as $detail) {
                $inventoryService->stockOut([
                    'outlet_id' => $sale->outlet_id,
                    'product_id' => $detail->product_id,
                    'qty' => $detail->qty,
                    'movement_date' => $sale->sale_date->toDateString(),
                    'source_type' => 'SALES',
                    'source_id' => $sale->id,
                    'reference_no' => $sale->invoice_number,
                    'sell_price' => $detail->price,
                    'note' => 'Penjualan kasir',
                ]);
            }

            if ($sale->customer_id) {
                Customer::where('id', $sale->customer_id)->update([
                    'last_transaction_at' => now(),
                ]);

                Customer::where('id', $sale->customer_id)->increment('total_transactions');
                Customer::where('id', $sale->customer_id)->increment('total_spent', $grandTotal);
            }

            DB::commit();

            return redirect()
                ->route('sales.receipt', [
                    'id' => $sale->id,
                    'print' => 1,
                ])
                ->with('success', 'Transaksi berhasil disimpan. Invoice: ' . $sale->invoice_number);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Sales transaction failed', [
                'message' => $e->getMessage(),
                'payload' => $request->except('_token'),
                'user_id' => Auth::id(),
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $saleId = Crypt::decryptString($id);

        $sale = Sale::with(['store', 'outlet', 'customer', 'creator', 'saleDetails.product'])
            ->findOrFail($saleId);

        if (Auth::user()?->outlet_id && Auth::user()->outlet_id !== $sale->outlet_id) {
            abort(403);
        }

        return view('sales.show', compact('sale'));
    }

    public function receipt(string $id)
    {
        $sale = $this->findSaleForReceipt($id);

        return view('sales.receipt', compact('sale'));
    }

    public function receiptPdf(string $id)
    {
        $sale = $this->findSaleForReceipt($id);

        $pdf = Pdf::loadView('sales.receipt-pdf', compact('sale'))
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isFontSubsettingEnabled', true)
            ->setPaper([0, 0, 226.77, 841.89], 'portrait');

        return $pdf->download($sale->invoice_number . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function productSearch(Request $request)
    {
        $search = $request->get('q');
        $outletId = $request->get('outlet_id');

        $products = Product::query()
            ->with(['unit', 'productOutlets' => fn($query) => $query->where('outlet_id', $outletId)])
            ->where('is_active', 1)
            ->when($outletId, function ($query) use ($outletId) {
                $query->whereHas('productOutlets', fn($q) => $q->where('outlet_id', $outletId)->where('stock', '>', 0));
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $products->map(function ($product) use ($outletId) {
                $stock = $outletId
                    ? (int) ($product->productOutlets->first()?->stock ?? 0)
                    : (int) ProductOutlet::where('product_id', $product->id)->sum('stock');

                return [
                    'id' => $product->id,
                    'text' => trim(($product->sku ? $product->sku . ' - ' : '') . $product->name),
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'price' => (float) $product->sell_price,
                    'stock' => $stock,
                    'unit' => $product->unit->name ?? '',
                ];
            }),
        ]);
    }

    public function customerSearch(Request $request)
    {
        $search = $request->get('q');
        $outletId = $request->get('outlet_id');

        $customers = Customer::query()
            ->where('is_active', 1)
            ->when($outletId, function ($query) use ($outletId) {
                $query->where(function ($q) use ($outletId) {
                    $q->whereNull('outlet_id')->orWhere('outlet_id', $outletId);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $customers->map(fn($customer) => [
                'id' => $customer->id,
                'text' => $customer->name . ($customer->phone ? ' - ' . $customer->phone : ''),
                'name' => $customer->name,
                'phone' => $customer->phone,
            ]),
        ]);
    }

    public function customerStore(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50|unique:customers,phone',
        ]);

        $outlet = Outlet::findOrFail($request->outlet_id);

        $customer = Customer::create([
            'store_id' => $outlet->store_id,
            'outlet_id' => $outlet->id,
            'code' => $this->generateCustomerCode(),
            'name' => $request->name,
            'phone' => $request->phone,
            'is_active' => 1,
        ]);

        return response()->json([
            'id' => $customer->id,
            'text' => $customer->name . ($customer->phone ? ' - ' . $customer->phone : ''),
            'message' => 'Customer berhasil ditambahkan.',
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');

        $lastSale = Sale::whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $number = $lastSale ? ((int) substr($lastSale->invoice_number, -4)) + 1 : 1;

        return 'INV-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function findSaleForReceipt(string $id): Sale
    {
        $saleId = is_numeric($id) ? $id : Crypt::decryptString($id);

        $sale = Sale::with(['store', 'outlet', 'customer', 'creator', 'saleDetails.product'])
            ->findOrFail($saleId);

        if (Auth::user()?->outlet_id && Auth::user()->outlet_id !== $sale->outlet_id) {
            abort(403);
        }

        return $sale;
    }

    private function generateCustomerCode(): string
    {
        $lastCustomer = Customer::latest('id')->first();
        $number = $lastCustomer ? $lastCustomer->id + 1 : 1;

        return 'CUST-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    private function normalizeCurrency($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $value = preg_replace('/[^0-9,.-]/', '', (string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
