@extends('layouts.app')
@section('page-title', 'Jurnal Harian')
@section('content')
<div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-start gap-3 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl shadow-sm">
        <span class="text-xl">✅</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm">
        <span class="text-xl">⚠️</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if(session('info'))
    <div class="flex items-start gap-3 bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-xl shadow-sm">
        <span class="text-xl">ℹ️</span>
        <span>{{ session('info') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT SIDE: FORM INPUT -->
        <div class="lg:col-span-5 space-y-6">
            {{-- NOTIF JURNAL BUTUH REVISI --}}
            @php
                $jurnalRevisiCount = $jurnals->filter(fn($j) => $j->status === 'revisi')->count();
            @endphp
            @if($jurnalRevisiCount > 0)
            <div class="bg-orange-50 border border-orange-300 rounded-xl p-4 shadow-sm animate-pulse">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-lg">🔄</span>
                    <p class="font-bold text-orange-800">Ada {{ $jurnalRevisiCount }} jurnal perlu direvisi!</p>
                </div>
                <p class="text-xs text-orange-700">Cek rincian di kolom riwayat jurnal dan lakukan perbaikan segera.</p>
            </div>
            @endif

            {{-- FORM INPUT JURNAL --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4">
                    <h3 class="text-white font-bold text-base">📖 Input Jurnal Kegiatan Hari Ini</h3>
                    <p class="text-blue-100 text-xs mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="p-5">
                    <form action="{{ route('siswa.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Kegiatan Hari Ini <span class="text-red-500">*</span></label>
                            <textarea name="kegiatan" placeholder="Deskripsikan kegiatan PKL yang Anda lakukan hari ini..." required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm h-28 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kegiatan') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Kendala / Catatan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <textarea name="kendala" placeholder="Tuliskan kendala atau catatan tambahan jika ada..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm h-20 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kendala') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Foto Dokumentasi <span class="text-gray-400 font-normal">(Opsional, maks 20MB)</span></label>
                            <input type="file" name="foto" accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold px-4 py-3 rounded-lg hover:from-green-600 hover:to-emerald-700 transition text-sm active:scale-95 shadow-sm flex items-center justify-center gap-1">
                            💾 Simpan Jurnal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: RIWAYAT JURNAL -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">📜 Riwayat Jurnal</h3>
                <span class="text-xs text-gray-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200 font-bold">Total: {{ $jurnals->total() }}</span>
            </div>

            <div class="space-y-4">
                @forelse($jurnals as $j)
                @php
                    $borderColor = match($j->status) {
                        'disetujui' => 'border-l-green-500',
                        'revisi'    => 'border-l-red-500',
                        default     => 'border-l-yellow-400',
                    };
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 {{ $borderColor }} overflow-hidden">
                    <div class="p-5">
                        {{-- Header Row --}}
                        <div class="flex flex-wrap justify-between items-start gap-2 mb-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">{{ $j->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                                @if($j->nilai)
                                    <p class="text-xs text-blue-600 font-bold mt-0.5">🏆 Nilai: {{ $j->nilai }}/100</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($j->foto)
                                    <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded font-bold border">📷 Dokumentasi</span>
                                @endif
                                {{-- Status Badge --}}
                                @if($j->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">✅ Disetujui</span>
                                @elseif($j->status === 'revisi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">🔄 Perlu Revisi</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">⏳ Menunggu</span>
                                @endif
                            </div>
                        </div>

                        {{-- Kegiatan --}}
                        <p class="text-gray-800 text-sm leading-relaxed whitespace-pre-line">{{ $j->kegiatan }}</p>

                        {{-- Kendala --}}
                        @if($j->kendala)
                        <div class="mt-3 bg-orange-50/50 border border-orange-200 rounded-lg p-3">
                            <p class="text-[10px] font-bold text-orange-700 uppercase tracking-wider">⚠️ Kendala / Catatan:</p>
                            <p class="text-xs text-orange-950 mt-1 whitespace-pre-line">{{ $j->kendala }}</p>
                        </div>
                        @endif

                        {{-- Foto --}}
                        @if($j->foto)
                        <div class="mt-3">
                            <a href="{{ Storage::url($j->foto) }}" target="_blank">
                                <img src="{{ Storage::url($j->foto) }}" class="w-full max-h-52 object-cover rounded-lg hover:opacity-95 transition cursor-pointer border shadow-sm" alt="Foto jurnal">
                            </a>
                        </div>
                        @endif

                        {{-- Catatan Revisi dari Pembimbing --}}
                        @if($j->catatan_revisi)
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-[10px] font-bold text-red-700 uppercase tracking-wider mb-1">📝 Catatan Revisi Pembimbing:</p>
                            <p class="text-xs text-red-800 whitespace-pre-line">{{ $j->catatan_revisi }}</p>
                        </div>
                        @endif

                        {{-- Aksi --}}
                        @if(in_array($j->status, ['menunggu', 'revisi']))
                        <div class="mt-4 pt-3 border-t border-slate-100 flex gap-2">
                            <a href="{{ route('siswa.jurnal.edit', $j->id) }}"
                                class="inline-flex items-center gap-1.5 {{ $j->status === 'revisi' ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-200' }} text-white text-xs font-bold px-3 py-2 rounded-lg transition active:scale-95 shadow-sm">
                                {{ $j->status === 'revisi' ? '🔄 Perbaiki Sekarang' : '✏️ Edit Jurnal' }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center">
                    <div class="text-4xl mb-3">📝</div>
                    <p class="text-gray-500 font-medium">Belum ada jurnal</p>
                    <p class="text-sm text-gray-400 mt-1">Mulai isi jurnal harian Anda di sebelah kiri!</p>
                </div>
                @endforelse
            </div>

            @if($jurnals->hasPages())
            <div class="mt-4">{{ $jurnals->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection