<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    function datatable(Request $request)
    {
        $data = StockOpname::with(['outlet', 'creator', 'approver'])
            ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('opname_date', function ($row) {
                return date('d/m/Y', strtotime($row->opname_date));
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->outlet->name ?? '-';
            })
            ->addColumn('type_badge', function ($row) {
                return match ($row->type) {
                    'full' => '<span class="badge bg-primary">Full</span>',
                    'partial' => '<span class="badge bg-info">Partial</span>',
                    default => '-',
                };
            })
            ->addColumn('total_items', function ($row) {
                return $row->details->count() . ' item';
            })
            ->addColumn('total_difference', function ($row) {
                $diff = $row->details->sum('difference');
                if ($diff > 0) {
                    return '<span class="text-success">+' . number_format($diff, 0, ',', '.') . '</span>';
                } elseif ($diff < 0) {
                    return '<span class="text-danger">' . number_format($diff, 0, ',', '.') . '</span>';
                }
                return '<span class="text-muted">0</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return match ($row->status) {
                    'draft' => '<span class="badge bg-secondary">Draft</span>',
                    'in_progress' => '<span class="badge bg-warning">In Progress</span>',
                    'review' => '<span class="badge bg-info">Review</span>',
                    'approved' => '<span class="badge bg-success">Approved</span>',
                    'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                    default => '-',
                };
            })
            ->addColumn('action', function ($row) {
                $id = Crypt::encryptString($row->id);
                $url = route('stock-opnames.destroy', $id);
                $module = 'stock opname';

                $btn = '
                    <div class="d-flex justify-content-center gap-1">
                        <a href="' . route('stock-opnames.show', $id) . '" class="btn btn-sm btn-info">
                            <i class="ri-eye-line"></i>
                        </a>
                ';

                if (in_array($row->status, ['draft', 'in_progress'])) {
                    $btn .= '
                        <a href="' . route('stock-opnames.edit', $id) . '" class="btn btn-sm btn-warning">
                            <i class="ri-edit-line"></i>
                        </a>
                    ';
                }

                if ($row->status === 'draft') {
                    $btn .= '
                        <button type="button" class="btn btn-sm btn-danger btn-delete" onclick="deleteData(\'' . $url . '\', \'' . $module . '\')">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    ';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['type_badge', 'total_difference', 'status_badge', 'action'])
            ->make(true);
    }

    public function index()
    {
        return view('stock-opnames.index');
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        return view('stock-opnames.create', compact('outlets'));
    }

    public function getProducts(Request $request)
    {
        $products = Product::where('outlet_id', $request->outlet_id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'stock']);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'opname_date' => 'required|date',
            'type' => 'required|in:partial,full',
            'pic_name' => 'required',
            'product_id' => 'required|array',
            'qty_counted' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $opname = StockOpname::create([
                'outlet_id' => $request->outlet_id,
                'opname_no' => $this->generateOpnameNumber(),
                'opname_date' => $request->opname_date,
                'status' => 'draft',
                'type' => $request->type,
                'pic_name' => $request->pic_name,
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->product_id as $index => $productId) {
                $product = Product::find($productId);
                if (!$product) continue;

                $qtyCounted = $this->clearNumber($request->qty_counted[$index]);
                $qtySystem = $product->stock;
                $difference = $qtyCounted - $qtySystem;

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $productId,
                    'qty_system' => $qtySystem,
                    'qty_counted' => $qtyCounted,
                    'difference' => $difference,
                    'note' => $request->detail_note[$index] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('stock-opnames.show', Crypt::encryptString($opname->id))
                ->with('success', 'Stock opname berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $id = Crypt::decryptString($id);
        $opname = StockOpname::with(['outlet', 'details.product', 'creator', 'reviewer', 'approver', 'stockAdjustment'])
            ->findOrFail($id);

        return view('stock-opnames.show', compact('opname'));
    }

    public function edit(string $id)
    {
        $encryptedId = $id;
        $id = Crypt::decryptString($id);
        $opname = StockOpname::with('details.product')->findOrFail($id);

        if (!in_array($opname->status, ['draft', 'in_progress'])) {
            return redirect()->route('stock-opnames.show', $encryptedId)
                ->with('error', 'Stock opname sudah tidak bisa diedit.');
        }

        $outlets = Outlet::where('is_active', 1)->orderBy('name')->get();
        return view('stock-opnames.edit', compact('opname', 'encryptedId', 'outlets'));
    }

    public function update(Request $request, string $id)
    {
        $id = Crypt::decryptString($id);
        $opname = StockOpname::with('details')->findOrFail($id);

        if (!in_array($opname->status, ['draft', 'in_progress'])) {
            return redirect()->route('stock-opnames.index')
                ->with('error', 'Stock opname sudah tidak bisa diupdate.');
        }

        $request->validate([
            'opname_date' => 'required|date',
            'pic_name' => 'required',
            'product_id' => 'required|array',
            'qty_counted' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $opname->details()->delete();

            foreach ($request->product_id as $index => $productId) {
                $product = Product::find($productId);
                if (!$product) continue;

                $qtyCounted = $this->clearNumber($request->qty_counted[$index]);
                $qtySystem = $product->stock;
                $difference = $qtyCounted - $qtySystem;

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $productId,
                    'qty_system' => $qtySystem,
                    'qty_counted' => $qtyCounted,
                    'difference' => $difference,
                    'note' => $request->detail_note[$index] ?? null,
                ]);
            }

            $opname->update([
                'opname_date' => $request->opname_date,
                'pic_name' => $request->pic_name,
                'note' => $request->note,
            ]);

            DB::commit();

            return redirect()
                ->route('stock-opnames.show', Crypt::encryptString($opname->id))
                ->with('success', 'Stock opname berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $id = Crypt::decryptString($id);
        $opname = StockOpname::findOrFail($id);

        if ($opname->status !== 'draft') {
            return response()->json([
                'status' => false,
                'message' => 'Stock opname yang sudah diproses tidak bisa dihapus.',
            ]);
        }

        $opname->delete();

        return response()->json([
            'status' => true,
            'message' => 'Stock opname berhasil dihapus.',
        ]);
    }

    public function approve(string $id)
    {
        $id = Crypt::decryptString($id);
        $opname = StockOpname::with('details.product')->findOrFail($id);

        if ($opname->status === 'approved') {
            return back()->with('error', 'Stock opname sudah diapprove.');
        }

        DB::beginTransaction();

        try {
            // Generate stock adjustment dari opname
            $adjustment = StockAdjustment::create([
                'outlet_id' => $opname->outlet_id,
                'adjustment_no' => $this->generateAdjustmentNumber(),
                'adjustment_date' => $opname->opname_date,
                'status' => 'approved',
                'note' => 'Auto-generated from Stock Opname ' . $opname->opname_no,
                'created_by' => $opname->created_by,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $inventoryService = app(InventoryService::class);

            foreach ($opname->details as $detail) {
                if ($detail->difference == 0) {
                    continue;
                }

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $detail->product_id,
                    'qty_system' => $detail->qty_system,
                    'qty_physical' => $detail->qty_counted,
                    'difference' => $detail->difference,
                    'note' => $detail->note ?? 'From opname ' . $opname->opname_no,
                ]);

                $inventoryService->adjustment([
                    'outlet_id' => $opname->outlet_id,
                    'product_id' => $detail->product_id,
                    'movement_date' => $opname->opname_date,
                    'new_stock' => $detail->qty_counted,
                    'reference_no' => $adjustment->adjustment_no,
                    'source_type' => 'STOCK_OPNAME',
                    'source_id' => $opname->id,
                    'note' => 'Stock opname adjustment - ' . $opname->opname_no,
                ]);
            }

            $opname->update([
                'status' => 'approved',
                'stock_adjustment_id' => $adjustment->id,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock opname berhasil diapprove dan stock sudah disesuaikan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function getProductsForOpname(Request $request)
    {
        $query = Product::where('outlet_id', $request->outlet_id)
            ->where('is_active', 1);

        if ($request->type === 'full') {
            return $query->orderBy('name')->get();
        }

        if ($request->type === 'partial') {
            if ($request->has('product_ids') && !empty($request->product_ids)) {
                return $query->whereIn('id', $request->product_ids)->orderBy('name')->get();
            }

            if ($request->has('category_ids') && !empty($request->category_ids)) {
                return $query->whereIn('product_category_id', $request->category_ids)->orderBy('name')->get();
            }
        }

        return collect();
    }

    private function clearNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        return (int) str_replace(['.', ','], '', $value);
    }

    private function generateOpnameNumber()
    {
        $prefix = 'SO-' . date('Ymd') . '-';
        $last = StockOpname::whereDate('created_at', now())
            ->where('opname_no', 'like', $prefix . '%')
            ->latest()
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->opname_no, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
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
