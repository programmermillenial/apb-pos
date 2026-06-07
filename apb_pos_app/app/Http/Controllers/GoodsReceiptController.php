<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GoodsReceiptController extends Controller
{
    function datatable(Request $request)
    {
        $data = GoodsReceipt::with('purchaseOrder')
            ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('po_number', function ($row) {
                return $row->purchaseOrder->po_number ?? '-';
            })
            ->addColumn('receipt_date', function ($row) {
                return date('d M Y', strtotime($row->receipt_date));
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'received') {
                    return '<span class="badge bg-success">Received</span>';
                }

                if ($row->status === 'cancelled') {
                    return '<span class="badge bg-danger">Cancelled</span>';
                }

                return '<span class="badge bg-secondary">Draft</span>';
            })
            ->addColumn('action', function ($row) {
                $id = Crypt::encryptString($row->id);

                return '
                    <div class="d-flex justify-content-center gap-2">
                        <a href="' . route('goods-receipts.show', $id) . '" 
                           class="btn btn-sm btn-info">
                            <i class="ri-eye-line"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('goods-receipts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('goods-receipts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'received_qty' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $purchaseOrderId = $request->purchase_order_id;

            $purchaseOrder = PurchaseOrder::with([
                'purchaseOrderDetails.goodsReceiptDetails',
                'purchaseOrderDetails.product'
            ])
                ->lockForUpdate()
                ->findOrFail($purchaseOrderId);

            if (!in_array($purchaseOrder->status, ['approved', 'partial_received'])) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->with('error', 'Purchase Order sudah tidak bisa diproses Goods Receipt.');
            }

            $hasReceivedItem = false;

            $goodsReceipt = GoodsReceipt::create([
                'purchase_order_id' => $purchaseOrder->id,
                'gr_number' => $this->generateNumber(),
                'receipt_date' => $request->receipt_date,
                'received_by' => Auth::user()->name ?? null,
                'notes' => $request->notes,
                'status' => 'received',
            ]);

            foreach ($request->received_qty as $purchaseOrderDetailId => $receivedQty) {
                $receivedQty = $this->clearNumber($receivedQty);

                if ($receivedQty <= 0) {
                    continue;
                }

                $purchaseOrderDetail = $purchaseOrder->purchaseOrderDetails
                    ->where('id', $purchaseOrderDetailId)
                    ->first();

                if (!$purchaseOrderDetail) {
                    throw new \Exception('Item Purchase Order tidak valid.');
                }

                $alreadyReceivedQty = $purchaseOrderDetail->goodsReceiptDetails->sum('received_qty');
                $remainingQty = $purchaseOrderDetail->qty - $alreadyReceivedQty;

                if ($remainingQty <= 0) {
                    throw new \Exception('Produk ' . ($purchaseOrderDetail->product->name ?? '-') . ' sudah diterima semua.');
                }

                if ($receivedQty > $remainingQty) {
                    throw new \Exception(
                        'Qty diterima untuk produk ' .
                            ($purchaseOrderDetail->product->name ?? '-') .
                            ' melebihi sisa qty. Sisa qty: ' . number_format($remainingQty, 0, ',', '.')
                    );
                }

                $subtotal = $receivedQty * $purchaseOrderDetail->price;

                GoodsReceiptDetail::create([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'purchase_order_detail_id' => $purchaseOrderDetail->id,
                    'product_id' => $purchaseOrderDetail->product_id,
                    'ordered_qty' => $purchaseOrderDetail->qty,
                    'received_qty' => $receivedQty,
                    'cost_price' => $purchaseOrderDetail->price,
                    'subtotal' => $subtotal,
                ]);

                $hasReceivedItem = true;
            }

            if (!$hasReceivedItem) {
                throw new \Exception('Minimal harus ada 1 item dengan qty diterima lebih dari 0.');
            }

            $this->createStockMovementFromGoodsReceipt($goodsReceipt);
            $this->updatePurchaseOrderReceiveStatus($purchaseOrder->id);

            DB::commit();

            return redirect()
                ->route('goods-receipts.index')
                ->with('success', 'Goods Receipt berhasil dibuat dan stok berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decryptString($id);

        $goodsReceipt = GoodsReceipt::with([
            'purchaseOrder',
            'details.product',
        ])->findOrFail($id);

        return view('goods-receipts.show', compact('goodsReceipt'));
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

    public function searchPurchaseOrder(Request $request)
    {
        $search = $request->search;

        $purchaseOrders = PurchaseOrder::query()
            ->whereIn('status', ['approved', 'partial_received'])
            ->when($search, function ($query) use ($search) {
                $query->where('po_number', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json(
            $purchaseOrders->map(function ($po) {
                return [
                    'id' => $po->id,
                    'text' => $po->po_number . ' - ' . strtoupper(str_replace('_', ' ', $po->status)),
                ];
            })
        );
    }

    public function getPurchaseOrderDetails(string $id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'purchaseOrderDetails.product',
            'purchaseOrderDetails.goodsReceiptDetails'
        ])->findOrFail($id);

        if (!in_array($purchaseOrder->status, ['approved', 'partial_received'])) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase Order sudah tidak bisa diproses Goods Receipt.',
                'details' => [],
            ], 422);
        }

        $details = $purchaseOrder->purchaseOrderDetails->map(function ($item) {
            $receivedQty = $item->goodsReceiptDetails->sum('received_qty');
            $remainingQty = $item->qty - $receivedQty;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name ?? $item->product_name ?? '-',
                'qty' => $item->qty,
                'received_qty' => $receivedQty,
                'remaining_qty' => max(0, $remainingQty),
                'price' => $item->price,
                'subtotal' => max(0, $remainingQty) * $item->price,
            ];
        })->filter(function ($item) {
            return $item['remaining_qty'] > 0;
        })->values();

        return response()->json([
            'status' => true,
            'details' => $details,
        ]);
    }

    private function updatePurchaseOrderReceiveStatus(int $purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::with([
            'purchaseOrderDetails.goodsReceiptDetails'
        ])->findOrFail($purchaseOrderId);

        $totalPurchaseQty = 0;
        $totalReceivedQty = 0;

        foreach ($purchaseOrder->purchaseOrderDetails as $item) {
            $purchaseQty = (int) $item->qty;
            $receivedQty = (int) $item->goodsReceiptDetails->sum('received_qty');

            $totalPurchaseQty += $purchaseQty;
            $totalReceivedQty += $receivedQty;
        }

        $status = 'approved';

        if ($totalReceivedQty > 0 && $totalReceivedQty < $totalPurchaseQty) {
            $status = 'partial_received';
        }

        if ($totalPurchaseQty > 0 && $totalReceivedQty >= $totalPurchaseQty) {
            $status = 'received';
        }

        $purchaseOrder->update([
            'status' => $status,
        ]);
    }

    private function createStockMovementFromGoodsReceipt(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->loadMissing([
            'purchaseOrder',
            'details.product'
        ]);

        $inventoryService = app(InventoryService::class);

        $outletId = $goodsReceipt->outlet_id
            ?? $goodsReceipt->purchaseOrder->outlet_id
            ?? null;

        if (!$outletId) {
            throw new \Exception('Outlet tidak ditemukan pada Goods Receipt / Purchase Order.');
        }

        foreach ($goodsReceipt->details as $detail) {
            $receivedQty = (int) $detail->received_qty;

            if ($receivedQty <= 0) {
                continue;
            }

            $exists = StockMovement::where('source_type', 'GOODS_RECEIPT')
                ->where('source_id', $goodsReceipt->id)
                ->where('product_id', $detail->product_id)
                ->exists();

            if ($exists) {
                continue;
            }

            $inventoryService->stockIn([
                'outlet_id' => $outletId,
                'product_id' => $detail->product_id,
                'movement_date' => $goodsReceipt->receipt_date ?? now()->toDateString(),
                'qty' => $receivedQty,
                'source_type' => 'GOODS_RECEIPT',
                'source_id' => $goodsReceipt->id,
                'reference_no' => $goodsReceipt->gr_number,
                'cost_price' => $detail->cost_price ?? $detail->product->cost_price ?? 0,
                'sell_price' => $detail->product->sell_price ?? 0,
                'note' => 'Stock masuk dari Goods Receipt',
            ]);
        }
    }

    private function clearNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) str_replace(['.', ','], '', $value);
    }

    private function generateNumber()
    {
        $prefix = 'GR-' . date('Ymd') . '-';

        $last = GoodsReceipt::whereDate('created_at', now())
            ->where('gr_number', 'like', $prefix . '%')
            ->latest()
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->gr_number, -4);

        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
