@extends('layouts.app')
@section('page-title', 'Jurnal Harian')
@section('content')
<div class="min-h-screen py-6 bg-slate-50">
    <div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-black text-slate-900">📖 Jurnal Harian</h1>
                <p class="text-sm text-slate-500 mt-1">Catat aktivitas PKL harian dan lihat status jurnal Anda secara ringkas.</p>
            </div>
            <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200 font-semibold">Total Jurnal: {{ $jurnals->total() }}</span>
        </div>

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

    <div class="space-y-6">
            {{-- NOTIF JURNAL BUTUH REVISI --}}
            @php
                $jurnalRevisiCount = $jurnals->filter(fn($j) => $j->status === 'revisi')->count();
            @endphp
            @if($jurnalRevisiCount > 0)
            <div class="bg-orange-50 border border-orange-300 rounded-3xl p-4 shadow-sm animate-pulse">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-lg">🔄</span>
                    <p class="font-bold text-orange-800">Ada {{ $jurnalRevisiCount }} jurnal perlu direvisi!</p>
                </div>
                <p class="text-xs text-orange-700">Isi jurnal di bawah, kemudian lihat ringkasan riwayat jurnal di bawah form.</p>
            </div>
            @endif

            {{-- FORM INPUT JURNAL --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4">
                    <h3 class="text-white font-bold text-base">📖 Input Jurnal Kegiatan Hari Ini</h3>
                    <p class="text-blue-100 text-xs mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="p-5">
                    <form action="{{ route('siswa.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Kegiatan Hari Ini <span class="text-red-500">*</span></label>
                            <textarea name="kegiatan" placeholder="Deskripsikan kegiatan PKL yang Anda lakukan hari ini..." required
                                class="w-full border border-slate-300 rounded-2xl px-3 py-2.5 text-sm h-28 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kegiatan') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Kendala / Catatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <textarea name="kendala" placeholder="Tuliskan kendala atau catatan tambahan jika ada..."
                                class="w-full border border-slate-300 rounded-2xl px-3 py-2.5 text-sm h-20 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kendala') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Foto Dokumentasi <span class="text-slate-400 font-normal">(Opsional, maks 20MB)</span></label>
                            <input type="file" name="foto" accept="image/*"
                                class="w-full border border-slate-300 rounded-2xl px-3 py-2 text-sm file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold px-4 py-3 rounded-2xl hover:from-green-600 hover:to-emerald-700 transition text-sm active:scale-95 shadow-sm flex items-center justify-center gap-2">
                            💾 Simpan Jurnal
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">📜 Riwayat Jurnal</h3>
                        <p class="text-xs text-slate-500 mt-1">Review ringkas jurnal harian, tanpa menampilkan keseluruhan teks atau preview foto.</p>
                    </div>
                    <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200 font-bold">Total: {{ $jurnals->total() }}</span>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($jurnals as $j)
                    @php
                        $cardStyle = match($j->status) {
                            'disetujui' => 'bg-emerald-50 border-emerald-100 text-slate-800',
                            'revisi'    => 'bg-red-50 border-red-100 text-slate-800',
                            default     => 'bg-amber-50 border-amber-100 text-slate-800',
                        };
                    @endphp
                    <div class="rounded-3xl border p-4 shadow-sm {{ $cardStyle }}">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                            <div>
                                <p class="text-sm font-semibold">{{ $j->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $j->status === 'disetujui' ? 'Disetujui' : ($j->status === 'revisi' ? 'Perlu direvisi' : 'Menunggu review') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2 items-center text-xs">
                                @if($j->foto)
                                <span class="inline-flex items-center gap-1 bg-white border border-slate-200 px-2.5 py-1 rounded-full">📷 Foto tersedia</span>
                                @endif
                                @if($j->nilai)
                                <span class="inline-flex items-center gap-1 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-full">🏆 Nilai {{ $j->nilai }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-slate-700 line-clamp-2">
                            {{ Str::limit($j->kegiatan, 100) }}
                        </div>
                        @if($j->kendala)
                        <div class="mt-3 text-xs text-slate-500 line-clamp-2">
                            {{ Str::limit($j->kendala, 70) }}
                        </div>
                        @endif
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                            <span>Review ringkas jurnal.</span>
                            @if(in_array($j->status, ['menunggu', 'revisi']))
                            <a href="{{ route('siswa.jurnal.edit', $j->id) }}"
                                class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-full font-semibold transition active:scale-95 shadow-sm">
                                {{ $j->status === 'revisi' ? '🔄 Perbaiki' : '✏️ Edit' }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center">
                        <div class="text-4xl mb-3">📝</div>
                        <p class="text-slate-600 font-medium">Belum ada jurnal</p>
                        <p class="text-sm text-slate-400 mt-1">Mulai isi jurnal harian Anda di atas.</p>
                    </div>
                    @endforelse
                </div>
                @if($jurnals->hasPages())
                <div class="px-5 py-4 border-t border-slate-200">
                    {{ $jurnals->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection