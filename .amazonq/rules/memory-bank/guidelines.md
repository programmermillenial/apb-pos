# APB POS - Development Guidelines

## PHP / Laravel Conventions

### Controller Structure
Every module controller follows this exact method order:
1. `datatable(Request $request)` — server-side DataTables endpoint (no `public` visibility shorthand used for this in some controllers)
2. `index()` — returns list view
3. `create()` — returns create form view
4. `store(Request $request)` — validates, DB::beginTransaction, try/catch, DB::commit/rollBack
5. `show(string $id)` — decrypts ID, eager loads relations, returns show view
6. `edit(string $id)` — decrypts ID, guards editable states, returns edit view
7. `update(Request $request, string $id)` — same pattern as store
8. `destroy(string $id)` — returns `response()->json(['status' => bool, 'message' => '...'])`
9. Private helpers at the bottom: `generateNumber()`, `clearNumber()`, status updaters

### ID Encryption Pattern (used universally)
All public-facing IDs must be encrypted before passing to routes/views:
```php
// In datatable column:
$id = Crypt::encryptString($row->id);
return route('module.show', $id);

// In controller method:
$id = Crypt::decryptString($id);
$model = Model::findOrFail($id);
```

### DB Transaction Pattern (all write operations)
```php
DB::beginTransaction();
try {
    // operations...
    DB::commit();
    return redirect()->route('module.index')->with('success', 'Pesan sukses.');
} catch (\Throwable $e) {
    DB::rollBack();
    return back()->withInput()->with('error', $e->getMessage());
}
```

### Pessimistic Locking (concurrent write protection)
Use `lockForUpdate()` when reading records that will be written to in the same transaction:
```php
$purchaseOrder = PurchaseOrder::lockForUpdate()->findOrFail($id);
```

### Validation Pattern
Always validate at the top of store/update before opening a transaction:
```php
$request->validate([
    'field' => 'required|exists:table,column',
    'array_field' => 'required|array',
]);
```

### Model Convention
- All models use `protected $guarded = ['id']` (not `$fillable`)
- Relationships use explicit foreign keys only when non-conventional
- `$casts` used on money/date fields (e.g., `'cost_price' => 'decimal:2'`)
- No soft deletes observed — hard delete is the default

### Status Guard Pattern
Status checks before allowing any state-changing operation:
```php
if ($model->status !== 'draft') {
    return back()->with('error', 'Tidak bisa diproses.');
}
// or for multiple valid statuses:
if (!in_array($model->status, ['approved', 'partial_received'])) { ... }
```

### Auto-Number Generation Pattern
Day-based sequential document numbers:
```php
private function generateNumber(): string
{
    $prefix = 'GR-' . date('Ymd') . '-';
    $last = GoodsReceipt::whereDate('created_at', now())
        ->where('gr_number', 'like', $prefix . '%')
        ->latest()->first();
    $lastNumber = $last ? (int) substr($last->gr_number, -4) : 0;
    return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
}
```

### Service Layer Usage
Inject services via `app()` helper inside controller methods (not constructor injection):
```php
$inventoryService = app(InventoryService::class);
$inventoryService->stockIn([...]);
```

InventoryService methods always use `DB::transaction()` internally — do NOT wrap them in another `DB::beginTransaction()` at the same nesting level unless you want a savepoint.

### AJAX / JSON Response Convention
```php
// Success
return response()->json(['status' => true, 'message' => 'Berhasil.', 'data' => $data]);

// Error with HTTP status
return response()->json(['status' => false, 'message' => 'Gagal.', 'details' => []], 422);
```

### Number Formatting (Indonesian locale)
- Currency displayed as: `'Rp ' . number_format($value, 0, ',', '.')`
- `clearNumber()` helper strips `.` and `,` from AutoNumeric-formatted strings:
```php
private function clearNumber($value): int
{
    return (int) str_replace(['.', ','], '', $value ?? '');
}
```

---

## Blade / Frontend Conventions

