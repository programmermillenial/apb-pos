<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    function datatable(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['outlet', 'category', 'brand', 'unit'])->orderByDesc('created_at');

            return DataTables::of($products)
                ->addColumn('outlet', fn($row) => $row->outlet->code ?? '-')
                ->addColumn('category', fn($row) => $row->category->name ?? '-')
                ->addColumn('brand', fn($row) => $row->brand->name ?? '-')
                ->addColumn('unit', fn($row) => $row->unit->name ?? '-')
                ->addColumn('sell_price', fn($row) => 'Rp ' . number_format($row->sell_price, 0, ',', '.'))
                ->addColumn('status', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);
                    $url = route('products.destroy', $id);
                    $module = 'product';

                    return '
                        <div class="d-flex justify-content-center gap-2">
                            <a href="' . route('products.show', $id) . '" class="btn btn-sm btn-info" data-loading="true">
                                <i class="ri-eye-line"></i>
                            </a>

                            <a href="' . route('products.edit', $id) . '" class="btn btn-sm btn-warning" data-loading="true">
                                <i class="ri-pencil-line"></i>
                            </a>

                            <button type="button" class="btn btn-sm btn-danger btn-delete" onclick="deleteData(\'' . $url . '\', \'' . $module . '\')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.create', compact('outlets', 'categories', 'brands', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'product_category_id' => 'required|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'sku' => 'required|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
            'weight' => 'nullable|numeric|min:0',
        ]);

        Product::create([
            'outlet_id' => $request->outlet_id,
            'product_category_id' => $request->product_category_id,
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'sku' => $request->sku,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'cost_price' => $request->cost_price,
            'sell_price' => $request->sell_price,
            'stock' => $request->stock,
            'weight' => $request->weight ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('products.index')->with('success', 'Product berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productId = Crypt::decryptString($id);
        $product = Product::with(['outlet', 'category', 'brand', 'unit'])->findOrFail($productId);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $productId = Crypt::decryptString($id);

        $product = Product::findOrFail($productId);

        $outlets = Outlet::orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.edit', compact('product', 'outlets', 'categories', 'brands', 'units', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $productId = Crypt::decryptString($id);
        $product = Product::findOrFail($productId);

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'product_category_id' => 'required|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
            'weight' => 'nullable|numeric|min:0',
        ]);

        $product->update([
            'outlet_id' => $request->outlet_id,
            'product_category_id' => $request->product_category_id,
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'sku' => $request->sku,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'cost_price' => $request->cost_price,
            'sell_price' => $request->sell_price,
            'stock' => $request->stock,
            'weight' => $request->weight ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('products.index')->with('success', 'Product berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productId = Crypt::decryptString($id);

        $product = Product::findOrFail($productId);
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product berhasil dihapus.'
        ]);
    }
}
