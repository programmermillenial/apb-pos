# APB POS - Technology Stack

## Backend

| Technology | Version | Role |
|---|---|---|
| PHP | ^8.3 | Runtime |
| Laravel | ^13.8 | Framework |
| Laravel Breeze | ^2.4 | Auth scaffolding |
| Yajra DataTables | ^13.1 | Server-side DataTables |
| barryvdh/laravel-dompdf | ^3.1 | PDF generation |
| maatwebsite/excel | ^3.1 | Excel export |
| barryvdh/laravel-debugbar | ^4.2 | Dev debugging |
| Laravel Pint | ^1.27 | Code style fixer |
| PHPUnit | ^12.5 | Testing |

## Frontend

| Technology | Version | Role |
|---|---|---|
| Vite | ^8.0.0 | Asset bundler |
| Alpine.js | ^3.4.2 | Lightweight reactivity |
| Tailwind CSS | ^3.1.0 | Utility CSS (Breeze auth pages) |
| Hope UI | — | Main UI theme (Bootstrap 5-based) |
| Bootstrap 5 | (via Hope UI) | Component framework |
| ApexCharts | (bundled) | Dashboard charts |
| Select2 | (bundled) | Enhanced dropdowns / AJAX search |
| SweetAlert2 | (bundled) | Confirmation dialogs |
| AutoNumeric | (bundled) | Numeric input formatting |
| FullCalendar | (vendor) | Calendar plugin |
| Remix Icons | (vendor) | Icon set |
| Flatpickr | (vendor) | Date picker |

## Database

| Item | Detail |
|---|---|
| Primary DB | MySQL (via Laragon local dev) |
| Dev DB | SQLite (`database/database.sqlite`) |
| ORM | Eloquent |

## Development Commands

```bash
# Full setup from scratch
composer run setup

# Start all dev processes (server + queue + pail + vite)
composer run dev

# Run tests
composer run test

# Build frontend assets
npm run build

# Dev frontend (HMR)
npm run dev

# Code style (Pint)
./vendor/bin/pint

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

## Key Config Notes
- Entry point: `apb-pos/index.php` (root `.htaccess` rewrites to `apb_pos_app/public`)
- Static assets (`/assets/`) served from `apb-pos/assets/` (outside Laravel public)
- Vite processes only `resources/css/app.css` and `resources/js/app.js`
- Auth uses Laravel Breeze (session-based, Blade views in `resources/views/auth/`)
- Queue: configured but uses sync driver in dev (via `composer run dev` queue:listen)
