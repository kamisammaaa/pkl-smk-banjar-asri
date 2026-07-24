@extends('layouts.app')
@section('page-title', 'Dashboard Pembimbing')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-800 rounded-xl p-6 text-white">
        <h2 class="text-xl font-bold">👨‍🏫 Halo, {{ auth()->user()->name }}!</h2>
        <p class="text-blue-100 mt-1">Ringkasan aktivitas siswa binaan PKL</p>
    </div>

    <!-- Stats Cards (6 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Siswa -->
        <a href="{{ route('pembimbing.siswa-binaan') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSiswa }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-xl">👥</div>
            </div>
        </a>

        <!-- Total Perusahaan -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Industri</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $totalPerusahaan }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-xl">🏭</div>
            </div>
        </div>
        
        <!-- Jurnal Menunggu -->
        <a href="{{ route('pembimbing.jurnal') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Jurnal Menunggu</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $jurnalMenunggu }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-xl">📖</div>
            </div>
        </a>
        
        <!-- Absensi Perlu Approve -->
        <a href="{{ route('pembimbing.absensi') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Perlu Approve</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $absensiPerluApprove }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-xl">✅</div>
            </div>
        </a>
        
        <!-- Kunjungan Direncanakan -->
        <a href="{{ route('pembimbing.kunjungan') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kunjungan</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $kunjunganMendatang }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-xl">📍</div>
            </div>
        </a>
        
        <!-- Siswa Absen Hari Ini -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Absen Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600">{{ $siswaAbsenHariIni }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-xl">📅</div>
            </div>
        </div>
    </div>

    <!-- Early Warning System -->
    @if(isset($earlyWarnings) && $earlyWarnings->count() > 0)
    <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-5 animate-in fade-in slide-in-from-bottom-2">
        <h3 class="font-bold text-red-800 mb-4 flex items-center gap-2">
            ⚠️ Perhatian Khusus (Early Warning System)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($earlyWarnings as $warning)
            <div class="bg-white p-3 rounded-lg border border-red-200 flex items-start gap-3 shadow-sm hover:shadow transition">
                <div class="text-2xl mt-0.5">🚨</div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">{{ $warning['nama'] }}</p>
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $warning['masalah'] }}</p>
                    <a href="{{ route('pembimbing.siswa-binaan') }}" class="text-[10px] text-blue-600 hover:underline mt-1 inline-block">Lihat Detail Siswa &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 2 Column Layout for Activity and Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Aktivitas Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-bold text-gray-800 mb-4">⚡ Aktivitas Terbaru</h3>
            <div class="space-y-3">
                @forelse($aktivitasTerbaru as $item)
                    @if($item['type'] === 'absensi')
                        @php $a = $item['data']; @endphp
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">📅</div>
                                <div>
                                    <p class="text-sm font-medium">{{ $a->siswa->name }} - Absensi {{ $a->tanggal->format('d/m') }}</p>
                                    <p class="text-xs text-gray-500">Jam: {{ $a->check_in }} • IP: {{ $a->ip_address }}</p>
                                </div>
                            </div>
                            <form action="{{ route('pembimbing.absensi.verify', $a->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="text-xs bg-orange-600 text-white px-3 py-1.5 rounded hover:bg-orange-700 font-medium active:scale-95 transition">
                                    Verifikasi
                                </button>
                            </form>
                        </div>
                    @else
                        @php $j = $item['data']; @endphp
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">📖</div>
                                <div>
                                    <p class="text-sm font-medium">{{ $j->siswa->name }} - Jurnal {{ $j->tanggal->format('d/m') }}</p>
                                    <p class="text-xs text-gray-500">{{ \Str::limit($j->kegiatan, 50) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.jurnal') }}" class="text-xs bg-yellow-600 text-white px-3 py-1.5 rounded hover:bg-yellow-700">Review</a>
                        </div>
                    @endif
                @empty
                    <p class="text-center text-gray-500 py-4">🎉 Tidak ada aktivitas baru</p>
                @endforelse
            </div>
        </div>

        <!-- Grafik Kehadiran -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-bold text-gray-800 mb-4">📊 Rekapitulasi Kehadiran Siswa Binaan</h3>
            <div class="relative h-72 w-full">
                @if(array_sum($chartAbsensi) > 0)
                    <canvas id="absensiChart" class="w-full h-full"></canvas>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <p class="text-4xl mb-2">📭</p>
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