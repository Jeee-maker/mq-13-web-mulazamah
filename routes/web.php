<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MuridController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/install-db', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    return 'Database berhasil di-install dan diisi data awal!';
});

Route::get('/debug-env', function () {
    return response()->json([
        'has_database_url' => env('DATABASE_URL') !== null,
        'has_db_url' => env('DB_URL') !== null,
        'db_connection' => env('DB_CONNECTION'),
        'host' => config('database.connections.pgsql.host'),
        'url_config' => config('database.connections.pgsql.url') !== null,
    ]);
});

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
