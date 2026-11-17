<?php

use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Seller;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

// ---- Publik: tanpa login ----
Route::get('/', [CatalogController::class, 'index'])->name('home');

// ---- Masuk dan pengajuan stand (khusus pemilik stand dan admin) ----
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/daftar/terkirim', [AuthController::class, 'done'])->name('register.done');
        Route::get('/lupa-sandi', [PasswordResetController::class, 'show'])->name('password.request');
    Route::post('/lupa-sandi', [PasswordResetController::class, 'update'])->middleware('throttle:10,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::put('/akun/email', [AuthController::class, 'updateEmail'])->name('account.email.update');
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
});

// ---- Pemilik stand ----
Route::middleware(['auth', EnsureRole::class.':seller'])->prefix('penjual')->name('seller.')->group(function () {
    Route::get('/', [Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::put('/stand', [Seller\DashboardController::class, 'update'])->name('stand.update');
    Route::post('/pemberitahuan/baca', [Seller\DashboardController::class, 'readNotices'])->name('notices.read');
    Route::post('/menu', [Seller\ProductController::class, 'store'])->name('products.store');
    Route::put('/menu/{product}', [Seller\ProductController::class, 'update'])->name('products.update');
    Route::delete('/menu/{product}', [Seller\ProductController::class, 'destroy'])->name('products.destroy');
});

// ---- Admin ----
Route::middleware(['auth', EnsureRole::class.':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/pengajuan/{user}/terima', [Admin\DashboardController::class, 'approve'])->name('approve');
    Route::post('/pengajuan/{user}/tolak', [Admin\DashboardController::class, 'reject'])->name('reject');
    Route::post('/stand/{stand}/aktif', [Admin\DashboardController::class, 'toggle'])->name('stands.toggle');
    Route::delete('/stand/{stand}', [Admin\DashboardController::class, 'destroy'])->name('stands.destroy');
});
