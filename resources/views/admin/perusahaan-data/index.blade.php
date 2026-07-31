@extends('layouts.app')
@section('page-title', 'Data Perusahaan Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white drop-shadow-md">🏢 Data Perusahaan PKL Siswa</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.perusahaan-data.print') }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition flex items-center gap-2">
                🖨️ Cetak Data Approved
            </a>
            <a href="{{ route('admin.perusahaan-data') }}?filter=all" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition">
                🔍 Semua Data
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-panel border-l-4 border-green-500 p-4 rounded-xl shadow-[0_0_15px_rgba(34,197,94,0.1)] font-bold text-green-400">
            <p class="font-semibold">✅ {{ session('success') }}</p>
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="flex gap-2 border-b border-white/5 pb-2">
        <a href="{{ route('admin.perusahaan-data') }}" class="px-4 py-2 text-sm font-medium rounded-t-lg {{ request('filter') !== 'approved' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner' : 'text-gray-400 hover:text-white drop-shadow-md' }}">
            Semua ({{ \App\Models\PerusahaanData::count() }})
        </a>
        <a href="{{ route('admin.perusahaan-data') }}?filter=approved" class="px-4 py-2 text-sm font-medium rounded-t-lg {{ request('filter') === 'approved' ? 'bg-green-500/20 text-green-400 border border-green-500/30 shadow-inner' : 'text-gray-400 hover:text-white drop-shadow-md' }}">
            ✅ Approved ({{ \App\Models\PerusahaanData::where('is_verified', true)->count() }})
        </a>
        <a href="{{ route('admin.perusahaan-data') }}?filter=pending" class="px-4 py-2 text-sm font-medium rounded-t-lg {{ request('filter') === 'pending' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 shadow-inner' : 'text-gray-400 hover:text-white drop-shadow-md' }}">
            ⏳ Menunggu ({{ \App\Models\PerusahaanData::where('is_verified', false)->count() }})
        </a>
    </div>

    <div class="glass-panel rounded-xl shadow-sm border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="glass-panel/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama Siswa</th>
                        <th class="px-4 py-3 font-semibold">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold">Pembimbing|Alamat|TTL</th>
                        <th class="px-4 py-3 font-semibold">No. Telp</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($data as $d)
                    <tr class="hover:glass-panel/5 {{ !$d->is_verified ? 'bg-yellow-50/30' : '' }}">
                        <td class="px-4 py-3 font-medium">{{ $d->siswa->name }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $d->nama_perusahaan }}</div>
                        
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">{{ $d->nama_pembimbing }}</div>
			    <div class="text-xs text-gray-400 line-clamp-1">{{ $d->alamat_pembimbing }}</div>
                            <div class="text-xs text-gray-400">{{ $d->ttl_pembimbing }}</div>
                        </td>

                        <td class="px-4 py-3 font-mono text-xs">{{ $d->no_telp }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($d->is_verified)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30 shadow-inner">
                                    <span>✓</span> Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 shadow-inner">
                                    <span>⏳</span> Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                @if(!$d->is_verified)
                                    <!-- Tombol Approve -->
                                    <form action="{{ route('admin.perusahaan-data.approve', $d) }}" method="POST" onsubmit="return confirm('Setujui data perusahaan untuk {{ $d->siswa->name }}?')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-1.5 rounded hover:bg-green-50 transition" title="Approve">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <!-- Tombol Reject -->
                                    <form action="{{ route('admin.perusahaan-data.reject', $d) }}" method="POST" onsubmit="return confirm('Tandai data ini belum valid?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 p-1.5 rounded hover:bg-red-50 transition" title="Reject">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    <!-- Tombol Undo Approve (opsional) -->
                                    <form action="{{ route('admin.perusahaan-data.reject', $d) }}" method="POST" onsubmit="return confirm('Batalkan approval untuk data ini?')">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-gray-400 p-1.5 rounded hover:glass-panel/5 transition text-xs" title="Batalkan">
                                            ↺
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Belum ada data perusahaan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t glass-panel/5">{{ $data->links() }}</div>
    </div>
</div>
@endsection
