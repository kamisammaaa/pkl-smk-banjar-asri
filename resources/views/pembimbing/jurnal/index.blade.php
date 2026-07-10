@extends('layouts.app')
@section('page-title', 'Review Jurnal')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">📖 Review Jurnal Siswa</h2>
        @php $pending = $jurnals->where('status','menunggu')->count(); @endphp
        @if($pending > 0)
        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
            ⏳ {{ $pending }} menunggu review
        </span>
        @endif
    </div>

    @if(session('success'))
    <div class="flex items-start gap-3 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
        <span class="text-xl">✅</span><span>{{ session('success') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Filter Siswa</label>
                <select name="siswa_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                    <option value="">Semua Siswa Binaan</option>
                    @foreach($siswaBinaan as $s)
                        <option value="{{ $s->user_id }}" {{ request('siswa_id') == $s->user_id ? 'selected' : '' }}>{{ $s->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                    <option value="">Semua</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>🔄 Revisi</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition font-medium">🔍 Filter</button>
                <a href="{{ route('pembimbing.jurnal') }}" class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition font-medium">Reset</a>
            </div>
        </form>
    </div>

    {{-- Jurnal Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($jurnals as $j)
        @php
            $borderColor = match($j->status) {
                'disetujui' => 'border-l-green-500',
                'revisi'    => 'border-l-orange-500',
                default     => 'border-l-yellow-400',
            };
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 {{ $borderColor }} overflow-hidden">
            <div class="p-4">
                {{-- Header --}}
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $j->siswa->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $j->tanggal->locale('id')->isoFormat('D MMMM Y') }} •
                            {{ $j->siswa->siswaProfile->perusahaan->nama ?? '-' }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @if($j->status === 'disetujui')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">✅ Disetujui</span>
                        @elseif($j->status === 'revisi')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">🔄 Revisi</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">⏳ Menunggu</span>
                        @endif
                        @if($j->nilai)
                            <span class="text-xs text-blue-600 font-medium">🏆 {{ $j->nilai }}/100</span>
                        @endif
                    </div>
                </div>

                {{-- Kegiatan --}}
                <p class="text-sm text-gray-700 leading-relaxed mb-2">{{ $j->kegiatan }}</p>

                {{-- Kendala --}}
                @if($j->kendala)
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-2.5 mb-3">
                    <p class="text-xs font-medium text-orange-700">⚠️ Kendala siswa:</p>
                    <p class="text-sm text-orange-800 mt-0.5">{{ $j->kendala }}</p>
                </div>
                @endif

                {{-- Foto --}}
                @if($j->foto)
                <a href="{{ Storage::url($j->foto) }}" target="_blank" class="block mb-3">
                    <img src="{{ Storage::url($j->foto) }}" class="w-full h-36 object-cover rounded-lg hover:opacity-90 transition" alt="Foto jurnal">
                    <p class="text-xs text-center text-gray-400 mt-1">🔍 Klik foto untuk memperbesar</p>
                </a>
                @endif

                {{-- Catatan Revisi Sebelumnya --}}
                @if($j->catatan_revisi)
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                    <p class="text-xs font-bold text-red-700 mb-1">📝 Catatan Revisi Sebelumnya:</p>
                    <p class="text-sm text-red-600">{{ $j->catatan_revisi }}</p>
                </div>
                @endif

                {{-- Form Approve/Revisi --}}
                @if($j->status !== 'disetujui')
                <form action="{{ route('pembimbing.jurnal.approve', $j->id) }}" method="POST" class="space-y-2 pt-3 border-t border-gray-100" id="form-jurnal-{{ $j->id }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <select name="status" class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-blue-200"
                            onchange="toggleCatatanRevisi({{ $j->id }}, this.value)">
                            <option value="disetujui">✅ Setujui</option>
                            <option value="revisi">🔄 Kembalikan Revisi</option>
                        </select>
                        <input type="number" name="nilai" min="0" max="100" placeholder="Nilai (0-100)"
                            value="{{ old('nilai', $j->nilai) }}"
                            class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div id="catatan-{{ $j->id }}" class="hidden">
                        <textarea name="catatan_revisi" placeholder="Tuliskan catatan revisi untuk siswa..."
                            class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm resize-none focus:ring-2 focus:ring-red-200 focus:border-red-400" rows="2">{{ old('catatan_revisi', $j->catatan_revisi) }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition active:scale-95">
                        Simpan Penilaian
                    </button>
                </form>
                @else
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-sm text-green-700">
                    <span>✓ Sudah disetujui</span>
                    @if($j->nilai) <span class="font-semibold">Nilai: {{ $j->nilai }}/100</span> @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-4xl mb-3">📭</div>
            <p class="text-gray-500 font-medium">Tidak ada jurnal ditemukan</p>
            <p class="text-sm text-gray-400 mt-1">Coba ubah filter untuk melihat jurnal lainnya.</p>
        </div>
        @endforelse
    </div>

    <div class="px-1">{{ $jurnals->appends(request()->query())->links() }}</div>
</div>

<script>
function toggleCatatanRevisi(id, value) {
    const el = document.getElementById('catatan-' + id);
    if (value === 'revisi') {
        el.classList.remove('hidden');
        el.querySelector('textarea').setAttribute('required', 'required');
    } else {
        el.classList.add('hidden');
        el.querySelector('textarea').removeAttribute('required');
    }
}
</script>
@endsection