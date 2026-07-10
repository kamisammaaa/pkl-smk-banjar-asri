@extends('layouts.app')
@section('page-title', 'Verifikasi Absensi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">✅ Monitoring Verifikasi Absensi</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar absensi siswa yang telah diverifikasi oleh pembimbing masing-masing</p>
        </div>
    </div>
    
    <!-- Table & Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[800px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Jam Check-In</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">IP Address</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">EOTAG Token</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($absensiVerified as $a)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-4 py-3 text-gray-800 font-medium">
                            {{ $a->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ $a->siswa->name }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $a->check_in }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                            {{ $a->ip_address }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 font-mono text-xs bg-gray-100 text-gray-700 rounded border border-gray-200">
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
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <div class="text-gray-400 text-4xl mb-3">🔍</div>
                            <div class="text-base font-semibold text-gray-700">Belum Ada Absensi Terverifikasi</div>
                            <div class="text-xs text-gray-400 mt-1">Daftar ini akan terisi setelah pembimbing melakukan verifikasi kehadiran siswa.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($absensiVerified->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $absensiVerified->links() }}
        </div>
        @endif
    </div>
</div>
@endsection