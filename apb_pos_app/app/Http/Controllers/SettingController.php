<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $store = $this->getCurrentStore();

        return view('settings.store', compact('store'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $store = Store::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stores', 'code')->ignore($store->id),
            ],
            'owner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
        ]);

        $store->update([
            'name' => $request->name,
            'code' => $request->code,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'tax_rate' => $request->tax_rate,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('setting.index')->with('success', 'Store setting berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getCurrentStore(): Store
    {
        $user = Auth::user();

        if ($user?->store_id) {
            return Store::findOrFail($user->store_id);
        }

        if ($user?->outlet?->store_id) {
            return Store::findOrFail($user->outlet->store_id);
        }

        return Store::firstOrCreate(
            ['code' => 'APB'],
            [
                'name' => config('app.name', 'APB POS'),
                'owner_name' => null,
                'phone' => null,
                'address' => null,
                'tax_rate' => 0,
                'is_active' => 1,
            ]
        );
    }
}
