<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MuridController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/cek-mulazamah', [AdminController::class, 'cekMulazamah'])->name('admin.cek_mulazamah');
    Route::get('/admin/cek-mulazamah/{id}', [AdminController::class, 'detailMulazamah'])->name('admin.detail_mulazamah');
    Route::get('/admin/konfirmasi-pembayaran', [AdminController::class, 'konfirmasiPembayaran'])->name('admin.konfirmasi_pembayaran');
    Route::post('/admin/konfirmasi-pembayaran/{id}', [AdminController::class, 'prosesPembayaran'])->name('admin.proses_pembayaran');

    Route::get('/murid/dashboard', [MuridController::class, 'dashboard'])->name('murid.dashboard');
    Route::get('/murid/mulazamah', [MuridController::class, 'mulazamah'])->name('murid.mulazamah');
    Route::post('/murid/mulazamah/upload', [MuridController::class, 'uploadPayment'])->name('murid.mulazamah.upload');
});
