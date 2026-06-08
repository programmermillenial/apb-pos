# APB POS - Project Structure

## Repository Layout

```
apb-pos/
├── apb_pos_app/          # Laravel application root
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # Resource + custom controllers
│   │   │   └── Requests/      # Form request validation (if any)
│   │   ├── Models/            # Eloquent models
│   │   ├── Services/          # Business logic services (InventoryService)
│   │   ├── Providers/
│   │   └── View/Components/
│   ├── database/
│   │   ├── migrations/        # Timestamped schema migrations
│   │   └── seeders/           # Data seeders
│   ├── resources/views/       # Blade templates per module
│   ├── routes/web.php         # All web routes
│   ├── config/
│   └── public/
├── assets/                    # Static frontend assets (served from root)
│   ├── css/                   # Hope UI theme CSS + custom styles
│   ├── js/
│   │   ├── charts/            # ApexCharts dashboard scripts
│   │   ├── plugins/           # Calendar, flatpickr, etc.
│   │   └── custom/            # App-specific JS
│   └── vendor/                # Third-party JS libs (FullCalendar, Leaflet, etc.)
├── images/ & uploads/         # Public image/upload directories
└── index.php / .htaccess      # Web root entry point pointing into apb_pos_app/public
```

## Core Modules & Relationships

### Models
| Model | Key Relations |
|---|---|
| Product | belongsTo Category, Brand, Unit, Outlet; hasMany StockMovement |
| PurchaseOrder | belongsTo Supplier, Outlet; hasMany PurchaseOrderDetail; belongsTo creator/approver (User) |
| PurchaseOrderDetail | belongsTo PurchaseOrder, Product; hasMany GoodsReceiptDetail |
| GoodsReceipt | belongsTo PurchaseOrder; hasMany GoodsReceiptDetail |
| GoodsReceiptDetail | belongsTo GoodsReceipt, PurchaseOrderDetail, Product |
| StockMovement | belongsTo Product, Outlet, User |
| Store | top-level entity (store_id=1 hardcoded for now) |
| Outlet | belongsTo Store |

### Controllers (Resource Pattern)
Each module follows: `datatable` → `index` → `create` → `store` → `show` → `edit` → `update` → `destroy`

Extra routes are registered before the resource route for specificity:
```php
Route::get('purchase-orders/datatable', [...]);
Route::get('purchase-orders/product-search', [...]);
Route::resource('purchase-orders', PurchaseOrderController::class);
Route::post('purchase-orders/{id}/submit', [...]);
```

### Service Layer
- `InventoryService` — single service class with `stockIn()`, `stockOut()`, `adjustment()` methods
- Called from controllers via `app(InventoryService::class)`
- Each method wraps operations in `DB::transaction()`

## Architectural Patterns

- **MVC**: Standard Laravel MVC; no API layer (server-rendered Blade views)
- **DataTables**: Server-side processing via `yajra/laravel-datatables-oracle`; each controller has a `datatable()` method
- **ID Encryption**: All public-facing IDs encrypted with `Crypt::encryptString()` / `Crypt::decryptString()`
- **Workflow States**: PO status machine: `draft → submitted → approved → partial_received → received / cancelled`
- **Pessimistic Locking**: `lockForUpdate()` used in GoodsReceipt store to prevent race conditions
- **Auto-numbering**: Sequential document numbers generated per-day (e.g., `PO-20260605-0001`, `GR-20260606-0001`)
- **Frontend Theme**: Hope UI (Bootstrap 5-based) with Remix Icons
