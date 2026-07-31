@extends('layouts.app')
@section('page-title', 'Dashboard Administrator')
@section('content')
<div class="space-y-6">
    <!-- Alerts for Unassigned Students -->
    @if(($siswaUnassignedPembimbing ?? 0) > 0 || ($siswaUnassignedPerusahaan ?? 0) > 0)
    <div class="bg-red-900/20 border-l-4 border-red-500/50 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.1)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-2 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <span class="text-3xl animate-bounce">⚠️</span>
            <div>
                <p class="font-bold text-red-200 text-sm md:text-base">Perhatian: Siswa Terlantar Ditemukan!</p>
                <div class="text-xs text-red-300 mt-0.5 space-y-1">
                    @if(($siswaUnassignedPembimbing ?? 0) > 0)
                    <p>• <strong>{{ $siswaUnassignedPembimbing }}</strong> siswa belum memiliki Pembimbing.</p>
                    @endif
                    @if(($siswaUnassignedPerusahaan ?? 0) > 0)
                    <p>• <strong>{{ $siswaUnassignedPerusahaan }}</strong> siswa belum di-assign ke Perusahaan Mitra.</p>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="bg-red-600/80 hover:bg-red-500 border border-red-500/50 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap shadow-sm active:scale-95 text-center">Plotting Siswa Sekarang</a>
    </div>
    @endif

    <!-- Stats Grid (Responsive: 1 col mobile -> 4 col desktop) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-panel p-4 rounded-xl shadow-sm flex items-center gap-4 hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center text-2xl border border-blue-500/30 shadow-[0_0_15px_rgba(59,130,246,0.3)]">👥</div>
            <div><p class="text-sm text-gray-400">Total Siswa</p><p class="text-2xl font-bold text-white">{{ $stats['total_siswa'] ?? 0 }}</p></div>
        </div>
        <div class="glass-panel p-4 rounded-xl shadow-sm flex items-center gap-4 hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 rounded-full bg-crypto-success/20 flex items-center justify-center text-2xl border border-crypto-success/30 shadow-[0_0_15px_rgba(14,203,129,0.3)]">🏢</div>
            <div><p class="text-sm text-gray-400">Industri Mitra</p><p class="text-2xl font-bold text-white">{{ $stats['total_perusahaan'] ?? 0 }}</p></div>
        </div>
        <div class="glass-panel p-4 rounded-xl shadow-sm flex items-center gap-4 hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 rounded-full bg-crypto-accent/20 flex items-center justify-center text-2xl border border-crypto-accent/30 shadow-[0_0_15px_rgba(112,0,255,0.3)]">📅</div>
            <div><p class="text-sm text-gray-400">Absensi Hari Ini</p><p class="text-2xl font-bold text-white">{{ $stats['absensi_hari_ini'] ?? 0 }}</p></div>
        </div>
        <div class="glass-panel p-4 rounded-xl shadow-sm flex items-center gap-4 hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-2xl border border-orange-500/30 shadow-[0_0_15px_rgba(249,115,22,0.3)]">👁️</div>
            <div><p class="text-sm text-gray-400">Kunjungan Bulan Ini</p><p class="text-2xl font-bold text-white">{{ $stats['kunjungan_bulan_ini'] ?? 0 }}</p></div>
        </div>
    </div>

    <!-- Chart & Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Section -->
        <div class="lg:col-span-2 glass-panel rounded-xl p-5 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-crypto-accent/5 to-crypto-success/5 opacity-0 group-hover:opacity-100 transition duration-500 pointer-events-none"></div>
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2 relative z-10">
                <span class="bg-blue-500/20 border border-blue-500/30 text-blue-400 p-1.5 rounded-lg text-sm shadow-[0_0_10px_rgba(59,130,246,0.2)]">📊</span> Distribusi Siswa per Jurusan
            </h3>
            <div class="relative h-64 w-full z-10">
                <canvas id="jurusanChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="lg:col-span-1 space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="bg-orange-500/20 border border-orange-500/30 text-orange-400 p-1.5 rounded-lg text-sm shadow-[0_0_10px_rgba(249,115,22,0.2)]">⚡</span> Aksi Cepat
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                <a href="{{ route('admin.siswa.index') }}" class="glass-panel p-3 rounded-xl hover:border-crypto-accent/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(112,0,255,0.15)] transition-all duration-300 flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">🎓</span><div><div class="font-bold text-white text-sm">Assign Siswa</div><div class="text-[10px] text-gray-400 uppercase tracking-wider">Plotting massal</div></div>
                </a>
                <a href="{{ route('admin.rekap-absensi.index') }}" class="glass-panel p-3 rounded-xl hover:border-crypto-success/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(14,203,129,0.15)] transition-all duration-300 flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">📈</span><div><div class="font-bold text-white text-sm">Rekap Absensi</div><div class="text-[10px] text-gray-400 uppercase tracking-wider">Laporan kehadiran</div></div>
                </a>
                <a href="{{ route('admin.jurnal.index') }}" class="glass-panel p-3 rounded-xl hover:border-blue-500/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(59,130,246,0.15)] transition-all duration-300 flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">📖</span><div><div class="font-bold text-white text-sm">Rekap Jurnal</div><div class="text-[10px] text-gray-400 uppercase tracking-wider">Laporan kegiatan</div></div>
                </a>
                <a href="{{ route('admin.periode-pkl.index') }}" class="glass-panel p-3 rounded-xl hover:border-orange-500/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(249,115,22,0.15)] transition-all duration-300 flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">📅</span><div><div class="font-bold text-white text-sm">Periode PKL</div><div class="text-[10px] text-gray-400 uppercase tracking-wider">Manajemen Waktu</div></div>
                </a>
                <a href="{{ route('admin.users.index') }}" class="glass-panel p-3 rounded-xl hover:border-pink-500/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(236,72,153,0.15)] transition-all duration-300 flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">👥</span><div><div class="font-bold text-white text-sm">Kelola User</div><div class="text-[10px] text-gray-400 uppercase tracking-wider">Hak akses & Profil</div></div>
                </a>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('jurusanChart');
    if (!ctx) return;
    
    const chartData = @json($chartJurusan ?? ['labels' => [], 'data' => []]);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: [
                    '#3b82f6', // blue-500
                    '#10b981', // emerald-500
                    '#f59e0b', // amber-500
                    '#8b5cf6', // violet-500
                    '#ec4899', // pink-500
                    '#14b8a6', // teal-500
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: '#d1d5db',
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 12
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
@endsection