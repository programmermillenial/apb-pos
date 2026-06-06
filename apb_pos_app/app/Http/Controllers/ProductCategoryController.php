<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryController extends Controller
{
    public function datatable()
    {
        $query = ProductCategory::query();
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function ($row) {
                if ($row->is_active) {
                    return '<span class="badge bg-success">Active</span>';
                }
                return '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                $id = Crypt::encryptString($row->id);
                $url = route('product-categories.destroy', $id);
                $module = 'product category';
                return '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('product-categories.edit', $id) . '" class="btn btn-warning btn-sm" data-loading="true">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm btn-delete" onclick="deleteData(\'' . $url . '\', \'' . $module . '\')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('product-categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        ProductCategory::create([
            // 'outlet_id' => auth()->user()->outlet_id ?? 1,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $productCategoryId = Crypt::decryptString($id);
        $productCategory = ProductCategory::findOrFail($productCategoryId);

        return view('product-categories.edit', compact('productCategory', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $productCategoryId = Crypt::decryptString($id);
            $productCategory = ProductCategory::findOrFail($productCategoryId);

            $request->validate([
                'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
                'description' => 'nullable|string',
                'is_active' => 'required|boolean',
            ]);

            $productCategory->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'is_active' => $request->is_active,
            ]);

            return redirect()
                ->route('product-categories.index')
                ->with('success', 'Product category berhasil diperbarui.');
        } catch (DecryptException $e) {
            return redirect()
                ->route('product-categories.index')
                ->with('error', 'Data tidak valid.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $id = Crypt::decryptString($id);
            $productCategory = ProductCategory::findOrFail($id);
            $productCategory->delete();

            return response()->json(['success' => true, 'message' => 'Product category berhasil dihapus.']);
        } catch (DecryptException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
