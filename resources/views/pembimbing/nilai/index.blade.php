@extends('layouts.app')
@section('page-title', 'Nilai Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white drop-shadow-md">🎯 Penilaian Akhir Siswa Binaan</h2>
        <div class="text-sm text-gray-400">Total: {{ $siswaBinaan->total() }} siswa</div>
    </div>

    @if(session('success'))
        <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.2)]">
            <p class="font-bold drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">✅ {{ session('success') }}</p>
        </div>
    @endif

    <!-- Table Siswa untuk Dinilai -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Nilai Akhir</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($siswaBinaan as $sp)
                    @php
                        // Ambil penilaian akhir jika sudah ada
                        $penilaian = \App\Models\PenilaianAkhir::where('siswa_user_id', $sp->user_id)
                            ->where('pembimbing_id', auth()->id())
                            ->first();
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-crypto-accent to-purple-800 flex items-center justify-center text-white font-bold shadow-lg">
                                    {{ strtoupper(substr($sp->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-white">{{ $sp->user->name }}</div>
                                    <div class="text-xs text-gray-400">NIS: {{ $sp->nis }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner">
                                {{ $sp->jurusan->nama }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-300 font-medium">{{ $sp->perusahaan->nama ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($penilaian)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold border shadow-inner {{ 
                                    $penilaian->nilai_akhir >= 80 ? 'bg-crypto-success/20 text-crypto-success border-crypto-success/30' : 
                                    ($penilaian->nilai_akhir >= 70 ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30') 
                                }}">
                                    {{ $penilaian->nilai_akhir }} ({{ $penilaian->grade }})
                                </span>
                            @else
                                <span class="text-xs text-gray-500 italic">Belum dinilai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pembimbing.nilai.create', $sp->user_id) }}" 
                               class="text-crypto-accent hover:text-white text-xs font-bold px-3 py-1.5 rounded hover:bg-white/10 active:scale-95 transition-colors inline-block">
                               {{ $penilaian ? '✏️ Edit Nilai' : '🎯 Beri Nilai' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2 opacity-50 drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">📭</div>
                            <p class="font-medium">Tidak ada siswa binaan untuk dinilai</p>
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