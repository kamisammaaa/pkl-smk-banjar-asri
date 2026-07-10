@extends('layouts.app')
@section('page-title', 'Siswa Binaan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">👥 Daftar Siswa Binaan</h2>
        <div class="text-sm text-gray-600">Total: {{ $siswaBinaan->total() }} siswa</div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Siswa</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NIS, atau Email" 
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('perusahaan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $id => $nama)
                        <option value="{{ $id }}" {{ request('jurusan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">🔍 Filter</button>
                <a href="{{ route('pembimbing.siswa-binaan') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 transition">↺</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">NIS</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($siswaBinaan as $sp)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($sp->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">{{ $sp->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $sp->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $sp->nis }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $sp->jurusan->nama }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-800">{{ $sp->perusahaan->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $sp->perusahaan->alamat ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pembimbing.penilaian.final', $sp->user_id) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-3 py-1.5 rounded hover:bg-indigo-50 transition">
                               🎯 Nilai
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Tidak ada siswa binaan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $siswaBinaan->links() }}
        </div>
    </div>
</div>
@endsection