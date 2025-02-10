<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;


// Breeze default home and dashboard routes
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Breeze user profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// POS System Routes - Ensure these routes are protected by authentication
Route::middleware(['auth'])->group(function () {

    Route::resource('users', UserController::class);
  
    // Menu Routes
    Route::get('/menus', [MenuItemController::class, 'index'])->name('menus.index'); // List all menu items
    Route::get('/menus/create', [MenuItemController::class, 'create'])->name('menus.create'); // Show create form
    Route::post('/menus', [MenuItemController::class, 'store'])->name('menus.store'); // Store a new menu item
    Route::get('/menus/{id}/edit', [MenuItemController::class, 'edit'])->name('menus.edit'); // Show edit form
    Route::get('/menus/{id}', [MenuItemController::class, 'show'])->name('menus.show'); // Show a single menu item
    Route::put('/menus/{id}', [MenuItemController::class, 'update'])->name('menus.update'); // Update a menu item
    Route::delete('/menus/{id}', [MenuItemController::class, 'destroy'])->name('menus.destroy'); // Delete a menu item

    
    // Order Route
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit'); // Show the edit order form
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update'); // Update the order
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    

    // Invoice Routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{id}/show', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    Route::post('/invoices/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');

    
    //Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales', [DashboardController::class,'sales'])->name('dashboard.sales');
    Route::get('/dashboard/inventory', [DashboardController::class, 'inventory'])->name('dashboard.inventory');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/dashboard/invoices', [DashboardController::class, 'invoices'])->name('dashboard.invoices');


});

require __DIR__ . '/auth.php';
