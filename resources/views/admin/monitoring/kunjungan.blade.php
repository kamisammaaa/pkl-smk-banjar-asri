@extends('layouts.app')
@section('page-title', 'Monitoring Kunjungan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">👁️ Monitoring Kunjungan Industri</h2>
        <div class="text-sm text-gray-600">
            Total: {{ $kunjungans->total() }} kunjungan
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pembimbing</label>
                <select name="pembimbing_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Pembimbing</option>
                    @foreach($pembimbingList as $id => $nama)
                        <option value="{{ $id }}" {{ request('pembimbing_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('perusahaan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">🔍 Filter</button>
                <a href="{{ route('admin.monitoring.kunjungan') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">↺</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kunjungan Bulan Ini</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['bulan_ini'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-xl">📅</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Siswa Dikunjungi</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['siswa_dikunjungi'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-xl">👥</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Perusahaan Terjangkau</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['perusahaan'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-xl">🏢</div>
            </div>
        </div>
    </div>

    <!-- Table Kunjungan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
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
                <tbody class="divide-y">
                    @forelse($kunjungans as $k)
                    <tr class="hover:bg-gray-50 {{ $k->status === 'rencana' ? 'bg-blue-50/20' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $k->tanggal->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $k->waktu }}</div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $k->pembimbing->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($k->siswa)
                                <div class="font-medium">{{ $k->siswa->name }}</div>
                                <div class="text-xs text-gray-500">{{ $k->siswa->siswaProfile?->nis ?? '' }}</div>
                            @else
                                @php
                                    $siswaBinaanDiPerusahaan = $k->perusahaan 
                                        ? $k->perusahaan->siswaProfiles->where('pembimbing_id', $k->pembimbing_id) 
                                        : collect();
                                @endphp
                                @forelse($siswaBinaanDiPerusahaan as $sp)
                                    <div class="font-medium text-xs text-gray-800">• {{ $sp->user->name ?? '-' }}</div>
                                @empty
                                    <span class="text-gray-400 text-xs">—</span>
                                @endforelse
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">{{ $k->perusahaan->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500 line-clamp-1">{{ $k->perusahaan->alamat ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($k->status === 'rencana')
                                <p class="text-xs text-blue-800 bg-blue-50 p-2 rounded">
                                    <strong>Rencana:</strong> {{ \Str::limit($k->catatan_rencana, 100) }}
                                </p>
                            @else
                                <p class="text-xs text-gray-700 bg-gray-50 p-2 rounded">
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
                                <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 font-medium">Rencana</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800 font-medium">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Belum ada data kunjungan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t bg-gray-50">{{ $kunjungans->links() }}</div>
    </div>
</div>
@endsection