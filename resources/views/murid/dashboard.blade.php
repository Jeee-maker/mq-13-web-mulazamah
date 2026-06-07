@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Selamat Datang {{ $user->name }}</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-danger">Keluar</button>
        </form>
    </div>

    <div class="dashboard-nav">
        <a href="{{ route('murid.mulazamah') }}" class="nav-item">Mulazamah</a>
        <a href="#" class="nav-item">Tabungan</a>
        <a href="#" class="nav-item">Pembayaran lainnya</a>
        <a href="#" class="nav-item">Absen Kegiatan</a>
        <a href="#" class="nav-item">Pengumuman</a>
        <a href="#" class="nav-item">Aktivitas</a>
    </div>

    <div class="glass-panel">
        <h3>Silakan pilih menu di atas untuk melanjutkan.</h3>
    </div>
</div>
@endsection
