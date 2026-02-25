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
use App\Http\Controllers\TransferredChatController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// Public Store Routes
Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::get('/product/{product}', [StoreController::class, 'show'])->name('store.product');
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('store.cart');
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('store.cart.add');
Route::post('/cart/update', [\App\Http\Controllers\CartController::class, 'update'])->name('store.cart.update');
Route::delete('/cart/remove/{key}', [\App\Http\Controllers\CartController::class, 'remove'])->name('store.cart.remove');
Route::get('/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::post('/checkout', [StoreController::class, 'placeOrder'])->name('store.checkout.place');

// CashUp Cash payment verification (before order confirmation)
Route::post('/checkout/cashup/create-payment-intent', [\App\Http\Controllers\CashUpCashController::class, 'createPaymentIntent'])->name('store.checkout.cashup.create-intent');
Route::post('/checkout/cashup/validate-payment', [\App\Http\Controllers\CashUpCashController::class, 'validatePayment'])->name('store.checkout.cashup.validate');
Route::post('/checkout/cashup/upload-transfer-image', [\App\Http\Controllers\CashUpCashController::class, 'uploadTransferImage'])->name('store.checkout.cashup.upload-image');
Route::get('/order/success/{order}', [StoreController::class, 'orderSuccess'])->name('store.order.success');
Route::get('/track/{trackingId}', [StoreController::class, 'track'])->name('store.track');

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
    Route::post('orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    Route::resource('orders', OrderController::class);

    // الدردشة المحولة
    Route::get('/transferred-chat', [TransferredChatController::class, 'index'])->name('transferred-chat.index');
    Route::patch('/transferred-chat/{id}/type', [TransferredChatController::class, 'updateType'])->name('transferred-chat.update-type');

    // Governorates (المسارات المخصصة قبل resource حتى لا تُلتقط كـ show)
    Route::get('/governorates/account-balance', [\App\Http\Controllers\TransferMoneyController::class, 'getAccountBalance'])->name('governorates.account-balance');
    Route::post('/governorates/transfer-balance', [\App\Http\Controllers\TransferMoneyController::class, 'getBalance'])->name('governorates.transfer-balance');
    Route::post('/governorates/transfer-money', [\App\Http\Controllers\TransferMoneyController::class, 'transferMoney'])->name('governorates.transfer-money');
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
