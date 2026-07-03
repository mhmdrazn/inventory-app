<?php

use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard - accessible by all roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products - admin & staff
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::resource('products', ProductController::class);
    });

    // Categories - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Borrowings - admin & staff
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::resource('borrowings', BorrowingController::class);
        Route::patch('borrowings/{borrowing}/return', [BorrowingController::class, 'returnItems'])->name('borrowings.return');
    });

    // Reports - admin & manager
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    });

    // User management - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
