@extends('layouts.app')
@section('page-title', 'Rekap Absensi')

@section('content')
<div class="space-y-6">
    
    {{-- Header dengan Tombol Export --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white drop-shadow-md">📊 Rekap Absensi Siswa</h2>
            <p class="text-sm text-gray-400 mt-1">Ringkasan kehadiran siswa per bulan · Persentase dihitung dari <strong>hari aktif</strong> (tidak termasuk hari libur)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.rekap-absensi.export', request()->query()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95" title="Export ke Excel/CSV">
                📥 CSV
            </a>
            <a href="{{ route('admin.rekap-absensi.export-pdf', request()->query()) }}" target="_blank"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95 border border-red-500/30" title="Cetak PDF">
                🖨️ PDF
            </a>
        </div>
    </div>
    
    <!-- Filter Card -->
    <div class="glass-panel p-4 rounded-xl shadow-sm border border-white/5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Bulan</label>
                <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" 
                       class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Pembimbing</label>
                <select name="pembimbing_id" 
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua</option>
                    @foreach($pembimbingList as $pb)
                        <option value="{{ $pb->id }}" {{ request('pembimbing_id') == $pb->id ? 'selected' : '' }}>
                            {{ $pb->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Jurusan</label>
                <select name="jurusan_id" 
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Masalah Kehadiran</label>
                <select name="filter_masalah" 
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
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
                   class="px-4 py-2 glass-panel/10 hover:bg-gray-200 rounded-lg text-sm transition text-gray-300 font-medium text-center">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-xl shadow-sm border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[900px]">
                <thead class="glass-panel/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold">Jurusan</th>
                        <th class="px-4 py-3 font-semibold">Pembimbing</th>
                        <th class="px-4 py-3 text-center font-semibold text-green-400 bg-green-500/10">✅ Hadir</th>
                        <th class="px-4 py-3 text-center font-semibold text-yellow-400 bg-yellow-500/10">⏰ Terlambat</th>
                        <th class="px-4 py-3 text-center font-semibold text-orange-400 bg-orange-500/10">🤒 Sakit</th>
                        <th class="px-4 py-3 text-center font-semibold text-blue-400 bg-blue-500/10">📝 Izin</th>
                        <th class="px-4 py-3 text-center font-semibold text-purple-400 bg-purple-500/10">🏖️ Libur</th>
                        <th class="px-4 py-3 text-center font-semibold text-red-400 bg-red-500/10">❌ Alpha</th>
                        <th class="px-4 py-3 text-center font-semibold">Hari Aktif</th>
                        <th class="px-4 py-3 text-center font-semibold">% Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($rekap as $r)
                    <tr class="hover:glass-panel/10 transition">
                        <td class="px-4 py-3 font-medium">{{ $r['siswa']->name }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $r['siswa']->siswaProfile?->jurusan?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $r['siswa']->siswaProfile?->pembimbing?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center bg-green-500/10 font-bold text-green-400">{{ $r['hadir'] }}</td>
                        <td class="px-4 py-3 text-center bg-yellow-500/10 font-bold text-yellow-400">{{ $r['terlambat'] }}</td>
                        <td class="px-4 py-3 text-center bg-orange-500/10 text-orange-400">{{ $r['sakit'] }}</td>
                        <td class="px-4 py-3 text-center bg-blue-500/10 text-blue-400">{{ $r['izin'] }}</td>
                        <td class="px-4 py-3 text-center bg-purple-500/10 text-purple-400">{{ $r['libur'] }}</td>
                        <td class="px-4 py-3 text-center bg-red-500/10 font-bold text-red-400">{{ $r['alpha'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-300">{{ $r['hari_aktif'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $pct = $r['persentase'];
                                $color = match(true) {
                                    $pct >= 90 => 'bg-green-500/20 text-green-400 border border-green-500/30 shadow-inner',
                                    $pct >= 75 => 'bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner',
                                    $pct >= 50 => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 shadow-inner',
                                    $pct > 0   => 'bg-red-100 text-red-700',
                                    default    => 'glass-panel/10 text-gray-400',
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $color }}">
                                {{ $pct }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-400">
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
    <div class="glass-panel border-l-4 border-blue-500 p-4 rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.1)] text-sm text-blue-400">
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