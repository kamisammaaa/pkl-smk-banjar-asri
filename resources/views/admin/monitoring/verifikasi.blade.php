@extends('layouts.app')
@section('page-title', 'Verifikasi Absensi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white drop-shadow-md">✅ Monitoring Verifikasi Absensi</h2>
            <p class="text-sm text-gray-400 mt-1">Daftar absensi siswa yang telah diverifikasi oleh pembimbing masing-masing</p>
        </div>
    </div>
    
    <!-- Table & Cards -->
    <div class="glass-panel rounded-xl shadow-sm border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[800px]">
                <thead class="glass-panel/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Jam Check-In</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">IP Address</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">EOTAG Token</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($absensiVerified as $a)
                    <tr class="hover:glass-panel/5/70 transition">
                        <td class="px-4 py-3 text-white drop-shadow-md font-medium">
                            {{ $a->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-white drop-shadow-md">
                            {{ $a->siswa->name }}
                        </td>
                        <td class="px-4 py-3 text-gray-400">
                            {{ $a->check_in }}
                        </td>
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                            {{ $a->ip_address }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 font-mono text-xs glass-panel/10 text-gray-300 rounded border border-white/5">
                                {{ $a->eotag_token }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-green-600 font-medium">
                            @php $profile = $a->siswa->siswaProfile; @endphp
                            👤 {{ $profile?->pembimbing?->name ?? 'Sistem' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <div class="text-gray-400 text-4xl mb-3">🔍</div>
                            <div class="text-base font-semibold text-gray-300">Belum Ada Absensi Terverifikasi</div>
                            <div class="text-xs text-gray-400 mt-1">Daftar ini akan terisi setelah pembimbing melakukan verifikasi kehadiran siswa.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($absensiVerified->hasPages())
        <div class="px-4 py-3 border-t glass-panel/5">
            {{ $absensiVerified->links() }}
        </div>
        @endif
    </div>
</div>
@endsection