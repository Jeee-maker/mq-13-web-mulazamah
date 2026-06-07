@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')

@push('css')
<style>
.tab-nav {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255,255,255,0.1);
}
.tab-item {
    padding: 10px 20px;
    cursor: pointer;
    font-weight: 600;
    color: rgba(255,255,255,0.6);
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
}
.tab-item.active {
    color: var(--gold);
    border-bottom-color: var(--gold);
}
.tab-content { display: none; }
.tab-content.active { display: block; }
.status-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.status-table th, .status-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Konfirmasi Pembayaran Mulazamah</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn-danger" style="text-decoration:none;">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Sub Menus -->
    <div class="tab-nav">
        <div class="tab-item active" onclick="switchTab('diproses', event)">Di Proses</div>
        <div class="tab-item" onclick="switchTab('selesai', event)">Selesai</div>
        <div class="tab-item" onclick="switchTab('ditolak', event)">Di Tolak</div>
    </div>

    <!-- Tab Di Proses -->
    <div id="tab-diproses" class="tab-content active glass-panel">
        <h3 class="mb-3 text-gold">Menunggu Konfirmasi</h3>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Murid</th>
                    <th>Nominal</th>
                    <th>Rek. Pengirim</th>
                    <th>Bukti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $trx)
                    <tr>
                        <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $trx->student->user->name }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td>{{ $trx->user_bank }} - {{ $trx->user_account_no }}<br><small>{{ $trx->user_account_name }}</small></td>
                        <td>
                            <a href="{{ asset('storage/' . $trx->receipt_path) }}" target="_blank" class="text-gold">Lihat Bukti</a>
                        </td>
                        <td>
                            <form action="{{ route('admin.proses_pembayaran', $trx->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn-success" style="padding:6px 12px; font-size:0.8rem;" onclick="return confirm('Konfirmasi pembayaran ini valid?')">Konfirmasi</button>
                            </form>
                            <form action="{{ route('admin.proses_pembayaran', $trx->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn-danger" style="padding:6px 12px; font-size:0.8rem;" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada transaksi yang menunggu konfirmasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tab Selesai -->
    <div id="tab-selesai" class="tab-content glass-panel">
        <h3 class="mb-3 text-success">Pembayaran Selesai</h3>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Tgl Selesai</th>
                    <th>Nama Murid</th>
                    <th>Nominal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approved as $trx)
                    <tr>
                        <td>{{ $trx->updated_at->format('d M Y H:i') }}</td>
                        <td>{{ $trx->student->user->name }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td><span class="status-paid" style="padding:4px 8px; border-radius:12px; font-size:0.85rem;">Selesai</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada transaksi selesai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tab Ditolak -->
    <div id="tab-ditolak" class="tab-content glass-panel">
        <h3 class="mb-3 text-danger">Pembayaran Ditolak</h3>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Tgl Ditolak</th>
                    <th>Nama Murid</th>
                    <th>Nominal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rejected as $trx)
                    <tr>
                        <td>{{ $trx->updated_at->format('d M Y H:i') }}</td>
                        <td>{{ $trx->student->user->name }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td><span class="status-unpaid" style="padding:4px 8px; border-radius:12px; font-size:0.85rem;">Ditolak</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada transaksi ditolak.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(tabId, event) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
    
    document.getElementById('tab-' + tabId).classList.add('active');
    event.target.classList.add('active');
}
</script>
@endpush
