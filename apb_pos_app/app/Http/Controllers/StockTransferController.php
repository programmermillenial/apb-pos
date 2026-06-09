<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class StockTransferController extends Controller
{
    function datatable(Request $request)
    {
        $data = StockTransfer::with(['fromOutlet', 'toOutlet', 'creator', 'approver'])
            ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('transfer_date', function ($row) {
                return date('d/m/Y', strtotime($row->transfer_date));
            })
            ->addColumn('from_outlet_name', function ($row) {
                return $row->fromOutlet->name ?? '-';
            })
            ->addColumn('to_outlet_name', function ($row) {
                return $row->toOutlet->name ?? '-';
            })
            ->addColumn('total_items', function ($row) {
                return $row->details->count() . ' item';
            })
            ->addColumn('status_badge', function ($row) {
                return match ($row->status) {
                    'draft' => '<span class="badge bg-secondary">Draft</span>',
                    'approved' => '<span class="badge bg-warning text-dark">Approved</span>',
                    'received' => '<span class="badge bg-success">Received</span>',
                    default => '<span class="badge bg-light text-dark">Unknown</span>',
                };
            })
            ->addColumn('creator_name', function ($row) {
                return $row->creator->name ?? '-';
            })
            ->addColumn('action', function ($row) {
                $id = Crypt::encryptString($row->id);
                $url = route('stock-transfers.destroy', $id);
                $module = 'stock transfer';

                $btn = '
                    <div class="d-flex justify-content-center gap-1">
                        <a href="' . route('stock-transfers.show', $id) . '" class="btn btn-sm btn-info">
                            <i class="ri-eye-line"></i>
                        </a>
                ';

                if ($row->status === 'draft') {
                    $btn .= '
                        <a href="' . route('stock-transfers.edit', $id) . '" class="btn btn-sm btn-warning">
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

    public function index()
    {
        return view('stock-transfers.index');
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        return view('stock-transfers.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_outlet_id' => 'required|exists:outlets,id',
            'to_outlet_id' => 'required|exists:outlets,id|different:from_outlet_id',
            'transfer_date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $transfer = StockTransfer::create([
                'from_outlet_id' => $request->from_outlet_id,
                'to_outlet_id' => $request->to_outlet_id,
                'transfer_no' => $this->generateTransferNumber(),
                'transfer_date' => $request->transfer_date,
                'status' => 'draft',
                'note' => $request->general_note,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->product_id as $key => $productId) {
                $quantity = (int)$request->quantity[$key];

                StockTransferDetail::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('stock-transfers.show', Crypt::encryptString($transfer->id))
                ->with('success', 'Stock transfer berhasil dibuat. Silakan approve untuk memproses.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $id = Crypt::decryptString($id);
        $transfer = StockTransfer::with(['fromOutlet', 'toOutlet', 'details.product', 'creator', 'approver'])
            ->findOrFail($id);

        return view('stock-transfers.show', compact('transfer'));
    }

    public function edit(string $id)
    {
        $encryptedId = $id;
        $id = Crypt::decryptString($id);
        $transfer = StockTransfer::with('details')->findOrFail($id);

        if ($transfer->status !== 'draft') {
            return redirect()->route('stock-transfers.index')
                ->with('error', 'Transfer yang sudah approved tidak bisa diedit.');
        }

        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        return view('stock-transfers.edit', compact('transfer', 'outlets', 'encryptedId'));
    }

    public function update(Request $request, string $id)
    {
        $id = Crypt::decryptString($id);
        $transfer = StockTransfer::with('details')->findOrFail($id);

        if ($transfer->status !== 'draft') {
            return redirect()->route('stock-transfers.index')
                ->with('error', 'Transfer yang sudah approved tidak bisa diupdate.');
        }

        $request->validate([
            'from_outlet_id' => 'required|exists:outlets,id',
            'to_outlet_id' => 'required|exists:outlets,id|different:from_outlet_id',
            'transfer_date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $transfer->details()->delete();

            foreach ($request->product_id as $key => $productId) {
                $quantity = (int)$request->quantity[$key];

                StockTransferDetail::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            $transfer->update([
                'from_outlet_id' => $request->from_outlet_id,
                'to_outlet_id' => $request->to_outlet_id,
                'transfer_date' => $request->transfer_date,
                'note' => $request->general_note,
            ]);

            DB::commit();

            return redirect()
                ->route('stock-transfers.show', Crypt::encryptString($transfer->id))
                ->with('success', 'Stock transfer berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $id = Crypt::decryptString($id);
        $transfer = StockTransfer::findOrFail($id);

        if ($transfer->status !== 'draft') {
            return response()->json([
                'status' => false,
                'message' => 'Transfer yang sudah approved tidak bisa dihapus.',
            ]);
        }

        $transfer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Stock transfer berhasil dihapus.',
        ]);
    }

    public function approve(string $id)
    {
        $id = Crypt::decryptString($id);
        $transfer = StockTransfer::with('details.product')->findOrFail($id);

        if ($transfer->status !== 'draft') {
            return back()->with('error', 'Transfer sudah diapprove.');
        }

        try {
            DB::beginTransaction();

            $inventoryService = app(InventoryService::class);

            foreach ($transfer->details as $detail) {
                // Reduce stock from source outlet
                $inventoryService->transfer([
                    'outlet_id' => $transfer->from_outlet_id,
                    'product_id' => $detail->product_id,
                    'movement_date' => $transfer->transfer_date,
                    'quantity' => -$detail->quantity,
                    'reference_no' => $transfer->transfer_no,
                    'source_type' => 'STOCK_TRANSFER_OUT',
                    'source_id' => $transfer->id,
                    'note' => 'Stock transfer out - ' . $transfer->transfer_no,
                ]);
            }

            $transfer->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock transfer berhasil diapprove. Menunggu penerimaan di outlet tujuan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Stock Transfer Approve Error', [
                'transfer_id' => $transfer->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', $e->getMessage());
        }
    }

    public function receive(string $id)
    {
        $id = Crypt::decryptString($id);
        $transfer = StockTransfer::with('details.product')->findOrFail($id);

        if ($transfer->status !== 'approved') {
            return back()->with('error', 'Hanya transfer yang sudah approved bisa diterima.');
        }

        try {
            DB::beginTransaction();

            $inventoryService = app(InventoryService::class);

            foreach ($transfer->details as $detail) {
                // Add stock to destination outlet
                $inventoryService->transfer([
                    'outlet_id' => $transfer->to_outlet_id,
                    'product_id' => $detail->product_id,
                    'movement_date' => now()->toDateString(),
                    'quantity' => $detail->quantity,
                    'reference_no' => $transfer->transfer_no,
                    'source_type' => 'STOCK_TRANSFER_IN',
                    'source_id' => $transfer->id,
                    'note' => 'Stock transfer in - ' . $transfer->transfer_no,
                ]);

                $detail->update(['quantity_received' => $detail->quantity]);
            }

            $transfer->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock transfer berhasil diterima.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Stock Transfer Receive Error', [
                'transfer_id' => $transfer->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', $e->getMessage());
        }
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('search');
        $outletId = $request->get('outlet_id');

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

        return response()->json(
            $products->map(function ($product) use ($outletId) {
                return [
                    'id' => $product->id,
                    'text' => ($product->sku ? $product->sku . ' - ' : '') . $product->name,
                    'current_stock' => $product->getStockForOutlet($outletId),
                ];
            })
        );
    }

    private function generateTransferNumber()
    {
        $prefix = 'TRF-' . date('Ymd') . '-';
        $last = StockTransfer::whereDate('created_at', now())
            ->where('transfer_no', 'like', $prefix . '%')
            ->latest()
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->transfer_no, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
