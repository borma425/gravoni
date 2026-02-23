<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// Public Store Routes
Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::get('/product/{product}', [StoreController::class, 'show'])->name('store.product');

// Public routes - Admin login
Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Protected routes - require authentication
Route::prefix('admin')->middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'exportStats'])->name('dashboard.export');

    // Products
    Route::post('products/upload-media', [ProductController::class, 'uploadMedia'])->name('products.upload-media');
    Route::resource('products', ProductController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);

    // Sales
    Route::resource('sales', SaleController::class)->except(['edit', 'update']);

    // Orders
    Route::resource('orders', OrderController::class);

    // Governorates
    Route::resource('governorates', GovernorateController::class);

    // Losses
    Route::resource('losses', \App\Http\Controllers\LossController::class)->only(['index', 'show', 'destroy']);

    // Stock Movements
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/sales-return', [StockMovementController::class, 'showSalesReturnForm'])->name('sales-return');
        Route::post('/sales-return', [StockMovementController::class, 'recordSalesReturn'])->name('sales-return.store');
        Route::get('/purchase-return', [StockMovementController::class, 'showPurchaseReturnForm'])->name('purchase-return');
        Route::post('/purchase-return', [StockMovementController::class, 'recordPurchaseReturn'])->name('purchase-return.store');
        Route::get('/damage', [StockMovementController::class, 'showDamageForm'])->name('damage');
        Route::post('/damage', [StockMovementController::class, 'recordDamage'])->name('damage.store');
        Route::get('/damages', [StockMovementController::class, 'indexDamages'])->name('damage.index');
        Route::delete('/damages/{movement}', [StockMovementController::class, 'destroyDamage'])->name('damage.destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/users', [SettingsController::class, 'storeUser'])->name('users.store');
        Route::delete('/users/{user}', [SettingsController::class, 'destroyUser'])->name('users.destroy');
    });
});
