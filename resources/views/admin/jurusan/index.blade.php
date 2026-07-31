@extends('layouts.app')
@section('page-title', 'Kelola Jurusan')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white drop-shadow-md">🎓 Kelola Jurusan</h2>
            <p class="text-sm text-gray-400 mt-1">Kelola data kompetensi keahlian / jurusan sekolah</p>
        </div>
    </div>

    @if(session('success')) 
        <div class="glass-panel border-l-4 border-green-500 p-4 rounded-xl shadow-[0_0_15px_rgba(34,197,94,0.1)]">
            <div class="flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <p class="font-bold text-green-400">{!! session('success') !!}</p>
            </div>
        </div> 
    @endif

    <!-- Form Tambah Jurusan -->
    <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <h3 class="text-sm font-semibold text-gray-300 mb-3">➕ Tambah Jurusan Baru</h3>
        <form action="{{ route('admin.jurusan.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="flex-1">
                <input 
                    type="text" 
                    name="nama" 
                    placeholder="Nama Jurusan (contoh: Teknik Komputer dan Jaringan)" 
                    required 
                    class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                >
            </div>
            <button type="submit" class="bg-crypto-accent hover:bg-purple-600 text-white px-6 py-2.5 rounded-lg shadow-[0_0_15px_rgba(112,0,255,0.3)] font-bold text-sm transition active:scale-95 whitespace-nowrap">
                💾 Simpan Jurusan
            </button>
        </form>
    </div>

    <!-- Table List Jurusan -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Nama Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($jurusan as $j)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3 font-bold text-white">{{ $j->nama }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.jurusan.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus jurusan {{ $j->nama }}?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/30 border border-red-500/20 shadow-inner transition-colors">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-12 text-center">
                            <div class="text-4xl mb-2 opacity-50 drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">📭</div>
                            <p class="text-gray-300 font-medium">Belum ada data jurusan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection