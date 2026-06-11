<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class OutletController extends Controller
{
    public function datatable()
    {
        $query = Outlet::query()->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {
                $id = Crypt::encryptString($row->id);
                $url = route('outlets.destroy', $id);
                $module = 'outlet';

                return '
                    <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('outlets.edit', $id) . '" class="btn btn-sm btn-warning" data-loading="true">
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

    public function index()
    {
        return view('outlets.index');
    }

    public function create()
    {
        return view('outlets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:outlets,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'required|boolean',
        ]);

        Outlet::create([
            'store_id' => 1,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('outlets.index')
            ->with('success', 'Outlet berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $outletId = Crypt::decryptString($id);

        $outlet = Outlet::findOrFail($outletId);

        return view('outlets.edit', compact('outlet', 'id'));
    }

    public function update(Request $request, string $id)
    {
        $id = Crypt::decryptString($id);

        $outlet = Outlet::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:outlets,code,' . $outlet->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'required|boolean',
        ]);

        $outlet->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('outlets.index')
            ->with('success', 'Outlet berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $id = Crypt::decryptString($id);

        $outlet = Outlet::findOrFail($id);
        $outlet->delete();

        return response()->json([
            'status' => true,
            'message' => 'Outlet berhasil dihapus.',
        ]);
    }
}
