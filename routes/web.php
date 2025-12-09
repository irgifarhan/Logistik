<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\SatkerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // Inventory Routes
        Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
        Route::get('/inventory/{barang}/edit', [InventoryController::class, 'edit'])->name('admin.inventory.edit');
        Route::put('/inventory/{barang}', [InventoryController::class, 'update'])->name('admin.inventory.update');
        Route::delete('/inventory/{barang}', [InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
        Route::post('/inventory/{barang}/restock', [InventoryController::class, 'restock'])->name('admin.inventory.restock');
        Route::get('/inventory/{barang}', [InventoryController::class, 'show'])->name('admin.inventory.show');
        
        // Category Routes
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
            Route::get('/create', [CategoryController::class, 'create'])->name('admin.categories.create');
            Route::post('/', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::post('/quick-store', [CategoryController::class, 'quickStore'])->name('admin.categories.quick-store');
            Route::get('/{kategori}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
            Route::put('/{kategori}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::delete('/{kategori}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
            Route::get('/get-categories', [CategoryController::class, 'getCategories'])->name('admin.categories.get');
        });
        
        // Requests Routes
        Route::get('/requests', [PermintaanController::class, 'index'])->name('admin.requests');
        Route::get('/requests/create', [PermintaanController::class, 'create'])->name('admin.requests.create');
        Route::post('/requests', [PermintaanController::class, 'store'])->name('admin.requests.store');
        Route::get('/requests/{permintaan}', [PermintaanController::class, 'show'])->name('admin.requests.show');
        Route::post('/requests/{permintaan}/approve', [PermintaanController::class, 'approve'])->name('admin.requests.approve');
        Route::post('/requests/{permintaan}/reject', [PermintaanController::class, 'reject'])->name('admin.requests.reject');
        
        // Reports Routes - Diperbarui sesuai dengan ReportController
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('admin.reports.generate');
        
        // Export Routes - Multiple options untuk fleksibilitas
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('admin.reports.export');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export.post');
        
        // Chart Data Routes - Untuk AJAX requests
        Route::get('/reports/chart-data', [ReportController::class, 'getChartData'])->name('admin.reports.chart-data');
        
        // Users Routes (dikomentari karena mungkin tidak dibutuhkan)
        // Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        // Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        // Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        // Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        // Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        // Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');
        
        // Satker Routes (dikomentari karena tidak digunakan)
        // Route::get('/satker', [SatkerController::class, 'index'])->name('admin.satker');
        // Route::post('/satker', [SatkerController::class, 'store'])->name('admin.satker.store');
        // Route::get('/satker/{satker}/edit', [SatkerController::class, 'edit'])->name('admin.satker.edit');
        // Route::put('/satker/{satker}', [SatkerController::class, 'update'])->name('admin.satker.update');
        // Route::delete('/satker/{satker}', [SatkerController::class, 'destroy'])->name('admin.satker.destroy');
        
        // Settings Routes (dikomentari karena tidak digunakan)
        // Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
        // Route::post('/settings/save', [SettingController::class, 'save'])->name('admin.settings.save');
    });

    // Kabid Routes
    Route::prefix('kabid')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kabidDashboard'])->name('kabid.dashboard');
        // Add other kabid routes here
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
});

// Fallback Route
Route::fallback(function () {
    return redirect()->route('home');
});