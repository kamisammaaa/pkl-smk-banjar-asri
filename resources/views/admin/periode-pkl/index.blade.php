@extends('layouts.app')
@section('page-title', 'Manajemen Periode PKL')

@section('content')
<div class="space-y-6">
    <!-- Header & Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">📅 Manajemen Periode PKL</h2>
            <p class="text-sm text-gray-500 mt-1">Atur jadwal dan periode pelaksanaan PKL siswa</p>
        </div>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
            <span class="text-lg">+</span> Tambah Periode
        </button>
    </div>

    @if(session('success')) <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm"><p class="font-semibold">✅ {{ session('success') }}</p></div> @endif
    @if($errors->any()) <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif

    @php $activePeriods = \App\Models\PeriodePKL::where('is_active', true)->get(); @endphp
    @if($activePeriods->isNotEmpty())
        <div class="space-y-2">
            @foreach($activePeriods as $activePeriod)
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-xl">🟢</div>
                        <div>
                            <p class="font-bold text-green-800">Periode Aktif: {{ $activePeriod->nama }}</p>
                            <p class="text-sm text-green-600">{{ $activePeriod->tanggal_mulai->format('d/m/Y') }} — {{ $activePeriod->tanggal_selesai->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @if(now()->between($activePeriod->tanggal_mulai, $activePeriod->tanggal_selesai))
                        <span class="inline-flex items-center px-3 py-1 bg-green-200 text-green-800 text-xs font-bold rounded-full whitespace-nowrap">✨ SEDANG BERJALAN</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-yellow-200 text-yellow-800 text-xs font-bold rounded-full whitespace-nowrap">⏳ BELUM MULAI / SUDAH SELESAI</span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3"><span class="text-2xl">⚠️</span><div><p class="font-semibold text-yellow-800">Belum ada periode PKL yang aktif</p><p class="text-sm text-yellow-700 mt-1">Silakan aktifkan salah satu periode di tabel bawah.</p></div></div>
    @endif

    <!-- Table (Responsive Scroll) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[600px]">
                <thead class="bg-gray-50 border-b">
                    <tr><th class="px-4 py-3 font-semibold text-gray-700">Nama Periode</th><th class="px-4 py-3 font-semibold text-gray-700">Mulai</th><th class="px-4 py-3 font-semibold text-gray-700">Selesai</th><th class="px-4 py-3 font-semibold text-gray-700 text-center">Durasi</th><th class="px-4 py-3 font-semibold text-gray-700 text-center">Status</th><th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($periode as $p)
                    <tr class="hover:bg-gray-50 {{ $p->is_active ? 'bg-green-50/40' : '' }}">
                        <td class="px-4 py-3 font-medium">{{ $p->nama }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">{{ $p->tanggal_mulai->diffInDays($p->tanggal_selesai) }} hari</span></td>
                        <td class="px-4 py-3 text-center">@if($p->is_active)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">✓ Aktif</span>@else<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">Nonaktif</span>@endif</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.periode-pkl.activate', $p) }}" method="POST" onsubmit="return confirm('{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }} periode ini?');">
                                    @csrf
                                    <button class="{{ $p->is_active ? 'text-orange-600 hover:text-orange-800 hover:bg-orange-50' : 'text-blue-600 hover:text-blue-800 hover:bg-blue-50' }} text-xs font-medium px-2 py-1 rounded">
                                        {{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @if(!$p->is_active)
                                    <form action="{{ route('admin.periode-pkl.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus periode ini?');">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded hover:bg-red-50">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">📭 Belum ada data periode PKL</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modal-tambah" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-tambah').classList.add('hidden')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200">
            <div class="px-5 py-4 border-b flex justify-between items-center bg-gray-50"><h3 class="font-bold text-gray-800">📅 Tambah Periode</h3><button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button></div>
            <form action="{{ route('admin.periode-pkl.store') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div><label class="block text-sm font-medium mb-1">Nama Periode</label><input type="text" name="nama" required class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Contoh: PKL Gelombang 1 2024/2025"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-sm font-medium mb-1">Tanggal Mulai</label><input type="date" name="tanggal_mulai" required class="w-full border rounded-lg px-3 py-2.5 text-sm"></div>
                    <div><label class="block text-sm font-medium mb-1">Tanggal Selesai</label><input type="date" name="tanggal_selesai" required class="w-full border rounded-lg px-3 py-2.5 text-sm"></div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection