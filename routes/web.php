<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard as home page
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // User CRUD routes
    Route::resource('user', UserController::class)->middleware('check.permission:user.index');

    // Role & Menu Management
    Route::resource('role', \App\Http\Controllers\RoleController::class)->middleware('check.permission:role.index');
    Route::resource('menu', \App\Http\Controllers\MenuController::class)->middleware('check.permission:menu.index');
    Route::get('permission', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permission.index')->middleware('check.permission:permission.index');
    Route::put('permission', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permission.update')->middleware('check.permission:permission.index');

    // Products CRUD routes
    Route::resource('products', ProductsController::class)->middleware('check.permission:products.index');

    // Photography Service Modules
    // Master Data
    Route::resource('kategori', \App\Http\Controllers\KategoriController::class)->middleware('check.permission:kategori.index');
    Route::resource('layanan', \App\Http\Controllers\LayananController::class)->middleware('check.permission:layanan.index');
    Route::resource('paket-layanan', \App\Http\Controllers\PaketLayananController::class)->middleware('check.permission:paket-layanan.index');

    // Transaksi & Core
    Route::resource('pesanan', \App\Http\Controllers\PesananController::class)->middleware('check.permission:pesanan.index');
    
    // Jadwal & Penugasan
    Route::resource('penugasan-fotografer', \App\Http\Controllers\PenugasanFotograferController::class)->middleware('check.permission:penugasan-fotografer.index');
    Route::resource('ketersediaan', \App\Http\Controllers\KetersediaanFotograferController::class)->middleware('check.permission:ketersediaan.index');
    Route::resource('jadwal-pesanan', \App\Http\Controllers\JadwalPesananController::class)->middleware('check.permission:jadwal-pesanan.index');

    // Pembayaran
    Route::resource('pembayaran', \App\Http\Controllers\PembayaranController::class)->middleware('check.permission:pembayaran.index');

    // Rating & Ulasan
    Route::resource('rating-fotografer', \App\Http\Controllers\RatingFotograferController::class)->middleware('check.permission:rating-fotografer.index');
    Route::resource('rating-layanan', \App\Http\Controllers\RatingLayananController::class)->middleware('check.permission:rating-layanan.index');


    // Laporan
    Route::prefix('laporan')->name('laporan.')->middleware('check.permission:laporan')->group(function () {
        Route::get('pesanan', [\App\Http\Controllers\LaporanController::class, 'pesanan'])->name('pesanan');
        Route::get('pendapatan', [\App\Http\Controllers\LaporanController::class, 'pendapatan'])->name('pendapatan');
        Route::get('fotografer', [\App\Http\Controllers\LaporanController::class, 'fotografer'])->name('fotografer');
    });

    // Activity Log
    Route::get('activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('activity-log/data', [\App\Http\Controllers\ActivityLogController::class, 'getData'])->name('activity-log.data');
    Route::get('activity-log/statistics', [\App\Http\Controllers\ActivityLogController::class, 'statistics'])->name('activity-log.statistics');
});

