<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::with('outlet')->latest();

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('outlet', fn($row) => $row->outlet->name ?? '-')
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-danger">Nonaktif</span>';
                })
                ->editColumn('total_spent', fn($row) => 'Rp ' . number_format($row->total_spent, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);
                    $url = route('customers.destroy', $id);
                    $module = 'customer';

                    return '
                        <div class="d-flex justify-content-center gap-1">
                            <a href="' . route('customers.edit', $id) . '" class="btn btn-sm btn-warning btn-edit" data-loading="true">
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::where('is_active', 1)->get();
        return view('customers.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:50|unique:customers,phone',
            'email'    => 'nullable|email|max:255|unique:customers,email',
            'address'  => 'nullable|string',
        ]);

        Customer::create([
            'store_id'   => 1,
            'outlet_id'  => $request->outlet_id,
            'code'       => $this->generateCode(),
            'name'       => $request->name,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'address'    => $request->address,
            'is_active'  => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
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
        $customerId = Crypt::decryptString($id);

        $customer = Customer::findOrFail($customerId);
        $outlets = Outlet::where('is_active', 1)->get();

        return view('customers.edit', compact('customer', 'outlets', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customerId = Crypt::decryptString($id);
        $customer = Customer::findOrFail($customerId);

        $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:50|unique:customers,phone,' . $customer->id,
            'email'    => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address'  => 'nullable|string',
        ]);

        $customer->update([
            'outlet_id'  => $request->outlet_id,
            'name'       => $request->name,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'address'    => $request->address,
            'is_active'  => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customerId = Crypt::decryptString($id);
        $customer = Customer::findOrFail($customerId);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus.'
        ]);
    }

    private function generateCode()
    {
        $last = Customer::latest('id')->first();
        $number = $last ? $last->id + 1 : 1;

        return 'CUST-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
