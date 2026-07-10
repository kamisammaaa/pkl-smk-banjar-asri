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
</div>
@endsection