### Layout Inheritance
All views extend `layouts.app` and push scripts to the `scripts` stack:
```blade
@extends('layouts.app')
@section('title', 'Page Title')      {{-- optional --}}
@section('content')
    ...
@endsection
@push('scripts')
    <script>...</script>
@endpush
```

### DataTables Index Page Pattern
Every index view follows this structure:
```blade
<table id="datatable" class="table table-bordered table-striped align-middle w-100">
    <thead>...</thead>
    {{-- No tbody - DataTables fills it --}}
</table>

@push('scripts')
<script>
    table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        language: { search: '', searchPlaceholder: 'Cari data...' },
        ajax: "{{ route('module.datatable') }}",
        columns: [...]
    });
</script>
@endpush
```
Note: `table` is a global variable (declared in layout) to allow `table.ajax.reload()` from the shared `deleteData()` function.

### Form Pages Pattern
- Wrap the entire form in `<form action="..." method="POST">` with `@csrf`
- Use Bootstrap card layout: one card per logical section
- Buttons at bottom: "Kembali" (btn-light) + "Simpan" (btn-primary with `ri-save-line` icon)
- Confirm destructive actions with SweetAlert2 before submitting

### SweetAlert2 Patterns
```javascript
// Confirmation before form submit
$('#form').on('submit', function(e) {
    e.preventDefault();
    Swal.fire({ title: '...', icon: 'question', showCancelButton: true, ... })
        .then((result) => { if (result.isConfirmed) this.submit(); });
});

// Toast notification (used for AJAX delete success)
Swal.fire({ toast: true, position: 'top-end', icon: 'success', timer: 3000, ... });
```

### Select2 AJAX Search Pattern
```javascript
$('#field_id').select2({
    theme: 'bootstrap-5',
    placeholder: 'Cari...',
    allowClear: true,
    ajax: {
        url: "{{ route('module.search') }}",
        dataType: 'json',
        delay: 300,
        data: function(params) { return { search: params.term }; },
        processResults: function(data) { return { results: data }; }
    }
});
```
Backend search endpoints return `[['id' => ..., 'text' => ...], ...]` arrays.

### AutoNumeric Integration
All monetary/quantity inputs get `.autonumeric` class. Global config in layout:
```javascript
let autoNumericOptions = {
    digitGroupSeparator: '.',
    decimalCharacter: ',',
    decimalPlaces: 0,
    minimumValue: '0',
    unformatOnSubmit: true,
};
```
Use `initAutoNumeric()` after dynamically rendering rows. Use `getAutoNumericValue(element)` to read values.

### Delete Pattern (AJAX, no page reload)
```javascript
function deleteData(url, module) {
    Swal.fire({ ... }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url, type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    Swal.fire({ toast: true, ... });
                    table.ajax.reload(null, false);  // preserve pagination
                }
            });
        }
    });
}
```

### Status Badge Convention
```php
// In datatable column, use Bootstrap contextual colors:
'draft'            => 'badge bg-secondary'
'submitted'        => 'badge bg-info'
'approved'         => 'badge bg-success'
'partial_received' => 'badge bg-warning'
'received'         => 'badge bg-primary'
'cancelled'        => 'badge bg-danger'
```

### Remix Icons
Use `ri-*` icon classes from Remix Icons for all UI icons. Common usage:
- `ri-add-line` — add/create buttons
- `ri-eye-line` — view/show buttons
- `ri-edit-line` — edit buttons
- `ri-delete-bin-line` — delete buttons
- `ri-save-line` — save/submit buttons

---

## Route Registration Pattern
Custom sub-routes are always registered BEFORE the resource route:
```php
Route::get('module/datatable', [Controller::class, 'datatable'])->name('module.datatable');
Route::get('module/search', [Controller::class, 'search'])->name('module.search');
Route::resource('module', Controller::class);
Route::post('module/{id}/action', [Controller::class, 'action'])->name('module.action');
```

## Flash Message Convention
- Success: `->with('success', 'Pesan sukses.')` — displayed as green alert
- Error: `->with('error', $e->getMessage())` — displayed as red alert
- Messages are rendered by the shared `layouts.alert` partial
