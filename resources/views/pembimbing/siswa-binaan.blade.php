@extends('layouts.app')
@section('page-title', 'Siswa Binaan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white drop-shadow-md">👥 Daftar Siswa Binaan</h2>
        <div class="text-sm text-gray-400 font-medium">Total: {{ $siswaBinaan->total() }} siswa</div>
    </div>

    <!-- Filter Card -->
    <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-300 mb-1">Cari Siswa</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NIS, atau Email" 
                       class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
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
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-crypto-accent text-white px-4 py-2 rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(112,0,255,0.3)] hover:bg-crypto-accentHover transition-colors active:scale-95">🔍 Filter</button>
                <a href="{{ route('pembimbing.siswa-binaan') }}" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-white/20 transition-colors active:scale-95">↺</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">NIS</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($siswaBinaan as $sp)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-crypto-accent to-blue-700 border border-white/10 flex items-center justify-center text-white font-bold drop-shadow-md">
                                    {{ strtoupper(substr($sp->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-200">{{ $sp->user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $sp->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ $sp->nis }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                {{ $sp->jurusan->nama }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-200">{{ $sp->perusahaan->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $sp->perusahaan->alamat ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-crypto-success/20 text-crypto-success border border-crypto-success/30 shadow-inner">
                                Aktif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pembimbing.penilaian.final', $sp->user_id) }}" 
                               class="text-crypto-accent hover:text-white text-xs font-bold px-3 py-1.5 rounded bg-crypto-accent/10 hover:bg-crypto-accent border border-crypto-accent/30 hover:border-crypto-accent transition-colors">
                               🎯 Nilai
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            <div class="text-3xl mb-2 opacity-50 drop-shadow-[0_0_10px_rgba(255,255,255,0.2)]">📭</div>
                            <p>Tidak ada siswa binaan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/10 bg-white/5">
            {{ $siswaBinaan->links() }}
        </div>
    </div>
</div>
@endsection