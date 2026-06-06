<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    function datatable(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('outlet')
                ->select('users.*')
                ->latest();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('outlet_name', function ($row) {
                    return $row->outlet->name ?? '-';
                })
                ->addColumn('role_badge', function ($row) {
                    $badge = match ($row->role) {
                        'superadmin' => 'primary',
                        'manager' => 'success',
                        'cashier' => 'warning',
                        default => 'secondary',
                    };

                    return '<span class="badge bg-' . $badge . '">' . ucfirst($row->role) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->is_active) {
                        return '<span class="badge bg-success">Aktif</span>';
                    }

                    return '<span class="badge bg-danger">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = Crypt::encryptString($row->id);
                    $url = route('users.destroy', $id);
                    $module = 'user';

                    return '
                        <div class="text-center">
                            <a href="' . route('users.edit', $id) . '" class="btn btn-sm btn-warning" data-loading="true">
                                <i class="ri-edit-line"></i>
                            </a>

                            <button type="button" class="btn btn-danger btn-sm btn-delete" onclick="deleteData(\'' . $url . '\', \'' . $module . '\')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['role_badge', 'status_badge', 'action'])
                ->make(true);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $roles = $this->roles();

        return view('users.create', compact('outlets', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => ['required', 'exists:outlets,id'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'outlet_id.required' => 'Outlet wajib dipilih.',
            'outlet_id.exists' => 'Outlet tidak valid.',
            'name.required' => 'Nama user wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
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
        $userId = Crypt::decryptString($id);
        $user = User::findOrFail($userId);

        $outlets = Outlet::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $roles = $this->roles();

        return view('users.edit', compact('user', 'outlets', 'roles', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $userId = Crypt::decryptString($id);

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'outlet_id' => ['required', 'exists:outlets,id'],
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'outlet_id.required' => 'Outlet wajib dipilih.',
            'outlet_id.exists' => 'Outlet tidak valid.',
            'name.required' => 'Nama user wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userId = Crypt::decryptString($id);

            $user = User::findOrFail($userId);

            if (Auth::id() === $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'User yang sedang login tidak boleh dihapus.',
                ], 422);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function roles(): array
    {
        return [
            'superadmin' => 'Superadmin',
            'manager' => 'Manager',
            'cashier' => 'Cashier',
            'logistic' => 'Logistic',
            'picker' => 'Picker',
            'checker' => 'Checker',
        ];
    }
}
