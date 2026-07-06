<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\OrderController;

// Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Logout Route (Auth Only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Inventory, Order, and CRM Routes (Auth Only)
Route::middleware('auth')->group(function () {
    // Open Access to All Authenticated Roles
    Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');
    Route::get('/items', [InventoryController::class, 'items'])->name('items.index');
    Route::get('/items/{id}', [InventoryController::class, 'showItem'])->name('items.show');
    Route::post('/items/{id}/messages', [InventoryController::class, 'storeMessage'])->name('messages.store');
    Route::get('/notifications', [InventoryController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [InventoryController::class, 'markNotificationRead'])->name('notifications.read');

    // Warehouse Staff Access (warehouse_staff)
    Route::middleware('role:warehouse_staff')->group(function () {
        Route::post('/items', [InventoryController::class, 'storeItem'])->name('items.store');
        Route::get('/items/{id}/edit', [InventoryController::class, 'editItem'])->name('items.edit');
        Route::post('/items/{id}/update', [InventoryController::class, 'updateItem'])->name('items.update');
        Route::post('/items/{id}/movements', [InventoryController::class, 'storeMovement'])->name('movements.store');
        
        // Reports
        Route::get('/reports', [InventoryController::class, 'reports'])->name('reports.index');
        Route::get('/reports/print', [InventoryController::class, 'printReport'])->name('reports.print');
    });

    // Sales Staff Access (sales_staff)
    Route::middleware('role:sales_staff')->group(function () {
        // Order & Tracking (API Kiriminaja)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/api/rates', [OrderController::class, 'apiGetRates'])->name('orders.api.rates');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/process', [OrderController::class, 'processShipment'])->name('orders.process');
        Route::get('/orders/{id}/tracking', [OrderController::class, 'trackShipment'])->name('orders.tracking');
        Route::post('/orders/{id}/complete', [OrderController::class, 'completeOrder'])->name('orders.complete');
        Route::post('/orders/{id}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment');

        // CRM Routes
        Route::get('/crm', [CrmController::class, 'index'])->name('crm.index');
        Route::get('/crm/templates', [CrmController::class, 'showTemplates'])->name('crm.templates');
        Route::get('/crm/customer/{phone}', [CrmController::class, 'showDetail'])->name('crm.detail');
        Route::get('/crm/complaints', [CrmController::class, 'complaints'])->name('crm.complaints');
        Route::post('/crm/complaints', [CrmController::class, 'storeComplaint'])->name('crm.complaints.store');
        Route::post('/crm/complaints/{id}/status', [CrmController::class, 'updateComplaintStatus'])->name('crm.complaints.updateStatus');
    });

    // Super Admin Only Access (super_admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');
    });
});

// API endpoints for E-commerce Microservice (Public / Tokenless / Simple cross-origin)
Route::prefix('api/ecommerce')->group(function () {
    Route::get('/products', [\App\Http\Controllers\ApiController::class, 'getProducts']);
    Route::post('/checkout', [\App\Http\Controllers\ApiController::class, 'placeOrder']);
    Route::get('/tracking/{order_number}', [\App\Http\Controllers\ApiController::class, 'trackOrder']);
});
