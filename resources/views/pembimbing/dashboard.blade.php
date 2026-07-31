@extends('layouts.app')
@section('page-title', 'Dashboard Pembimbing')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="glass-panel bg-gradient-to-r from-crypto-accent/80 to-blue-800/80 rounded-xl p-6 text-white border border-white/10 shadow-[0_0_15px_rgba(112,0,255,0.2)]">
        <h2 class="text-xl font-bold drop-shadow-md">👨‍🏫 Halo, {{ auth()->user()->name }}!</h2>
        <p class="text-blue-200 mt-1 drop-shadow-sm">Ringkasan aktivitas siswa binaan PKL</p>
    </div>

    <!-- Stats Cards (6 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Siswa -->
        <a href="{{ route('pembimbing.siswa-binaan') }}" class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 hover:border-white/20 transition-colors block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Siswa</p>
                    <p class="text-2xl font-bold text-white drop-shadow-[0_0_5px_rgba(255,255,255,0.3)]">{{ $totalSiswa }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl border border-white/20">👥</div>
            </div>
        </a>

        <!-- Total Perusahaan -->
        <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Industri</p>
                    <p class="text-2xl font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">{{ $totalPerusahaan }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-crypto-accent/20 flex items-center justify-center text-xl border border-crypto-accent/30">🏭</div>
            </div>
        </div>
        
        <!-- Jurnal Menunggu -->
        <a href="{{ route('pembimbing.jurnal') }}" class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 hover:border-yellow-500/30 transition-colors block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Jurnal Menunggu</p>
                    <p class="text-2xl font-bold text-yellow-400 drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]">{{ $jurnalMenunggu }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center text-xl border border-yellow-500/30">📖</div>
            </div>
        </a>
        
        <!-- Absensi Perlu Approve -->
        <a href="{{ route('pembimbing.absensi') }}" class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 hover:border-orange-500/30 transition-colors block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Perlu Approve</p>
                    <p class="text-2xl font-bold text-orange-400 drop-shadow-[0_0_5px_rgba(249,115,22,0.5)]">{{ $absensiPerluApprove }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center text-xl border border-orange-500/30">✅</div>
            </div>
        </a>
        
        <!-- Kunjungan Direncanakan -->
        <a href="{{ route('pembimbing.kunjungan') }}" class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 hover:border-purple-500/30 transition-colors block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Kunjungan</p>
                    <p class="text-2xl font-bold text-purple-400 drop-shadow-[0_0_5px_rgba(168,85,247,0.5)]">{{ $kunjunganMendatang }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center text-xl border border-purple-500/30">📍</div>
            </div>
        </a>
        
        <!-- Siswa Absen Hari Ini -->
        <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Absen Hari Ini</p>
                    <p class="text-2xl font-bold text-crypto-success drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">{{ $siswaAbsenHariIni }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-crypto-success/20 flex items-center justify-center text-xl border border-crypto-success/30">📅</div>
            </div>
        </div>
    </div>

    <!-- Early Warning System -->
    @if(isset($earlyWarnings) && $earlyWarnings->count() > 0)
    <div class="glass-panel bg-red-500/10 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.1)] border border-red-500/30 p-5 animate-in fade-in slide-in-from-bottom-2">
        <h3 class="font-bold text-red-400 mb-4 flex items-center gap-2 drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">
            ⚠️ Perhatian Khusus (Early Warning System)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($earlyWarnings as $warning)
            <div class="bg-black/20 p-3 rounded-lg border border-red-500/30 flex items-start gap-3 shadow-inner hover:border-red-500/50 transition-colors">
                <div class="text-2xl mt-0.5 drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">🚨</div>
                <div>
                    <p class="font-bold text-gray-200 text-sm">{{ $warning['nama'] }}</p>
                    <p class="text-xs text-red-300 mt-1 font-medium">{{ $warning['masalah'] }}</p>
                    <a href="{{ route('pembimbing.siswa-binaan') }}" class="text-[10px] text-crypto-accent hover:text-white mt-1 inline-block transition-colors">Lihat Detail Siswa &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 2 Column Layout for Activity and Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Aktivitas Terbaru -->
        <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 p-5">
            <h3 class="font-bold text-white mb-4">⚡ Aktivitas Terbaru</h3>
            <div class="space-y-3">
                @forelse($aktivitasTerbaru as $item)
                    @if($item['type'] === 'absensi')
                        @php $a = $item['data']; @endphp
                        <div class="flex items-center justify-between p-3 bg-orange-500/10 rounded-lg border border-orange-500/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center">📅</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-200">{{ $a->siswa->name }} - Absensi {{ $a->tanggal->format('d/m') }}</p>
                                    <p class="text-xs text-gray-400">Jam: {{ $a->check_in }} • IP: {{ $a->ip_address }}</p>
                                </div>
                            </div>
                            <form action="{{ route('pembimbing.absensi.verify', $a->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="text-xs bg-orange-500/20 text-orange-400 border border-orange-500/30 px-3 py-1.5 rounded hover:bg-orange-500 hover:text-white font-medium active:scale-95 transition-colors shadow-inner">
                                    Verifikasi
                                </button>
                            </form>
                        </div>
                    @else
                        @php $j = $item['data']; @endphp
                        <div class="flex items-center justify-between p-3 bg-yellow-500/10 rounded-lg border border-yellow-500/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-yellow-500/20 border border-yellow-500/30 flex items-center justify-center">📖</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-200">{{ $j->siswa->name }} - Jurnal {{ $j->tanggal->format('d/m') }}</p>
                                    <p class="text-xs text-gray-400">{{ \Str::limit($j->kegiatan, 50) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.jurnal') }}" class="text-xs bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-3 py-1.5 rounded hover:bg-yellow-500 hover:text-white transition-colors shadow-inner">Review</a>
                        </div>
                    @endif
                @empty
                    <p class="text-center text-gray-500 py-4">🎉 Tidak ada aktivitas baru</p>
                @endforelse
            </div>
        </div>

        <!-- Grafik Kehadiran -->
        <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 p-5">
            <h3 class="font-bold text-white mb-4">📊 Rekapitulasi Kehadiran Siswa Binaan</h3>
            <div class="relative h-72 w-full">
                @if(array_sum($chartAbsensi) > 0)
                    <canvas id="absensiChart" class="w-full h-full"></canvas>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <p class="text-4xl mb-2 opacity-50 drop-shadow-[0_0_10px_rgba(255,255,255,0.2)]">📭</p>
                        <p class="text-gray-400 text-sm italic">Belum ada data absensi yang tercatat untuk siswa binaan Anda.</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(array_sum($chartAbsensi) > 0)
    const ctx = document.getElementById('absensiChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
            datasets: [{
                data: [
                    {{ $chartAbsensi['hadir'] }},
                    {{ $chartAbsensi['sakit'] }},
                    {{ $chartAbsensi['izin'] }},
                    {{ $chartAbsensi['alpha'] }}
                ],
                backgroundColor: [
                    '#10B981', // green-500
                    '#F59E0B', // amber-500
                    '#3B82F6', // blue-500
                    '#EF4444'  // red-500
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed + ' kali';
                            }
                            return label;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
    @endif
});
</script>
@endpush