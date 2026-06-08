<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentController extends Controller
{
    function datatable(Request $request)
    {
        $data = StockAdjustment::with(['outlet', 'creator', 'approver'])
            ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('adjustment_date', function ($row) {
                return date('d/m/Y', strtotime($row->adjustment_date));
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->outlet->name ?? '-';
            })
            ->addColumn('total_items', function ($row) {
                return $row->details->count() . ' item';
            })
            ->addColumn('status_badge', function ($row) {
                return match ($row->status) {
                    'draft' => '<span class="badge bg-secondary">Draft</span>',
                    'approved' => '<span class="badge bg-success">Approved</span>',
                    default => '<span class="badge bg-light text-dark">Unknown</span>',
                };
            })
            ->addColumn('creator_name', function ($row) {
                return $row->creator->name ?? '-';
            })
            ->addColumn('action', function ($row) {
                $id = Crypt::encryptString($row->id);
                $url = route('stock-adjustments.destroy', $id);
                $module = 'stock adjustment';

                $btn = '
                    <div class="d-flex justify-content-center gap-1">
                        <a href="' . route('stock-adjustments.show', $id) . '" class="btn btn-sm btn-info">
                            <i class="ri-eye-line"></i>
                        </a>
                ';

                if ($row->status === 'draft') {
                    $btn .= '
                        <a href="' . route('stock-adjustments.edit', $id) . '" class="btn btn-sm btn-warning">
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
        return view('stock-adjustments.index');
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        return view('stock-adjustments.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'adjustment_date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'new_stock.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $adjustment = StockAdjustment::create([
                'outlet_id' => $request->outlet_id,
                'adjustment_no' => $this->generateAdjustmentNumber(),
                'adjustment_date' => $request->adjustment_date,
                'status' => 'draft',
                'note' => $request->general_note,
                'created_by' => Auth::id(),
            ]);

            $hasItems = false;

            foreach ($request->product_id as $key => $productId) {
                $newStock = $this->clearNumber($request->new_stock[$key]);
                $note = $request->note[$key] ?? null;

                $product = Product::findOrFail($productId);
                $qtySystem = $product->stock;
                $difference = $newStock - $qtySystem;

                if ($difference == 0) {
                    continue;
                }

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $productId,
                    'qty_system' => $qtySystem,
                    'qty_physical' => $newStock,
                    'difference' => $difference,
                    'note' => $note,
                ]);

                $hasItems = true;
            }

            if (!$hasItems) {
                throw new \Exception('Tidak ada produk yang perlu disesuaikan.');
            }

            DB::commit();

            return redirect()
                ->route('stock-adjustments.show', Crypt::encryptString($adjustment->id))
                ->with('success', 'Stock adjustment berhasil dibuat. Silakan approve untuk memproses.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $id = Crypt::decryptString($id);
        $adjustment = StockAdjustment::with(['outlet', 'details.product', 'creator', 'approver'])
            ->findOrFail($id);

        return view('stock-adjustments.show', compact('adjustment'));
    }

    public function edit(string $id)
    {
        $encryptedId = $id;
        $id = Crypt::decryptString($id);
        $adjustment = StockAdjustment::with('details')->findOrFail($id);

        if ($adjustment->status !== 'draft') {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Adjustment yang sudah approved tidak bisa diedit.');
        }

        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        return view('stock-adjustments.edit', compact('adjustment', 'outlets', 'encryptedId'));
    }

    public function update(Request $request, string $id)
    {
        $id = Crypt::decryptString($id);
        $adjustment = StockAdjustment::with('details')->findOrFail($id);

        if ($adjustment->status !== 'draft') {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Adjustment yang sudah approved tidak bisa diupdate.');
        }

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'adjustment_date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'new_stock.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $adjustment->details()->delete();

            $hasItems = false;

            foreach ($request->product_id as $key => $productId) {
                $newStock = $this->clearNumber($request->new_stock[$key]);
                $note = $request->note[$key] ?? null;

                $product = Product::findOrFail($productId);
                $qtySystem = $product->stock;
                $difference = $newStock - $qtySystem;

                if ($difference == 0) {
                    continue;
                }

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $productId,
                    'qty_system' => $qtySystem,
                    'qty_physical' => $newStock,
                    'difference' => $difference,
                    'note' => $note,
                ]);

                $hasItems = true;
            }

            if (!$hasItems) {
                throw new \Exception('Tidak ada produk yang perlu disesuaikan.');
            }

            $adjustment->update([
                'outlet_id' => $request->outlet_id,
                'adjustment_date' => $request->adjustment_date,
                'note' => $request->general_note,
            ]);

            DB::commit();

            return redirect()
                ->route('stock-adjustments.show', Crypt::encryptString($adjustment->id))
                ->with('success', 'Stock adjustment berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $id = Crypt::decryptString($id);
        $adjustment = StockAdjustment::findOrFail($id);

        if ($adjustment->status !== 'draft') {
            return response()->json([
                'status' => false,
                'message' => 'Adjustment yang sudah approved tidak bisa dihapus.',
            ]);
        }

        $adjustment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Stock adjustment berhasil dihapus.',
        ]);
    }

    public function approve(string $id)
    {
        $id = Crypt::decryptString($id);
        $adjustment = StockAdjustment::with('details.product')->findOrFail($id);

        if ($adjustment->status !== 'draft') {
            return back()->with('error', 'Adjustment sudah diapprove.');
        }

        DB::beginTransaction();

        try {
            $inventoryService = app(InventoryService::class);

            foreach ($adjustment->details as $detail) {
                $inventoryService->adjustment([
                    'outlet_id' => $adjustment->outlet_id,
                    'product_id' => $detail->product_id,
                    'movement_date' => $adjustment->adjustment_date,
                    'new_stock' => $detail->qty_physical,
                    'reference_no' => $adjustment->adjustment_no,
                    'source_type' => 'STOCK_ADJUSTMENT',
                    'source_id' => $adjustment->id,
                    'note' => $detail->note ?? 'Stock adjustment - ' . $adjustment->adjustment_no,
                ]);
            }

            $adjustment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock adjustment berhasil diapprove dan stock sudah disesuaikan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('search');
        $outletId = $request->get('outlet_id');

        $products = Product::query()
            ->where('is_active', 1)
            ->when($outletId, function ($query) use ($outletId) {
                $query->where('outlet_id', $outletId);
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

        return response()->json(
            $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => ($product->sku ? $product->sku . ' - ' : '') . $product->name,
                    'current_stock' => $product->stock,
                ];
            })
        );
    }

    private function clearNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        return (int) str_replace(['.', ','], '', $value);
    }

    private function generateAdjustmentNumber()
    {
        $prefix = 'ADJ-' . date('Ymd') . '-';
        $last = StockAdjustment::whereDate('created_at', now())
            ->where('adjustment_no', 'like', $prefix . '%')
            ->latest()
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->adjustment_no, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
