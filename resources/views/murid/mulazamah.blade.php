@extends('layouts.app')

@section('title', 'Mulazamah - Murid')

@push('css')
<style>
.status-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.status-table th, .status-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
.tab-content { display: none; }
.tab-content.active { display: block; }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Selamat Datang {{ $user->name }}</h2>
        <a href="{{ route('murid.dashboard') }}" class="btn-danger" style="text-decoration:none;">Kembali</a>
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
        <div class="glass-panel stat-card">
            <div class="stat-label">Bulan Saat Ini</div>
            <div class="stat-value">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
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

    <!-- Sub Menus -->
    <div class="tab-nav">
        <div class="tab-item active" onclick="switchTab('transfer', event)">Pembayaran Transfer</div>
        <div class="tab-item" onclick="switchTab('riwayat', event)">Riwayat & Status</div>
        <div class="tab-item" onclick="switchTab('hubungi', event)">Hubungi Mulazamah</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div class="mb-1">{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div id="tab-transfer" class="tab-content active glass-panel">
        <h3 class="mb-3 text-gold">Formulir Pembayaran</h3>
        <form action="{{ route('murid.mulazamah.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Pilih Rekening Tujuan</label>
                <select name="dest_bank_info" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="BSI - 7123456789 - A.N MQ-13">BSI - 7123456789 - A.N MQ-13</option>
                    <option value="BRI - 0021345678 - A.N MQ-13">BRI - 0021345678 - A.N MQ-13</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Bank Kamu</label>
                <input type="text" name="user_bank" class="form-control" required placeholder="Contoh: BCA, BNI...">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Rekening Kamu</label>
                <input type="text" name="user_account_no" class="form-control" required placeholder="Masukkan nomor rekening...">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Rekening Kamu</label>
                <input type="text" name="user_account_name" class="form-control" required placeholder="Masukkan nama pemilik rekening...">
            </div>
            <div class="form-group">
                <label class="form-label">Nominal</label>
                <input type="number" name="amount" class="form-control" required placeholder="Contoh: 125000">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan (Opsional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan keterangan jika ada..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Upload Foto Bukti Transaksi (Wajib Max. 2MB)</label>
                <input type="file" name="receipt" class="form-control" required accept="image/*">
            </div>
            <button type="submit" class="btn-gold" onclick="return confirm('Apakah Anda yakin data sudah benar? Proses pembayaran akan diteruskan ke Admin.')" style="margin-top:1rem;">Konfirmasi Pembayaran</button>
        </form>
    </div>

    <div id="tab-riwayat" class="tab-content glass-panel">
        <h3 class="mb-3 text-gold">Status Transaksi Pembayaran</h3>
        <table class="status-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
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
                        <td>
                            <a href="#" class="btn-gold" style="padding:6px 12px; font-size:0.8rem; text-decoration:none;">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada transaksi pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="tab-hubungi" class="tab-content glass-panel text-center">
        <h3 class="mb-3 text-gold">Butuh Bantuan?</h3>
        <p class="mb-4">Jika pembayaran Anda belum dikonfirmasi atau ada masalah lain, silakan hubungi admin Mulazamah melalui WhatsApp.</p>
        <a href="https://wa.me/6285704141792" target="_blank" class="btn-success" style="display:inline-block; text-decoration:none; font-size:1.1rem; padding:15px 30px;">
            Hubungi via WhatsApp
        </a>
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
