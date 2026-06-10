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

    Route::get('product-categories/datatable', [ProductCategoryController::class, 'datatable'])->name('product-categories.datatable')->middleware('role:manager');
    Route::resource('product-categories', ProductCategoryController::class)->middleware('role:manager');

    Route::get('brands/datatable', [BrandController::class, 'datatable'])->name('brands.datatable')->middleware('role:manager');
    Route::resource('brands', BrandController::class)->middleware('role:manager');

    Route::get('units/datatable', [UnitController::class, 'datatable'])->name('units.datatable')->middleware('role:manager');
    Route::resource('units', UnitController::class)->middleware('role:manager');

    Route::get('products/datatable', [ProductController::class, 'datatable'])->name('products.datatable')->middleware('role:manager,logistic');
    Route::resource('products', ProductController::class)->middleware('role:manager,logistic');

    Route::get('suppliers/datatable', [SupplierController::class, 'datatable'])->name('suppliers.datatable')->middleware('role:manager,logistic');
    Route::resource('suppliers', SupplierController::class)->middleware('role:manager,logistic');

    Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable')->middleware('role:manager,cashier');
    Route::resource('customers', CustomerController::class)->middleware('role:manager,cashier');

    Route::get('outlets/datatable', [OutletController::class, 'datatable'])->name('outlets.datatable')->middleware('role:superadmin');
    Route::resource('outlets', OutletController::class)->middleware('role:superadmin');

    Route::get('users/datatable', [UserController::class, 'datatable'])->name('users.datatable')->middleware('role:superadmin');
    Route::resource('users', UserController::class)->middleware('role:superadmin');

    Route::get('purchase-orders/datatable', [PurchaseOrderController::class, 'datatable'])->name('purchase-orders.datatable')->middleware('role:manager,logistic');
    Route::get('purchase-orders/product-search', [PurchaseOrderController::class, 'productSearch'])->name('purchase-orders.product-search')->middleware('role:manager,logistic');
    Route::resource('purchase-orders', PurchaseOrderController::class)->middleware('role:manager,logistic');
    Route::post('purchase-orders/{id}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit')->middleware('role:manager,logistic');
    Route::post('purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve')->middleware('role:manager,logistic');
    Route::post('purchase-orders/{id}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel')->middleware('role:manager,logistic');

    Route::get('goods-receipts/datatable', [GoodsReceiptController::class, 'datatable'])->name('goods-receipts.datatable')->middleware('role:manager,logistic,checker');
    Route::get('goods-receipts/search-po', [GoodsReceiptController::class, 'searchPurchaseOrder'])->name('goods-receipts.search-po')->middleware('role:manager,logistic,checker');
    Route::get('goods-receipts/po-details/{id}', [GoodsReceiptController::class, 'getPurchaseOrderDetails'])->name('goods-receipts.po-details')->middleware('role:manager,logistic,checker');
    Route::resource('goods-receipts', GoodsReceiptController::class)->middleware('role:manager,logistic,checker');

    Route::get('stock-adjustments/datatable', [StockAdjustmentController::class, 'datatable'])->name('stock-adjustments.datatable')->middleware('role:manager,logistic,checker');
    Route::get('stock-adjustments/product-search', [StockAdjustmentController::class, 'searchProduct'])->name('stock-adjustments.product-search')->middleware('role:manager,logistic,checker');
    Route::resource('stock-adjustments', StockAdjustmentController::class)->middleware('role:manager,logistic,checker');
    Route::post('stock-adjustments/{id}/approve', [StockAdjustmentController::class, 'approve'])->name('stock-adjustments.approve')->middleware('role:manager,logistic,checker');

    Route::get('stock-transfers/datatable', [StockTransferController::class, 'datatable'])->name('stock-transfers.datatable')->middleware('role:manager,logistic,picker,checker');
    Route::get('stock-transfers/product-search', [StockTransferController::class, 'searchProduct'])->name('stock-transfers.product-search')->middleware('role:manager,logistic,picker,checker');
    Route::resource('stock-transfers', StockTransferController::class)->middleware('role:manager,logistic,picker,checker');
    Route::post('stock-transfers/{id}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve')->middleware('role:manager,logistic,picker,checker');
    Route::post('stock-transfers/{id}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive')->middleware('role:manager,logistic,picker,checker');

    Route::get('stock-opnames/datatable', [StockOpnameController::class, 'datatable'])->name('stock-opnames.datatable')->middleware('role:manager,logistic,picker,checker');
    Route::get('stock-opnames/get-products', [StockOpnameController::class, 'getProducts'])->name('stock-opnames.get-products')->middleware('role:manager,logistic,picker,checker');
    Route::resource('stock-opnames', StockOpnameController::class)->middleware('role:manager,logistic,picker,checker');
    Route::post('stock-opnames/{id}/approve', [StockOpnameController::class, 'approve'])->name('stock-opnames.approve')->middleware('role:manager,logistic,picker,checker');
    Route::get('sales/datatable', [SalesController::class, 'datatable'])->name('sales.datatable')->middleware('role:manager,cashier');
    Route::get('sales/history', [SalesController::class, 'history'])->name('sales.history')->middleware('role:manager,cashier');
    Route::get('sales/product-search', [SalesController::class, 'productSearch'])->name('sales.product-search')->middleware('role:manager,cashier');
    Route::get('sales/customer-search', [SalesController::class, 'customerSearch'])->name('sales.customer-search')->middleware('role:manager,cashier');
    Route::post('sales/customer-store', [SalesController::class, 'customerStore'])->name('sales.customer-store')->middleware('role:manager,cashier');
    Route::get('sales/{id}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt')->middleware('role:manager,cashier');
    Route::get('sales/{id}/receipt-pdf', [SalesController::class, 'receiptPdf'])->name('sales.receipt-pdf')->middleware('role:manager,cashier');
    Route::resource('sales', SalesController::class)->middleware('role:manager,cashier');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales')->middleware('role:manager');
    Route::get('reports/sales/csv', [ReportController::class, 'salesCsv'])->name('reports.sales.csv')->middleware('role:manager');
    Route::get('reports/profit', [ReportController::class, 'profit'])->name('reports.profit')->middleware('role:manager');
    Route::get('reports/profit/csv', [ReportController::class, 'profitCsv'])->name('reports.profit.csv')->middleware('role:manager');
    Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock')->middleware('role:manager');
    Route::get('reports/stock/csv', [ReportController::class, 'stockCsv'])->name('reports.stock.csv')->middleware('role:manager');
    Route::get('reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement')->middleware('role:manager');
    Route::get('reports/stock-movement/csv', [ReportController::class, 'stockMovementCsv'])->name('reports.stock-movement.csv')->middleware('role:manager');
    Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock')->middleware('role:manager');
    Route::get('reports/low-stock/csv', [ReportController::class, 'lowStockCsv'])->name('reports.low-stock.csv')->middleware('role:manager');
    Route::get('reports/slow-moving', [ReportController::class, 'slowMoving'])->name('reports.slow-moving')->middleware('role:manager');
    Route::get('reports/slow-moving/csv', [ReportController::class, 'slowMovingCsv'])->name('reports.slow-moving.csv')->middleware('role:manager');
    Route::get('reports/stock-aging', [ReportController::class, 'stockAging'])->name('reports.stock-aging')->middleware('role:manager');
    Route::get('reports/stock-aging/csv', [ReportController::class, 'stockAgingCsv'])->name('reports.stock-aging.csv')->middleware('role:manager');
    Route::get('reports/stock-valuation', [ReportController::class, 'stockValuation'])->name('reports.stock-valuation')->middleware('role:manager');
    Route::get('reports/stock-valuation/csv', [ReportController::class, 'stockValuationCsv'])->name('reports.stock-valuation.csv')->middleware('role:manager');
    Route::get('reports/stock-discrepancy', [ReportController::class, 'stockDiscrepancy'])->name('reports.stock-discrepancy')->middleware('role:manager');
    Route::get('reports/stock-discrepancy/csv', [ReportController::class, 'stockDiscrepancyCsv'])->name('reports.stock-discrepancy.csv')->middleware('role:manager');
    Route::get('reports/transfer-pending', [ReportController::class, 'transferPending'])->name('reports.transfer-pending')->middleware('role:manager');
    Route::get('reports/transfer-pending/csv', [ReportController::class, 'transferPendingCsv'])->name('reports.transfer-pending.csv')->middleware('role:manager');
    Route::get('reports/fast-moving', [ReportController::class, 'fastMoving'])->name('reports.fast-moving')->middleware('role:manager');
    Route::get('reports/fast-moving/csv', [ReportController::class, 'fastMovingCsv'])->name('reports.fast-moving.csv')->middleware('role:manager');
    Route::get('reports/stock-card', [ReportController::class, 'stockCard'])->name('reports.stock-card')->middleware('role:manager');
    Route::resource('report', ReportController::class)->middleware('role:manager');
    Route::resource('setting', SettingController::class)->middleware('role:superadmin');
});

require __DIR__ . '/auth.php';
