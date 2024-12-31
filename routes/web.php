<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Redirect root to login or dashboard based on auth status
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Routes for unauthenticated users (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// Protect routes with auth middleware
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //dashboard related routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('shopping-list/store', [DashboardController::class, 'storeItem'])->name('shopping-list.store');
    Route::post('shopping-list/update', [DashboardController::class, 'updatePurchaseStatus'])->name('shopping-list.update');
    Route::post('shopping-list/destroy', [DashboardController::class, 'destroyItem'])->name('shopping-list.destroy');
    Route::post('shopping-list/update-budget', [DashboardController::class, 'updateBudget'])->name('shopping-list.update-budget');

});
