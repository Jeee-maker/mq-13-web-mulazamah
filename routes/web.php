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
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true,
        ]);
        return "Instalasi Database Berhasil! Tabel dan data telah dibuat. <a href='/'>Kembali ke Beranda</a>";
    } catch (\Exception $e) {
        return "Gagal: " . $e->getMessage();
    }
});

Route::get('/debug-db', function () {
    $userCount = \App\Models\User::count();
    $studentCount = \App\Models\Student::count();
    $admin = \App\Models\User::where('role', 'admin')->first();
    $maulid = \App\Models\User::where('username', 'Maulid Ahmad Fadiaz')->first();

    return [
        'total_users' => $userCount,
        'total_students' => $studentCount,
        'admin_exists' => $admin ? 'Yes' : 'No',
        'maulid_exists' => $maulid ? 'Yes' : 'No',
    ];
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
