@extends('layouts.app')
@section('page-title', 'Dashboard Administrator')
@section('content')
<div class="space-y-6">
    <!-- Alerts for Unassigned Students -->
    @if(($siswaUnassignedPembimbing ?? 0) > 0 || ($siswaUnassignedPerusahaan ?? 0) > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-2">
        <div class="flex items-center gap-3">
            <span class="text-3xl animate-bounce">⚠️</span>
            <div>
                <p class="font-bold text-red-800 text-sm md:text-base">Perhatian: Siswa Terlantar Ditemukan!</p>
                <div class="text-xs text-red-600 mt-0.5 space-y-1">
                    @if(($siswaUnassignedPembimbing ?? 0) > 0)
                    <p>• <strong>{{ $siswaUnassignedPembimbing }}</strong> siswa belum memiliki Pembimbing.</p>
                    @endif
                    @if(($siswaUnassignedPerusahaan ?? 0) > 0)
                    <p>• <strong>{{ $siswaUnassignedPerusahaan }}</strong> siswa belum di-assign ke Perusahaan Mitra.</p>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap shadow-sm active:scale-95 text-center">Plotting Siswa Sekarang</a>
    </div>
    @endif

    <!-- Stats Grid (Responsive: 1 col mobile -> 4 col desktop) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-2xl">👥</div>
            <div><p class="text-sm text-gray-500">Total Siswa</p><p class="text-2xl font-bold text-gray-800">{{ $stats['total_siswa'] ?? 0 }}</p></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-2xl">🏢</div>
            <div><p class="text-sm text-gray-500">Industri Mitra</p><p class="text-2xl font-bold text-gray-800">{{ $stats['total_perusahaan'] ?? 0 }}</p></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-2xl">📅</div>
            <div><p class="text-sm text-gray-500">Absensi Hari Ini</p><p class="text-2xl font-bold text-gray-800">{{ $stats['absensi_hari_ini'] ?? 0 }}</p></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-2xl">👁️</div>
            <div><p class="text-sm text-gray-500">Kunjungan Bulan Ini</p><p class="text-2xl font-bold text-gray-800">{{ $stats['kunjungan_bulan_ini'] ?? 0 }}</p></div>
        </div>
    </div>

    <!-- Chart & Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="bg-blue-100 text-blue-700 p-1.5 rounded-lg text-sm">📊</span> Distribusi Siswa per Jurusan
            </h3>
            <div class="relative h-64 w-full">
                <canvas id="jurusanChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="lg:col-span-1 space-y-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="bg-orange-100 text-orange-700 p-1.5 rounded-lg text-sm">⚡</span> Aksi Cepat
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                <a href="{{ route('admin.siswa.index') }}" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:border-blue-300 hover:shadow-md transition flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform">🎓</span><div><div class="font-bold text-gray-800 text-sm">Assign Siswa</div><div class="text-[10px] text-gray-500 uppercase tracking-wider">Plotting massal</div></div>
                </a>
                <a href="{{ route('admin.rekap-absensi.index') }}" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:border-green-300 hover:shadow-md transition flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform">📈</span><div><div class="font-bold text-gray-800 text-sm">Rekap Absensi</div><div class="text-[10px] text-gray-500 uppercase tracking-wider">Laporan kehadiran</div></div>
                </a>
                <a href="{{ route('admin.jurnal.index') }}" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:border-purple-300 hover:shadow-md transition flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform">📖</span><div><div class="font-bold text-gray-800 text-sm">Rekap Jurnal</div><div class="text-[10px] text-gray-500 uppercase tracking-wider">Laporan kegiatan</div></div>
                </a>
                <a href="{{ route('admin.periode-pkl.index') }}" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:border-amber-300 hover:shadow-md transition flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform">📅</span><div><div class="font-bold text-gray-800 text-sm">Periode PKL</div><div class="text-[10px] text-gray-500 uppercase tracking-wider">Manajemen Waktu</div></div>
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:border-indigo-300 hover:shadow-md transition flex items-center gap-3 group active:scale-95">
                    <span class="text-2xl group-hover:scale-110 transition-transform">👥</span><div><div class="font-bold text-gray-800 text-sm">Kelola User</div><div class="text-[10px] text-gray-500 uppercase tracking-wider">Hak akses & Profil</div></div>
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
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            family: "'Inter', sans-serif",
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