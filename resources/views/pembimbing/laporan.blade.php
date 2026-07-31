@extends('layouts.app')
@section('page-title', 'Laporan Siswa Binaan')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white drop-shadow-md">📄 Laporan Siswa Binaan</h2>
            <p class="text-sm text-gray-400 mt-1">Rekap kehadiran, jurnal, dan nilai akhir</p>
        </div>
        <div class="flex gap-2 no-print">
            <button onclick="window.print()" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2.5 rounded-lg shadow transition-colors flex items-center gap-2 font-bold text-sm active:scale-95">
                🖨️ Cetak / PDF
            </button>
            <a href="{{ route('pembimbing.laporan.export') }}" class="bg-crypto-success hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.3)] transition-colors flex items-center gap-2 font-bold text-sm active:scale-95">
                📥 Export Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 no-print">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('perusahaan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Jurusan</label>
                <select name="jurusan_id" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('jurusan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Masalah Kehadiran</label>
                <select name="filter_masalah" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Siswa</option>
                    <option value="alpha" {{ request('filter_masalah') == 'alpha' ? 'selected' : '' }}>❌ Ada Alpha</option>
                    <option value="sakit" {{ request('filter_masalah') == 'sakit' ? 'selected' : '' }}>🤒 Ada Sakit</option>
                    <option value="izin" {{ request('filter_masalah') == 'izin' ? 'selected' : '' }}>📝 Ada Izin</option>
                    <option value="terlambat" {{ request('filter_masalah') == 'terlambat' ? 'selected' : '' }}>⏰ Ada Terlambat</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-crypto-accent text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-600 transition-colors font-bold shadow-[0_0_15px_rgba(112,0,255,0.3)]">
                    🔍 Filter
                </button>
                <a href="{{ route('pembimbing.laporan') }}" class="px-4 py-2 bg-white/10 border border-white/20 hover:bg-white/20 rounded-lg text-sm transition-colors text-gray-300 hover:text-white font-bold text-center">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Laporan -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[1100px]">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Industri</th>
                        
                        {{-- ✅ Kolom Breakdown Absensi --}}
                        <th class="px-2 py-3 font-bold text-center text-white drop-shadow-md bg-white/10" colspan="5">📊 Detail Absensi</th>
                        
                        <th class="px-4 py-3 font-semibold text-green-400 text-center bg-green-500/20 shadow-inner">✅ Nilai Hadir</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">📖 Jurnal</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">🎯 Nilai Akhir</th>
                    </tr>
                    <tr class="bg-white/5 border-b border-white/10 text-xs">
                        <th></th><th></th><th></th>
                        {{-- Sub-header untuk breakdown --}}
                        <th class="px-2 py-2 text-center font-bold text-green-400 bg-green-500/20 shadow-inner">✅ Hadir</th>
                        <th class="px-2 py-2 text-center font-bold text-yellow-400 bg-yellow-500/20 shadow-inner">⏰ Terlambat</th>
                        <th class="px-2 py-2 text-center font-bold text-orange-400 bg-orange-500/20 shadow-inner">🤒 Sakit</th>
                        <th class="px-2 py-2 text-center font-bold text-blue-400 bg-blue-500/20 shadow-inner">📝 Izin</th>
                        <th class="px-2 py-2 text-center font-bold text-red-400 bg-red-500/20 shadow-inner">❌ Alpha</th>
                        <th></th><th></th><th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($laporanData as $data)
                    <tr class="hover:bg-white/5 transition-colors">
                        {{-- Nama Siswa --}}
                        <td class="px-4 py-3">
                            <div class="font-bold text-white">{{ $data['siswa']->user->name }}</div>
                            <div class="text-xs text-gray-400">NIS: {{ $data['siswa']->nis }}</div>
                        </td>
                        
                        {{-- Jurusan --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-crypto-accent/20 text-crypto-accent border border-crypto-accent/30 shadow-inner">
                                {{ $data['siswa']->jurusan->nama }}
                            </span>
                        </td>
                        
                        {{-- Industri --}}
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-300 font-medium truncate max-w-[150px]" title="{{ $data['siswa']->perusahaan->nama ?? '-' }}">
                                {{ $data['siswa']->perusahaan->nama ?? '-' }}
                            </div>
                        </td>
                        
                        {{-- ✅ Breakdown Absensi (5 Kolom) --}}
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30 shadow-inner">
                                {{ $data['hadir'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 shadow-inner">
                                {{ $data['terlambat'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30 shadow-inner">
                                {{ $data['sakit'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner">
                                {{ $data['izin'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30 shadow-inner">
                                {{ $data['alpha'] }}
                            </span>
                        </td>
                        
                        {{-- Nilai Kehadiran --}}
                        <td class="px-4 py-3 text-center">
                            @if($data['total_absen'] > 0)
                                <div class="text-sm font-bold {{ $data['nilai_kehadiran'] >= 80 ? 'text-green-400 drop-shadow-[0_0_5px_rgba(34,197,94,0.5)]' : ($data['nilai_kehadiran'] >= 60 ? 'text-yellow-400 drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]' : 'text-red-400 drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]') }}">
                                    {{ $data['nilai_kehadiran'] }}%
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Total: {{ $data['total_absen'] }} hari</div>
                            @else
                                <div class="text-sm font-semibold text-gray-400">Belum ada data</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Absensi kosong</div>
                            @endif
                        </td>
                        
                        {{-- Nilai Jurnal --}}
                        <td class="px-4 py-3 text-center">
                            @if($data['total_jurnal'] > 0)
                                <div class="text-sm font-bold {{ $data['nilai_jurnal'] >= 80 ? 'text-crypto-success drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]' : ($data['nilai_jurnal'] >= 60 ? 'text-blue-400 drop-shadow-[0_0_5px_rgba(59,130,246,0.5)]' : 'text-yellow-400 drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]') }}">
                                    {{ $data['nilai_jurnal'] }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $data['total_jurnal'] }} disetujui</div>
                            @else
                                <div class="text-sm font-semibold text-gray-400">Belum ada data</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Jurnal kosong</div>
                            @endif
                        </td>
                        
                        {{-- Nilai Akhir --}}
                        <td class="px-4 py-3 text-center">
                            @if($data['nilai_akhir'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-inner
                                    {{ $data['nilai_akhir'] >= 85 ? 'bg-crypto-success/20 text-crypto-success border-crypto-success/30' : 
                                       ($data['nilai_akhir'] >= 70 ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 
                                       ($data['nilai_akhir'] >= 60 ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30')) }}">
                                    {{ $data['nilai_akhir'] }} ({{ $data['grade'] }})
                                </span>
                            @else
                                <span class="text-xs text-gray-500 italic">Belum dinilai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-4xl opacity-50 drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">📭</div>
                            <p class="text-gray-300 font-medium">Tidak ada data laporan</p>
                            <p class="text-xs text-gray-400 mt-1">Pastikan siswa sudah di-assign ke Anda sebagai pembimbing</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Summary Footer --}}
        @if(count($laporanData) > 0)
        <div class="px-4 py-3 border-t border-white/10 bg-white/5 text-xs text-gray-400 flex justify-between">
            <span>Total siswa: <strong>{{ count($laporanData) }}</strong></span>
            <span>Data diperbarui: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
        @endif
    </div>
    
    {{-- Info Box --}}
    <div class="glass-panel border-l-4 border-blue-500/50 p-4 rounded-r-lg shadow-[0_0_15px_rgba(59,130,246,0.2)]">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-200">
                    <strong class="text-white drop-shadow-md">💡 Cara Hitung Nilai Kehadiran:</strong><br>
                    ✅ Hadir tepat waktu = 100 poin • ⏰ Terlambat = 70 poin<br>
                    🤒 Sakit / 📝 Izin = 100 poin • ❌ Alpha = 0 poin<br>
                    Nilai akhir = Rata-rata tertimbang dari semua hari absensi yang tersedia.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body { font-size: 11pt; }
    table { width: 100% !important; font-size: 10pt; color: black !important; }
    th, td { padding: 4px 2px !important; color: black !important; }
    /* Ensure texts and borders print properly without dark theme interference */
    * { text-shadow: none !important; box-shadow: none !important; }
    .text-white, .text-gray-300, .text-gray-400 { color: black !important; }
    .bg-white\/5, .bg-white\/10 { background: none !important; border-color: #ddd !important; }
    .border-white\/10, .border-white\/5, .border-white\/20, .divide-white\/5 > * { border-color: #ddd !important; }
    
    .bg-green-500\/20 { background: #f0fdf4 !important; border: 1px solid #ddd !important; color: #166534 !important; }
    .bg-yellow-500\/20 { background: #fefce8 !important; border: 1px solid #ddd !important; color: #854d0e !important; }
    .bg-orange-500\/20 { background: #fff7ed !important; border: 1px solid #ddd !important; color: #9a3412 !important; }
    .bg-blue-500\/20 { background: #eff6ff !important; border: 1px solid #ddd !important; color: #1e40af !important; }
    .bg-red-500\/20 { background: #fef2f2 !important; border: 1px solid #ddd !important; color: #991b1b !important; }
    .bg-crypto-accent\/20 { background: #faf5ff !important; border: 1px solid #ddd !important; color: #6b21a8 !important; }
    .bg-crypto-success\/20 { background: #f0fdf4 !important; border: 1px solid #ddd !important; color: #166534 !important; }
    
    .text-green-400 { color: #166534 !important; }
    .text-yellow-400 { color: #854d0e !important; }
    .text-orange-400 { color: #9a3412 !important; }
    .text-blue-400 { color: #1e40af !important; }
    .text-red-400 { color: #991b1b !important; }
    .text-crypto-success { color: #166534 !important; }
    .text-crypto-accent { color: #6b21a8 !important; }
}
/* Hide scrollbar for cleaner look */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
</style>
@endsection