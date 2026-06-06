<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = Unit::query()->latest();

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
                    $url = route('units.destroy', $id);
                    $module = 'unit';

                    return '
                        <div class="text-center">
                            <a href="' . route('units.edit', $id) . '" class="btn btn-sm btn-warning" data-loading="true">
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

    public function index()
    {
        return view('units.index');
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'short_name' => 'required|string|max:50|unique:units,short_name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Unit::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_name' => $request->short_name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $unitId = Crypt::decryptString($id);
        $unit = Unit::findOrFail($unitId);

        return view('units.edit', compact('unit', 'id'));
    }

    public function update(Request $request, string $id)
    {
        $unitId = Crypt::decryptString($id);
        $unit = Unit::findOrFail($unitId);

        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'short_name' => 'required|string|max:50|unique:units,short_name,' . $unit->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $unit->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_name' => $request->short_name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $unitId = Crypt::decryptString($id);
        $unit = Unit::findOrFail($unitId);
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit berhasil dihapus.'
        ]);
    }
}
