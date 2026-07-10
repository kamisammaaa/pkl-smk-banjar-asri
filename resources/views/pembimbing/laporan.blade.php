@extends('layouts.app')
@section('page-title', 'Laporan Siswa Binaan')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📄 Laporan Siswa Binaan</h2>
            <p class="text-sm text-gray-500 mt-1">Rekap kehadiran, jurnal, dan nilai akhir</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                🖨️ Cetak / PDF
            </button>
            <a href="{{ route('pembimbing.laporan.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                📥 Export Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('perusahaan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('jurusan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition font-medium">
                    🔍 Filter
                </button>
                <a href="{{ route('pembimbing.laporan') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition text-gray-700 font-medium text-center">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Laporan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[1100px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Industri</th>
                        
                        {{-- ✅ Kolom Breakdown Absensi --}}
                        <th class="px-2 py-3 font-semibold text-center text-gray-700 bg-gray-100" colspan="5">📊 Detail Absensi</th>
                        
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center bg-green-50">✅ Nilai Hadir</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">📖 Jurnal</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">🎯 Nilai Akhir</th>
                    </tr>
                    <tr class="bg-gray-50 border-b text-xs">
                        <th></th><th></th><th></th>
                        {{-- Sub-header untuk breakdown --}}
                        <th class="px-2 py-2 text-center font-medium text-green-700 bg-green-50">✅ Hadir</th>
                        <th class="px-2 py-2 text-center font-medium text-yellow-700 bg-yellow-50">⏰ Terlambat</th>
                        <th class="px-2 py-2 text-center font-medium text-orange-700 bg-orange-50">🤒 Sakit</th>
                        <th class="px-2 py-2 text-center font-medium text-blue-700 bg-blue-50">📝 Izin</th>
                        <th class="px-2 py-2 text-center font-medium text-red-700 bg-red-50">❌ Alpha</th>
                        <th></th><th></th><th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporanData as $data)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Nama Siswa --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $data['siswa']->user->name }}</div>
                            <div class="text-xs text-gray-500">NIS: {{ $data['siswa']->nis }}</div>
                        </td>
                        
                        {{-- Jurusan --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $data['siswa']->jurusan->nama }}
                            </span>
                        </td>
                        
                        {{-- Industri --}}
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-800 truncate max-w-[150px]" title="{{ $data['siswa']->perusahaan->nama ?? '-' }}">
                                {{ $data['siswa']->perusahaan->nama ?? '-' }}
                            </div>
                        </td>
                        
                        {{-- ✅ Breakdown Absensi (5 Kolom) --}}
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                {{ $data['hadir'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                {{ $data['terlambat'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                {{ $data['sakit'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                {{ $data['izin'] }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                {{ $data['alpha'] }}
                            </span>
                        </td>
                        
                        {{-- Nilai Kehadiran --}}
                        <td class="px-4 py-3 text-center">
                            <div class="text-sm font-bold {{ $data['nilai_kehadiran'] >= 80 ? 'text-green-600' : ($data['nilai_kehadiran'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $data['nilai_kehadiran'] }}%
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Total: {{ $data['total_absen'] }} hari</div>
                        </td>
                        
                        {{-- Nilai Jurnal --}}
                        <td class="px-4 py-3 text-center">
                            <div class="text-sm font-bold {{ $data['nilai_jurnal'] >= 80 ? 'text-green-600' : ($data['nilai_jurnal'] >= 60 ? 'text-blue-600' : 'text-yellow-600') }}">
                                {{ $data['nilai_jurnal'] }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $data['total_jurnal'] }} disetujui</div>
                        </td>
                        
                        {{-- Nilai Akhir --}}
                        <td class="px-4 py-3 text-center">
                            @if($data['nilai_akhir'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                    {{ $data['nilai_akhir'] >= 85 ? 'bg-green-100 text-green-800' : 
                                       ($data['nilai_akhir'] >= 70 ? 'bg-blue-100 text-blue-800' : 
                                       ($data['nilai_akhir'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ $data['nilai_akhir'] }} ({{ $data['grade'] }})
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dinilai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-4xl">📭</div>
                            <p class="text-gray-500 font-medium">Tidak ada data laporan</p>
                            <p class="text-xs text-gray-400 mt-1">Pastikan siswa sudah di-assign ke Anda sebagai pembimbing</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Summary Footer --}}
        @if(count($laporanData) > 0)
        <div class="px-4 py-3 border-t bg-gray-50 text-xs text-gray-600 flex justify-between">
            <span>Total siswa: <strong>{{ count($laporanData) }}</strong></span>
            <span>Data diperbarui: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
        @endif
    </div>
    
    {{-- Info Box --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>💡 Cara Hitung Nilai Kehadiran:</strong><br>
                    ✅ Hadir tepat waktu = 100 poin • ⏰ Terlambat = 70 poin<br>
                    🤒 Sakit / 📝 Izin = 50 poin • ❌ Alpha = 0 poin<br>
                    Nilai akhir = Rata-rata tertimbang dari semua hari absensi.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .sidebar, header, button, .no-print { display: none !important; }
    .main-wrapper { margin-left: 0 !important; padding: 0 !important; }
    body { background: white; font-size: 11pt; }
    table { width: 100% !important; font-size: 10pt; }
    th, td { padding: 4px 2px !important; }
    /* Ensure breakdown columns print properly */
    .bg-green-50, .bg-yellow-50, .bg-orange-50, .bg-blue-50, .bg-red-50 { 
        background: none !important; 
        border: 1px solid #ddd !important;
    }
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