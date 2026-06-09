<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockTransferController;
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

    Route::get('goods-receipts/datatable', [GoodsReceiptController::class, 'datatable'])->name('goods-receipts.datatable');
    Route::get('goods-receipts/search-po', [GoodsReceiptController::class, 'searchPurchaseOrder'])->name('goods-receipts.search-po');
    Route::get('goods-receipts/po-details/{id}', [GoodsReceiptController::class, 'getPurchaseOrderDetails'])->name('goods-receipts.po-details');
    Route::resource('goods-receipts', GoodsReceiptController::class);

    Route::get('stock-adjustments/datatable', [StockAdjustmentController::class, 'datatable'])->name('stock-adjustments.datatable');
    Route::get('stock-adjustments/product-search', [StockAdjustmentController::class, 'searchProduct'])->name('stock-adjustments.product-search');
    Route::resource('stock-adjustments', StockAdjustmentController::class);
    Route::post('stock-adjustments/{id}/approve', [StockAdjustmentController::class, 'approve'])->name('stock-adjustments.approve');

    Route::get('stock-transfers/datatable', [StockTransferController::class, 'datatable'])->name('stock-transfers.datatable');
    Route::get('stock-transfers/product-search', [StockTransferController::class, 'searchProduct'])->name('stock-transfers.product-search');
    Route::resource('stock-transfers', StockTransferController::class);
    Route::post('stock-transfers/{id}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
    Route::post('stock-transfers/{id}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');

    Route::get('stock-opnames/datatable', [StockOpnameController::class, 'datatable'])->name('stock-opnames.datatable');
    Route::get('stock-opnames/get-products', [StockOpnameController::class, 'getProducts'])->name('stock-opnames.get-products');
    Route::resource('stock-opnames', StockOpnameController::class);
    Route::post('stock-opnames/{id}/approve', [StockOpnameController::class, 'approve'])->name('stock-opnames.approve');



    Route::resource('sales', SalesController::class);
    Route::resource('report', ReportController::class);
    Route::resource('setting', SettingController::class);
});

require __DIR__ . '/auth.php';
