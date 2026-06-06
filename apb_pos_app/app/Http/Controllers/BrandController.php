<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = Brand::query()->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn(
                    'is_active',
                    fn($row) =>
                    $row->is_active
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-danger">Nonaktif</span>'
                )
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);
                    $url = route('brands.destroy', $id);
                    $module = 'brand';

                    return '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('brands.edit', $id) . '" class="btn btn-sm btn-warning" data-loading="true">
                                <i class="ri-edit-line"></i>
                            </a>

                            <button type="button" class="btn btn-sm btn-danger btn-delete" onclick="deleteData(\'' . $url . '\', \'' . $module . '\')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }
    }

    public function index()
    {
        return view('brands.index');
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $brandId = Crypt::decryptString($id);
        $brand = Brand::findOrFail($brandId);

        return view('brands.edit', compact('brand', 'id'));
    }

    public function update(Request $request, string $id)
    {
        $brandId = Crypt::decryptString($id);
        $brand = Brand::findOrFail($brandId);

        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $brandId = Crypt::decryptString($id);
        $brand = Brand::findOrFail($brandId);
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand berhasil dihapus.'
        ]);
    }
}
