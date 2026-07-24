@extends('layouts.app')
@section('page-title', 'Rekap Absensi')

@section('content')
<div class="space-y-6">
    
    {{-- Header dengan Tombol Export --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">📊 Rekap Absensi Siswa</h2>
            <p class="text-sm text-gray-500 mt-1">Ringkasan kehadiran siswa per bulan · Persentase dihitung dari <strong>hari aktif</strong> (tidak termasuk hari libur)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.rekap-absensi.export', request()->query()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                📥 Export CSV
            </a>
        </div>
    </div>
    
    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Pembimbing</label>
                <select name="pembimbing_id" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua</option>
                    @foreach($pembimbingList as $pb)
                        <option value="{{ $pb->id }}" {{ request('pembimbing_id') == $pb->id ? 'selected' : '' }}>
                            {{ $pb->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jurusan</label>
                <select name="jurusan_id" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Masalah Kehadiran</label>
                <select name="filter_masalah" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua Siswa</option>
                    <option value="alpha" {{ request('filter_masalah') == 'alpha' ? 'selected' : '' }}>❌ Ada Alpha</option>
                    <option value="sakit" {{ request('filter_masalah') == 'sakit' ? 'selected' : '' }}>🤒 Ada Sakit</option>
                    <option value="izin" {{ request('filter_masalah') == 'izin' ? 'selected' : '' }}>📝 Ada Izin</option>
                    <option value="terlambat" {{ request('filter_masalah') == 'terlambat' ? 'selected' : '' }}>⏰ Ada Terlambat</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition font-medium">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.rekap-absensi.index') }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition text-gray-700 font-medium text-center">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[900px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold">Jurusan</th>
                        <th class="px-4 py-3 font-semibold">Pembimbing</th>
                        <th class="px-4 py-3 text-center font-semibold text-green-700 bg-green-50">✅ Hadir</th>
                        <th class="px-4 py-3 text-center font-semibold text-yellow-600 bg-yellow-50">⏰ Terlambat</th>
                        <th class="px-4 py-3 text-center font-semibold text-orange-700 bg-orange-50">🤒 Sakit</th>
                        <th class="px-4 py-3 text-center font-semibold text-blue-700 bg-blue-50">📝 Izin</th>
                        <th class="px-4 py-3 text-center font-semibold text-purple-700 bg-purple-50">🏖️ Libur</th>
                        <th class="px-4 py-3 text-center font-semibold text-red-700 bg-red-50">❌ Alpha</th>
                        <th class="px-4 py-3 text-center font-semibold">Hari Aktif</th>
                        <th class="px-4 py-3 text-center font-semibold">% Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekap as $r)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium">{{ $r['siswa']->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $r['siswa']->siswaProfile?->jurusan?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $r['siswa']->siswaProfile?->pembimbing?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center bg-green-50 font-bold text-green-700">{{ $r['hadir'] }}</td>
                        <td class="px-4 py-3 text-center bg-yellow-50 font-bold text-yellow-700">{{ $r['terlambat'] }}</td>
                        <td class="px-4 py-3 text-center bg-orange-50">{{ $r['sakit'] }}</td>
                        <td class="px-4 py-3 text-center bg-blue-50">{{ $r['izin'] }}</td>
                        <td class="px-4 py-3 text-center bg-purple-50 text-purple-700">{{ $r['libur'] }}</td>
                        <td class="px-4 py-3 text-center bg-red-50 font-bold text-red-700">{{ $r['alpha'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $r['hari_aktif'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $pct = $r['persentase'];
                                $color = match(true) {
                                    $pct >= 90 => 'bg-green-100 text-green-800',
                                    $pct >= 75 => 'bg-blue-100 text-blue-800',
                                    $pct >= 50 => 'bg-yellow-100 text-yellow-800',
                                    $pct > 0   => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $color }}">
                                {{ $pct }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-gray-400 mb-2 text-3xl">📭</div>
                            <p>Tidak ada data rekap absensi</p>
                            @if(request('bulan') || request('pembimbing_id') || request('jurusan_id'))
                                <a href="{{ route('admin.rekap-absensi.index') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Reset filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Info Box --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg text-sm text-blue-700">
        <strong>💡 Keterangan:</strong>
        <ul class="list-disc list-inside mt-1 space-y-0.5">
            <li><strong>Hadir</strong> = Hadir tepat waktu (sebelum batas jam masuk + toleransi industri)</li>
            <li><strong>Terlambat</strong> = Hadir namun melewati jam masuk + toleransi industri</li>
            <li><strong>Hari Aktif</strong> = Total hari absensi dikurangi hari Libur 🏖️</li>
            <li><strong>% Hadir</strong> = (Hadir + Terlambat ÷ Hari Aktif) × 100</li>
            <li>Hari <strong>Libur</strong> tidak dihitung sebagai hari aktif sehingga tidak mempengaruhi persentase kehadiran</li>
            <li>Filter yang aktif diterapkan juga pada hasil Export CSV</li>
        </ul>
    </div>
</div>
@endsection