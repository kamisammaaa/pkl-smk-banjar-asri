@extends('layouts.app')
@section('page-title', 'Monitoring Kunjungan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white drop-shadow-md">👁️ Monitoring Kunjungan Industri</h2>
        <div class="text-sm text-gray-400">
            Total: {{ $kunjungans->total() }} kunjungan
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-panel p-4 rounded-xl shadow-sm border border-white/5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Pembimbing</label>
                <select name="pembimbing_id" class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Pembimbing</option>
                    @foreach($pembimbingList as $id => $nama)
                        <option value="{{ $id }}" {{ request('pembimbing_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('perusahaan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">🔍 Filter</button>
                <a href="{{ route('admin.monitoring.kunjungan') }}" class="px-4 py-2 glass-panel/10 rounded-lg text-sm hover:bg-gray-200 transition" title="Reset Filter">↺</a>
                <a href="{{ route('admin.monitoring.kunjungan.pdf', request()->query()) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition flex items-center gap-1 shadow-lg" title="Cetak PDF">
                    🖨️ PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-panel p-4 rounded-xl shadow-sm border border-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Kunjungan Bulan Ini</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['bulan_ini'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-xl">📅</div>
            </div>
        </div>
        <div class="glass-panel p-4 rounded-xl shadow-sm border border-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Siswa Dikunjungi</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['siswa_dikunjungi'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-xl">👥</div>
            </div>
        </div>
        <div class="glass-panel p-4 rounded-xl shadow-sm border border-white/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Perusahaan Terjangkau</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['perusahaan'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-xl">🏢</div>
            </div>
        </div>
    </div>

    <!-- Table Kunjungan -->
    <div class="glass-panel rounded-xl shadow-sm border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="glass-panel/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Pembimbing</th>
                        <th class="px-4 py-3 font-semibold">Siswa</th>
                        <th class="px-4 py-3 font-semibold">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold">Catatan</th>
                        <th class="px-4 py-3 font-semibold text-center">Foto</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($kunjungans as $k)
                    <tr class="hover:glass-panel/5 {{ $k->status === 'rencana' ? 'bg-blue-500/10' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $k->tanggal->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $k->waktu }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $k->pembimbing->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($k->siswa)
                                <div class="font-medium">{{ $k->siswa->name }}</div>
                                <div class="text-xs text-gray-400">{{ $k->siswa->siswaProfile?->nis ?? '' }}</div>
                            @else
                                @php
                                    $siswaBinaanDiPerusahaan = $k->perusahaan 
                                        ? $k->perusahaan->siswaProfiles->where('pembimbing_id', $k->pembimbing_id) 
                                        : collect();
                                @endphp
                                @forelse($siswaBinaanDiPerusahaan as $sp)
                                    <div class="font-medium text-xs text-white drop-shadow-md">• {{ $sp->user->name ?? '-' }}</div>
                                @empty
                                    <span class="text-gray-400 text-xs">—</span>
                                @endforelse
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">{{ $k->perusahaan->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-400 line-clamp-1">{{ $k->perusahaan->alamat ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($k->status === 'rencana')
                                <p class="text-xs text-blue-400 bg-blue-500/10 p-2 rounded">
                                    <strong>Rencana:</strong> {{ \Str::limit($k->catatan_rencana, 100) }}
                                </p>
                            @else
                                <p class="text-xs text-gray-300 glass-panel/5 p-2 rounded">
                                    <strong>Hasil:</strong> {{ \Str::limit($k->catatan, 100) }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->foto)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($k->foto) }}" target="_blank" class="text-blue-600 hover:underline text-xs">📷 Lihat</a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->status === 'rencana')
                                <span class="px-2 py-1 rounded text-xs bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner font-medium">Rencana</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs bg-green-500/20 text-green-400 border border-green-500/30 shadow-inner font-medium">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Belum ada data kunjungan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/5 glass-panel/5">{{ $kunjungans->links() }}</div>
    </div>
</div>
@endsection