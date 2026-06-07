@extends('layouts.app')

@section('title', 'Detail Mulazamah - Admin')

@push('css')
<style>
.status-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.status-table th, .status-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Detail Mulazamah: {{ $student->user->name }}</h2>
        <a href="{{ route('admin.cek_mulazamah') }}" class="btn-danger" style="text-decoration:none;">Kembali</a>
    </div>

    <!-- Stats Summary -->
    <div class="stats-grid">
        <div class="glass-panel stat-card">
            <div class="stat-label">Terbayar</div>
            <div class="stat-value text-success">Rp {{ number_format($student->total_paid, 0, ',', '.') }}</div>
        </div>
        <div class="glass-panel stat-card">
            <div class="stat-label">Kekurangan</div>
            <div class="stat-value text-danger">Rp {{ number_format($student->total_debt, 0, ',', '.') }}</div>
        </div>
        <div class="glass-panel stat-card">
            <div class="stat-label">Seharusnya</div>
            <div class="stat-value text-gold">Rp {{ number_format($student->total_expected, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="glass-panel">
        <h3 class="mb-4">Data Mulazamah Per Tahun</h3>

        @foreach($yearsData as $data)
            <div class="accordion">
                <div class="accordion-header">
                    <h4>Tahun {{ $data['year'] }}H</h4>
                    <span>▼</span>
                </div>
                <div class="accordion-content">
                    
                    <!-- Custom Progress Bar -->
                    <div class="progress-container">
                        @php
                            $total = $data['expected'] > 0 ? $data['expected'] : 1;
                            $paidPercentage = min(100, ($data['paid'] / $total) * 100);
                        @endphp
                        
                        <div class="progress-marker" style="left: 100%;">
                            Seharusnya: Rp {{ number_format($data['expected'], 0, ',', '.') }}
                        </div>
                        
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar-fill" style="width: {{ $paidPercentage }}%;"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:0.85rem;">
                            <span class="text-success">Terbayar: Rp {{ number_format($data['paid'], 0, ',', '.') }}</span>
                            <span class="text-danger">Kekurangan: Rp {{ number_format($data['debt'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Months List -->
                    <div class="month-list">
                        @foreach($data['months'] as $month)
                            <div class="month-item {{ $month->is_paid ? 'paid' : '' }}">
                                <div class="month-name">{{ $month->hijri_month }}</div>
                                <div>
                                    <span class="month-status {{ $month->is_paid ? 'status-paid' : 'status-unpaid' }}">
                                        {{ $month->is_paid ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                    <div style="font-size:0.8rem; margin-top:4px; opacity:0.8;">Rp {{ number_format($month->amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="glass-panel mt-3" style="margin-top: 1.5rem;">
        <h3 class="mb-3 text-gold">Status Transaksi Pembayaran Murid Ini</h3>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td>{{ $trx->notes ?: '-' }}</td>
                        <td>
                            @if($trx->status === 'pending')
                                <span class="status-pending" style="padding:4px 8px; border-radius:12px; font-size:0.85rem;">Di Proses</span>
                            @elseif($trx->status === 'approved')
                                <span class="status-paid" style="padding:4px 8px; border-radius:12px; font-size:0.85rem;">Selesai</span>
                            @else
                                <span class="status-unpaid" style="padding:4px 8px; border-radius:12px; font-size:0.85rem;">Di Tolak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada transaksi pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
