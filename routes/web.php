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
    $url = env('DATABASE_URL');
    $parsed = is_string($url) ? parse_url($url) : null;
    if (is_array($parsed) && isset($parsed['pass'])) {
        $parsed['pass'] = 'HIDDEN';
    }
    
    $config = config('database.connections.pgsql');
    $parser = new \Illuminate\Support\ConfigurationUrlParser();
    $parsedConfig = $parser->parseConfiguration($config);

    if (isset($config['url'])) $config['url'] = 'HIDDEN';
    if (isset($config['password'])) $config['password'] = 'HIDDEN';
    
    if (isset($parsedConfig['url'])) $parsedConfig['url'] = 'HIDDEN';
    if (isset($parsedConfig['password'])) $parsedConfig['password'] = 'HIDDEN';

    return response()->json([
        'parsed_url' => $parsed,
        'config' => $config,
        'parsed_config' => $parsedConfig,
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
