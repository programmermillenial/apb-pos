<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    function datatable(Request $request)
    {
        if ($request->ajax()) {
            $purchaseOrders = PurchaseOrder::with(['outlet', 'supplier'])
                ->latest();

            return DataTables::of($purchaseOrders)
                ->addIndexColumn()
                ->addColumn('outlet_name', function ($row) {
                    return $row->outlet->name ?? '-';
                })
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier->name ?? '-';
                })
                ->editColumn('po_date', function ($row) {
                    return date('d/m/Y', strtotime($row->po_date));
                })
                ->editColumn('grand_total', function ($row) {
                    return 'Rp ' . number_format($row->grand_total, 0, ',', '.');
                })
                ->addColumn('status_badge', function ($row) {
                    return match ($row->status) {
                        'draft' => '<span class="badge bg-secondary">Draft</span>',
                        'submitted' => '<span class="badge bg-info">Submitted</span>',
                        'approved' => '<span class="badge bg-success">Approved</span>',
                        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                        'received' => '<span class="badge bg-primary">Received</span>',
                        default => '<span class="badge bg-light text-dark">Unknown</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);
                    $url = route('purchase-orders.destroy', $id);
                    $module = 'purchase order';

                    $btn = '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('purchase-orders.show', $id) . '" class="btn btn-sm btn-info">
                                <i class="ri-eye-line"></i>
                            </a>
                    ';

                    if ($row->status === 'draft') {
                        $btn .= '
                            <a href="' . route('purchase-orders.edit', $id) . '" class="btn btn-sm btn-warning">
                                <i class="ri-edit-line"></i>
                            </a>

                            <button type="button" class="btn btn-sm btn-danger btn-delete" onclick="deleteData(\'' . $url . '\', \'' . $module . '\')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        ';
                    }

                    $btn .= '</div>';

                    return $btn;
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
        return view('purchase-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();

        return view('purchase-orders.create', compact('outlets', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty.*' => 'required|numeric|min:1',
            'price.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $discountAmount = (float) ($request->discount_amount ?? 0);
            $taxAmount = (float) ($request->tax_amount ?? 0);

            $po = PurchaseOrder::create([
                'store_id' => 1,
                'outlet_id' => $request->outlet_id,
                'supplier_id' => $request->supplier_id,
                'po_number' => $this->generatePoNumber(),
                'po_date' => $request->po_date,
                'expected_date' => $request->expected_date,
                'subtotal' => 0,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => 0,
                'status' => 'draft',
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->product_id as $key => $productId) {
                $product = Product::findOrFail($productId);

                $qty = (int) $request->qty[$key];
                $price = (float) $request->price[$key];
                $itemDiscount = (float) ($request->item_discount_amount[$key] ?? 0);

                $itemSubtotal = ($qty * $price) - $itemDiscount;
                $subtotal += $itemSubtotal;

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'qty' => $qty,
                    'price' => $price,
                    'discount_amount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $grandTotal = $subtotal - $discountAmount + $taxAmount;

            $po->update([
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchaseOrderId = Crypt::decryptString($id);
        $purchaseOrder = PurchaseOrder::with(['outlet', 'supplier', 'purchaseOrderDetails.product', 'creator', 'approver'])->findOrFail($purchaseOrderId);

        // dd($purchaseOrder->toArray());
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchaseOrderId = Crypt::decryptString($id);
        $purchaseOrder = PurchaseOrder::with('purchaseOrderDetails')->findOrFail($purchaseOrderId);

        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.index')->with('error', 'Purchase Order tidak bisa diedit karena sudah diproses.');
        }

        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'outlets', 'suppliers', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $purchaseOrderId = Crypt::decryptString($id);
        $purchaseOrder = PurchaseOrder::with('purchaseOrderDetails')->findOrFail($purchaseOrderId);

        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.index')->with('error', 'Purchase Order tidak bisa diupdate karena sudah diproses.');
        }

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty.*' => 'required|numeric|min:1',
            'price.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $discountAmount = (float) ($request->discount_amount ?? 0);
            $taxAmount = (float) ($request->tax_amount ?? 0);

            $purchaseOrder->purchaseOrderDetails()->delete();

            foreach ($request->product_id as $key => $productId) {
                $product = Product::findOrFail($productId);

                $qty = (int) $request->qty[$key];
                $price = (float) $request->price[$key];
                $itemDiscount = (float) ($request->item_discount_amount[$key] ?? 0);

                $itemSubtotal = ($qty * $price) - $itemDiscount;
                $subtotal += $itemSubtotal;

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'qty' => $qty,
                    'price' => $price,
                    'discount_amount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $grandTotal = $subtotal - $discountAmount + $taxAmount;

            $purchaseOrder->update([
                'outlet_id' => $request->outlet_id,
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'expected_date' => $request->expected_date,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'note' => $request->note,
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decryptString($id);

        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'draft') {
            return response()->json([
                'status' => false,
                'message' => 'Purchase Order tidak bisa dihapus karena sudah diproses.',
            ]);
        }

        $purchaseOrder->delete();

        return response()->json([
            'status' => true,
            'message' => 'Purchase Order berhasil dihapus.',
        ]);
    }

    function submit(string $id)
    {
        $id = Crypt::decryptString($id);

        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Purchase Order tidak bisa disubmit.');
        }

        $purchaseOrder->update([
            'status' => 'submitted',
        ]);

        return back()->with('success', 'Purchase Order berhasil disubmit.');
    }

    function approve(string $id)
    {
        $id = Crypt::decryptString($id);

        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'submitted') {
            return back()->with('error', 'Purchase Order belum disubmit.');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Purchase Order berhasil diapprove.');
    }

    function reject(string $id)
    {
        $id = Crypt::decryptString($id);

        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return back()->with('error', 'Purchase Order tidak bisa dibatalkan.');
        }

        $purchaseOrder->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Purchase Order berhasil dibatalkan.');
    }

    private function generatePoNumber()
    {
        $date = now()->format('Ymd');

        $lastPo = PurchaseOrder::whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $number = $lastPo ? ((int) substr($lastPo->po_number, -4)) + 1 : 1;

        return 'PO-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function productSearch(Request $request)
    {
        $search = $request->get('q');

        $products = Product::query()
            ->where('is_active', 1)
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
            'results' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => ($product->sku ? $product->sku . ' - ' : '') . $product->name,
                    'cost_price' => (float) $product->cost_price,
                ];
            }),
        ]);
    }
}
