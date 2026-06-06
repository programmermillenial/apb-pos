<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('product-categories/datatable', [ProductCategoryController::class, 'datatable'])->name('product-categories.datatable');
    Route::resource('product-categories', ProductCategoryController::class);

    Route::get('brands/datatable', [BrandController::class, 'datatable'])->name('brands.datatable');
    Route::resource('brands', BrandController::class);

    Route::get('units/datatable', [UnitController::class, 'datatable'])->name('units.datatable');
    Route::resource('units', UnitController::class);

    Route::get('products/datatable', [ProductController::class, 'datatable'])->name('products.datatable');
    Route::resource('products', ProductController::class);

    Route::get('suppliers/datatable', [SupplierController::class, 'datatable'])->name('suppliers.datatable');
    Route::resource('suppliers', SupplierController::class);

    Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable');
    Route::resource('customers', CustomerController::class);

    Route::get('outlets/datatable', [OutletController::class, 'datatable'])->name('outlets.datatable');
    Route::resource('outlets', OutletController::class);

    Route::get('users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::resource('users', UserController::class);

    Route::get('purchase-orders/datatable', [PurchaseOrderController::class, 'datatable'])->name('purchase-orders.datatable');
    Route::get('purchase-orders/product-search', [PurchaseOrderController::class, 'productSearch'])->name('purchase-orders.product-search');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::post('purchase-orders/{id}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit');
    Route::post('purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{id}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');



    Route::resource('sales', SalesController::class);
    Route::resource('report', ReportController::class);
    Route::resource('setting', SettingController::class);
});

require __DIR__ . '/auth.php';
