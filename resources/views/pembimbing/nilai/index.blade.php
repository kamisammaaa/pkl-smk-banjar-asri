@extends('layouts.app')
@section('page-title', 'Nilai Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">🎯 Penilaian Akhir Siswa Binaan</h2>
        <div class="text-sm text-gray-600">Total: {{ $siswaBinaan->total() }} siswa</div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            <p class="font-semibold">✅ {{ session('success') }}</p>
        </div>
    @endif

    <!-- Table Siswa untuk Dinilai -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">Nilai Akhir</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($siswaBinaan as $sp)
                    @php
                        // Ambil penilaian akhir jika sudah ada
                        $penilaian = \App\Models\PenilaianAkhir::where('siswa_user_id', $sp->user_id)
                            ->where('pembimbing_id', auth()->id())
                            ->first();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($sp->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">{{ $sp->user->name }}</div>
                                    <div class="text-xs text-gray-500">NIS: {{ $sp->nis }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $sp->jurusan->nama }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-800">{{ $sp->perusahaan->nama ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($penilaian)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ 
                                    $penilaian->nilai_akhir >= 80 ? 'bg-green-100 text-green-800' : 
                                    ($penilaian->nilai_akhir >= 70 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') 
                                }}">
                                    {{ $penilaian->nilai_akhir }} ({{ $penilaian->grade }})
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dinilai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pembimbing.nilai.create', $sp->user_id) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-3 py-1.5 rounded hover:bg-indigo-50 transition">
                               {{ $penilaian ? '✏️ Edit Nilai' : '🎯 Beri Nilai' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Tidak ada siswa binaan untuk dinilai</p>
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