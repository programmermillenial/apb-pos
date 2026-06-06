<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::query()->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);
                    $url = route('suppliers.destroy', $id);
                    $module = 'supplier';

                    return '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('suppliers.edit', $id) . '" class="btn btn-sm btn-warning" data-loading="true">
                                <i class="ri-edit-line"></i>
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
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        $request->merge([
            'code' => $this->generateCode()
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
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
        $supplierId = Crypt::decryptString($id);
        $supplier = Supplier::findOrFail($supplierId);

        return view('suppliers.edit', compact('supplier', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplierId = Crypt::decryptString($id);
        $supplier = Supplier::findOrFail($supplierId);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplierId = Crypt::decryptString($id);
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier berhasil dihapus.',
        ]);
    }

    private function generateCode()
    {
        $last = Supplier::latest('id')->first();
        $number = $last ? $last->id + 1 : 1;

        return 'SUPP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
