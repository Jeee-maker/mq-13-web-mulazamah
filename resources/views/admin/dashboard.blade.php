@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Selamat Datang Admin</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-danger">Keluar</button>
        </form>
    </div>

    <div class="dashboard-nav">
        <a href="{{ route('admin.cek_mulazamah') }}" class="nav-item">Cek Mulazamah</a>
        <a href="{{ route('admin.konfirmasi_pembayaran') }}" class="nav-item">Konfirmasi Pembayaran Mulazamah</a>
        <a href="#" class="nav-item">Cek Tabungan</a>
        <a href="#" class="nav-item">Konfirmasi Tabungan</a>
        <a href="#" class="nav-item">Cek Absen</a>
        <a href="#" class="nav-item">Konfirmasi Absensi</a>
        <a href="#" class="nav-item">Cek dan Tambah Pengumuman</a>
    </div>

    <div class="glass-panel">
        <h3>Silakan pilih menu di atas untuk melanjutkan.</h3>
    </div>
</div>
@endsection
