@extends('layouts.app')
@section('page-title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white shadow-lg">
        <h2 class="text-xl font-bold">👋 Halo, {{ $infoSiswa['nama'] }}!</h2>
        <p class="mt-1 text-blue-100">{{ $infoSiswa['jurusan'] }} | NIS: {{ $infoSiswa['nis'] }}</p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('siswa.absensi.index') }}" class="bg-white text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 transition active:scale-95">📅 Absensi Sekarang</a>
            <a href="{{ route('siswa.jurnal.index') }}" class="bg-blue-700 bg-opacity-50 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-opacity-70 transition active:scale-95">📖 Isi Jurnal</a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm mb-1">🏢 Perusahaan PKL</div>
            <div class="text-base font-bold text-gray-800 truncate">{{ $infoSiswa['perusahaan'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm mb-1">👨‍🏫 Pembimbing</div>
            <div class="text-base font-bold text-gray-800 truncate">{{ $infoSiswa['pembimbing'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm mb-1">📅 Bulan Ini</div>
            <div class="text-base font-bold text-gray-800">{{ now()->format('F Y') }}</div>
        </div>
    </div>

    <!-- STATISTIK ABSENSI -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            📊 Statistik Absensi <span class="text-sm font-normal text-gray-500">(Bulan Ini)</span>
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-green-700">{{ $statsAbsensi['hadir'] }}</div>
                <div class="text-xs text-green-600 mt-1">✅ Hadir</div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-yellow-700">{{ $statsAbsensi['terlambat'] }}</div>
                <div class="text-xs text-yellow-600 mt-1">⏰ Terlambat</div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-blue-700">{{ $statsAbsensi['izin'] }}</div>
                <div class="text-xs text-blue-600 mt-1">📝 Izin</div>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-orange-700">{{ $statsAbsensi['sakit'] }}</div>
                <div class="text-xs text-orange-600 mt-1">🤒 Sakit</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-red-700">{{ $statsAbsensi['alpha'] }}</div>
                <div class="text-xs text-red-600 mt-1">❌ Alpha</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-gray-700">{{ $statsAbsensi['total'] }}</div>
                <div class="text-xs text-gray-600 mt-1">📅 Total</div>
            </div>
        </div>
    </div>

    <!-- STATISTIK JURNAL -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            📖 Statistik Jurnal <span class="text-sm font-normal text-gray-500">(Bulan Ini)</span>
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-green-700">{{ $statsJurnal['disetujui'] }}</div>
                <div class="text-xs text-green-600 mt-1">✅ Disetujui</div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-yellow-700">{{ $statsJurnal['menunggu'] }}</div>
                <div class="text-xs text-yellow-600 mt-1">⏳ Menunggu</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-red-700">{{ $statsJurnal['revisi'] }}</div>
                <div class="text-xs text-red-600 mt-1">🔄 Revisi</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-gray-700">{{ $statsJurnal['total'] }}</div>
                <div class="text-xs text-gray-600 mt-1">📝 Total Jurnal</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- PENGUMUMAN TERBARU -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">📢 Pengumuman Terbaru</h3>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $pengumuman->count() }}</span>
            </div>
            <div class="divide-y max-h-96 overflow-y-auto">
                @forelse($pengumuman as $p)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-lg flex-shrink-0">📣</div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-800 text-sm truncate">{{ $p->judul }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $p->admin->name }} • {{ $p->published_at?->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ Str::limit($p->isi, 100) }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-2">📭</div>
                    <p class="text-sm">Belum ada pengumuman</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- JADWAL KUNJUNGAN -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">👁️ Jadwal Kunjungan</h3>
                @if($kunjunganMendatang)
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Akan Datang</span>
                @endif
            </div>
            
            @if($kunjunganMendatang)
            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-2xl">📅</div>
                    <div class="flex-1">
                        <p class="font-bold text-green-800 text-sm">Kunjungan Berikutnya</p>
                        <p class="text-green-700 text-sm mt-0.5">{{ $kunjunganMendatang->tanggal->format('d/m/Y') }}</p>
                        <p class="text-green-600 text-xs mt-1">👨‍🏫 {{ $kunjunganMendatang->pembimbing?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="divide-y max-h-96 overflow-y-auto">
                @forelse($kunjungans as $k)
                <div class="p-4 hover:bg-gray-50 transition {{ $k->tanggal >= now() ? 'bg-blue-50/30' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full {{ $k->tanggal >= now() ? 'bg-blue-100' : 'bg-gray-100' }} flex items-center justify-center text-lg flex-shrink-0">
                            {{ $k->tanggal >= now() ? '🔜' : '✅' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $k->tanggal->format('d/m/Y') }}</h4>
                                @if($k->tanggal >= now())
                                    <span class="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">Akan datang</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">Selesai</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">🏢 {{ $k->perusahaan?->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-600 mt-1">👨‍🏫 {{ $k->pembimbing?->name ?? '-' }}</p>
                            @if($k->catatan)
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ Str::limit($k->catatan, 80) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-2">📭</div>
                    <p class="text-sm">Belum ada jadwal kunjungan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection