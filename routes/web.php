<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckPermission;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    // Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
    Route::prefix('inventory')->group(function () {
        // Items
        Route::get('/', [ItemController::class, 'index'])->name('inventory.index');
        Route::get('/items/create', [ItemController::class, 'create'])->name('inventory.items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('inventory.items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('inventory.items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('inventory.items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('inventory.items.destroy');

        // Categories
        Route::get('/categories', [ItemCategoryController::class, 'index'])->name('inventory.categories.index');
        Route::post('/categories', [ItemCategoryController::class, 'store'])->name('inventory.categories.store');
        Route::put('/categories/{category}', [ItemCategoryController::class, 'update'])->name('inventory.categories.update');
        Route::delete('/categories/{category}', [ItemCategoryController::class, 'destroy'])->name('inventory.categories.destroy');
    });

    Route::prefix('inventory/stock')->group(function () {
        Route::get('/', [StockManagementController::class, 'index'])
            ->name('inventory.stock.index')
            ->middleware(CheckPermission::class . ':view_stock_history');

        Route::post('/adjust', [StockManagementController::class, 'adjust'])
            ->name('inventory.stock.adjust')
            ->middleware(CheckPermission::class . ':adjust_stock');

        Route::get('/{item}/history', [StockManagementController::class, 'history'])
            ->name('inventory.stock.history')
            ->middleware(CheckPermission::class . ':view_stock_history');
    });

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])
        ->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    Route::prefix('purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])
            ->name('purchase-orders.index')
            ->middleware(CheckPermission::class . ':view_purchase_orders');

        Route::get('/create', [PurchaseOrderController::class, 'create'])
            ->name('purchase-orders.create')
            ->middleware(CheckPermission::class . ':create_purchase_orders');

        Route::post('/', [PurchaseOrderController::class, 'store'])
            ->name('purchase-orders.store')
            ->middleware(CheckPermission::class . ':create_purchase_orders');

        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
            ->name('purchase-orders.show')
            ->middleware(CheckPermission::class . ':view_purchase_orders');

        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
            ->name('purchase-orders.update')
            ->middleware(CheckPermission::class . ':edit_purchase_orders');

        Route::get('/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'generatePdf'])
            ->name('purchase-orders.pdf')
            ->middleware(CheckPermission::class . ':view_purchase_orders');
    });

    Route::prefix('sales')->group(function () {
        Route::get('/', [OrderController::class, 'index'])
            ->name('sales.index')
            ->middleware(CheckPermission::class . ':view_orders');

        Route::get('/create', [OrderController::class, 'create'])
            ->name('sales.create')
            ->middleware(CheckPermission::class . ':create_orders');

        Route::post('/', [OrderController::class, 'store'])
            ->name('sales.store')
            ->middleware(CheckPermission::class . ':create_orders');

        Route::get('/{order}', [OrderController::class, 'show'])
            ->name('sales.show')
            ->middleware(CheckPermission::class . ':view_orders');

        Route::post('/{order}/void', [OrderController::class, 'void'])
            ->name('sales.void')
            ->middleware(CheckPermission::class . ':void_orders');

        Route::get('/{order}/receipt', [OrderController::class, 'receipt'])
            ->name('sales.receipt')
            ->middleware(CheckPermission::class . ':view_orders');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])
            ->name('reports.index')
            ->middleware(CheckPermission::class . ':view_reports');

        Route::get('/sales', [ReportController::class, 'sales'])
            ->name('reports.sales')
            ->middleware(CheckPermission::class . ':view_sales_reports');

        Route::get('/inventory', [ReportController::class, 'inventory'])
            ->name('reports.inventory')
            ->middleware(CheckPermission::class . ':view_inventory_reports');

        Route::get('/financial', [ReportController::class, 'financial'])
            ->name('reports.financial')
            ->middleware(CheckPermission::class . ':view_financial_reports');

        // Export routes
        Route::get('/sales/export', [ReportController::class, 'exportSales'])
            ->name('reports.sales.export')
            ->middleware(CheckPermission::class . ':export_reports');

        Route::get('/inventory/export', [ReportController::class, 'exportInventory'])
            ->name('reports.inventory.export')
            ->middleware(CheckPermission::class . ':export_reports');

        Route::get('/financial/export', [ReportController::class, 'exportFinancial'])
            ->name('reports.financial.export')
            ->middleware(CheckPermission::class . ':export_reports');
    });

    Route::get('/settings', [SettingController::class, 'index'])
        ->name('settings.index')
        ->middleware('permission:manage_settings');

    Route::put('/settings', [SettingController::class, 'update'])
        ->name('settings.update')
        ->middleware('permission:manage_settings');
    // Add more routes here...
});

require __DIR__ . '/auth.php';
