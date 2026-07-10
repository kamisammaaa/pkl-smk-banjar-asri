@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">👁️ Monitoring Kunjungan Pembimbing</h2>
    
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50"><tr><th class="p-3">Tanggal</th><th class="p-3">Pembimbing</th><th class="p-3">Siswa</th><th class="p-3">Perusahaan</th><th class="p-3">Catatan</th><th class="p-3">Foto</th></tr></thead>
            <tbody>
                @forelse($kunjungans as $k)
                <tr class="border-t">
                    <td class="p-3">{{ $k->tanggal }}</td>
                    <td class="p-3">{{ $k->pembimbing->name ?? '-' }}</td>
                    <td class="p-3">
                        @if($k->siswa)
                            {{ $k->siswa->name }}
                        @else
                            @php
                                $siswaBinaanDiPerusahaan = $k->perusahaan 
                                    ? $k->perusahaan->siswaProfiles->where('pembimbing_id', $k->pembimbing_id) 
                                    : collect();
                            @endphp
                            @foreach($siswaBinaanDiPerusahaan as $sp)
                                <div class="text-xs">• {{ $sp->user->name ?? '-' }}</div>
                            @endforeach
                        @endif
                    </td>
                    <td class="p-3">{{ $k->perusahaan->nama ?? '-' }}</td>
                    <td class="p-3">
                        @if($k->status === 'rencana')
                            [Rencana] {{ Str::limit($k->catatan_rencana, 50) }}
                        @else
                            [Selesai] {{ Str::limit($k->catatan, 50) }}
                        @endif
                    </td>
                    <td class="p-3">{{ $k->foto ? '📷 Ada' : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada data kunjungan</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $kunjungans->links() }}</div>
    </div>
</div>
@endsection