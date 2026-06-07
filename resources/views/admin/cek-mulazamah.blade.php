@extends('layouts.app')

@section('title', 'Cek Mulazamah')

@push('css')
<style>
.status-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.status-table th, .status-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Cek Mulazamah Murid</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn-danger" style="text-decoration:none;">Kembali</a>
    </div>

    <div class="glass-panel">
        <form action="{{ route('admin.cek_mulazamah') }}" method="GET" class="form-group" style="display:flex; gap:10px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama murid..." value="{{ request('search') }}">
            <button type="submit" class="btn-gold">Cari</button>
        </form>

        <table class="status-table" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>Nama Murid</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->user->username }}</td>
                        <td>
                            <a href="{{ route('admin.detail_mulazamah', $student->id) }}" class="btn-gold" style="padding:6px 12px; font-size:0.8rem; text-decoration:none;">Cek</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada murid ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
            {{ $students->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
