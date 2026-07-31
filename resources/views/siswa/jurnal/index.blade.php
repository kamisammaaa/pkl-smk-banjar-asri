@extends('layouts.app')
@section('page-title', 'Jurnal Harian')
@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-black text-white">📖 Jurnal Harian</h1>
                <p class="text-sm text-gray-400 mt-1">Catat aktivitas PKL harian dan lihat status jurnal Anda secara ringkas.</p>
            </div>
            <span class="text-xs text-crypto-accent bg-crypto-accent/20 px-3 py-1 rounded-full border border-crypto-accent/30 font-semibold shadow-[0_0_8px_rgba(112,0,255,0.2)]">Total Jurnal: {{ $jurnals->total() }}</span>
        </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-start gap-3 glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-xl shadow-[0_0_15px_rgba(14,203,129,0.2)]">
        <span class="text-xl">✅</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.2)]">
        <span class="text-xl">⚠️</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if(session('info'))
    <div class="flex items-start gap-3 glass-panel bg-blue-500/20 border-l-4 border-blue-500 text-blue-400 p-4 rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.2)]">
        <span class="text-xl">ℹ️</span>
        <span>{{ session('info') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.2)]">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="space-y-6">
            {{-- NOTIF JURNAL BUTUH REVISI --}}
            @php
                $jurnalRevisiCount = $jurnals->filter(fn($j) => $j->status === 'revisi')->count();
                $statusAbsensi = $absensiHariIni?->status;
            @endphp
            @if($jurnalRevisiCount > 0)
            <div class="glass-panel bg-orange-500/20 border border-orange-500/30 rounded-3xl p-4 shadow-[0_0_15px_rgba(249,115,22,0.2)] animate-pulse">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-lg">🔄</span>
                    <p class="font-bold text-orange-400">Ada {{ $jurnalRevisiCount }} jurnal perlu direvisi!</p>
                </div>
                <p class="text-xs text-orange-300">Isi jurnal di bawah, kemudian lihat ringkasan riwayat jurnal di bawah form.</p>
            </div>
            @endif

            {{-- INFO STATUS ABSENSI & DAMPAK KE PENILAIAN JURNAL --}}
            @if($statusAbsensi === 'alpha')
            <div class="flex items-start gap-3 glass-panel bg-red-900/30 border border-red-500/50 rounded-3xl p-4 shadow-[0_0_15px_rgba(239,68,68,0.2)]">
                <span class="text-2xl mt-0.5">🚨</span>
                <div>
                    <p class="font-bold text-red-400 text-sm">Anda tercatat <span class="uppercase">Alpha</span> hari ini</p>
                    <p class="text-xs text-red-300 mt-1 leading-relaxed">
                        Karena alpha, Anda <strong class="text-red-200">tidak dapat mengisi jurnal</strong> hari ini.
                        Namun hari ini <strong class="text-red-200">tetap dihitung sebagai kewajiban jurnal</strong> dalam penilaian —
                        sehingga <strong class="text-red-200">nilai jurnal Anda akan berkurang</strong>.
                    </p>
                    <p class="text-xs text-red-500 mt-2">💡 Hubungi pembimbing jika ada kekeliruan pada status absensi.</p>
                </div>
            </div>

            @elseif($statusAbsensi === 'sakit')
            <div class="flex items-start gap-3 glass-panel bg-orange-900/30 border border-orange-500/50 rounded-3xl p-4 shadow-[0_0_15px_rgba(249,115,22,0.2)]">
                <span class="text-2xl mt-0.5">🤒</span>
                <div>
                    <p class="font-bold text-orange-400 text-sm">Anda tercatat <span class="uppercase">Sakit</span> hari ini</p>
                    <p class="text-xs text-orange-300 mt-1 leading-relaxed">
                        Karena sakit, Anda <strong class="text-orange-200">tidak dapat mengisi jurnal</strong> hari ini.
                        Hari ini <strong class="text-orange-200">tidak dihitung</strong> sebagai kewajiban jurnal —
                        jadi <strong class="text-orange-200">nilai jurnal Anda tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>

            @elseif($statusAbsensi === 'izin')
            <div class="flex items-start gap-3 glass-panel bg-blue-900/30 border border-blue-500/50 rounded-3xl p-4 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                <span class="text-2xl mt-0.5">📋</span>
                <div>
                    <p class="font-bold text-blue-400 text-sm">Anda tercatat <span class="uppercase">Izin</span> hari ini</p>
                    <p class="text-xs text-blue-300 mt-1 leading-relaxed">
                        Karena izin, Anda <strong class="text-blue-200">tidak dapat mengisi jurnal</strong> hari ini.
                        Hari ini <strong class="text-blue-200">tidak dihitung</strong> sebagai kewajiban jurnal —
                        jadi <strong class="text-blue-200">nilai jurnal Anda tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>

            @elseif($statusAbsensi === 'libur')
            <div class="flex items-start gap-3 glass-panel bg-purple-900/30 border border-purple-500/50 rounded-3xl p-4 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
                <span class="text-2xl mt-0.5">🏖️</span>
                <div>
                    <p class="font-bold text-purple-400 text-sm">Hari ini adalah <span class="uppercase">Hari Libur</span></p>
                    <p class="text-xs text-purple-300 mt-1 leading-relaxed">
                        Anda <strong class="text-purple-200">tidak perlu mengisi jurnal</strong> hari ini.
                        Hari libur <strong class="text-purple-200">tidak dihitung</strong> sebagai kewajiban jurnal —
                        jadi <strong class="text-purple-200">nilai jurnal Anda tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>

            @elseif(!$absensiHariIni)
            <div class="flex items-start gap-3 glass-panel bg-yellow-900/30 border border-yellow-500/50 rounded-3xl p-4 shadow-[0_0_15px_rgba(234,179,8,0.2)]">
                <span class="text-2xl mt-0.5">⚠️</span>
                <div>
                    <p class="font-bold text-yellow-400 text-sm">Absensi hari ini belum diisi</p>
                    <p class="text-xs text-yellow-300 mt-1 leading-relaxed">
                        Anda harus <strong class="text-yellow-200">mengisi absensi terlebih dahulu</strong> sebelum dapat mengisi jurnal.
                        Jurnal hanya bisa diisi saat absensi berstatus <strong class="text-yellow-200">hadir</strong> atau <strong class="text-yellow-200">terlambat</strong>.
                    </p>
                </div>
            </div>
            @endif

            {{-- FORM INPUT JURNAL --}}
            @if($sudahIsiHariIni && $jurnalHariIni)
            {{-- Sudah isi jurnal hari ini: tampilkan info --}}
            <div class="glass-panel border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] rounded-3xl overflow-hidden">
                <div class="bg-gradient-to-r from-crypto-success/80 to-green-600/80 px-5 py-4 border-b border-white/10">
                    <h3 class="text-white font-bold text-base">✅ Jurnal Hari Ini Sudah Diisi</h3>
                    <p class="text-green-100 text-xs mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-3 bg-crypto-success/10 border border-crypto-success/20 rounded-2xl p-4 mb-4 shadow-inner">
                        <span class="text-2xl drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">📖</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-crypto-success mb-1">Jurnal sudah tercatat untuk hari ini</p>
                            <p class="text-xs text-gray-400">Anda hanya dapat mengisi jurnal satu kali per hari. Jurnal hari ini dapat diedit jika statusnya <span class="font-semibold text-gray-300">menunggu</span> atau <span class="font-semibold text-gray-300">revisi</span>.</p>
                        </div>
                    </div>
                    @php
                        $statusColor = match($jurnalHariIni->status) {
                            'disetujui' => 'bg-crypto-success/20 text-crypto-success border-crypto-success/30 shadow-[0_0_8px_rgba(14,203,129,0.2)]',
                            'revisi'    => 'bg-red-500/20 text-red-400 border-red-500/30 shadow-[0_0_8px_rgba(239,68,68,0.2)]',
                            default     => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30 shadow-[0_0_8px_rgba(234,179,8,0.2)]',
                        };
                        $statusLabel = match($jurnalHariIni->status) {
                            'disetujui' => '✅ Disetujui',
                            'revisi'    => '🔄 Perlu Direvisi',
                            default     => '⏳ Menunggu Review',
                        };
                    @endphp
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $statusColor }}">{{ $statusLabel }}</span>
                            @if($jurnalHariIni->foto)
                            <span class="text-xs bg-white/5 border border-white/10 text-gray-300 px-2.5 py-1 rounded-full">📷 Ada foto</span>
                            @endif
                        </div>
                        <div class="bg-white/5 border border-white/5 rounded-2xl p-3 text-sm text-gray-300">
                            <p class="font-semibold text-xs text-gray-400 uppercase tracking-wider mb-1">Kegiatan</p>
                            <p class="line-clamp-3 text-white">{{ $jurnalHariIni->kegiatan }}</p>
                        </div>
                        @if($jurnalHariIni->kendala)
                        <div class="bg-white/5 border border-white/5 rounded-2xl p-3 text-sm text-gray-300">
                            <p class="font-semibold text-xs text-gray-400 uppercase tracking-wider mb-1">Kendala / Catatan</p>
                            <p class="line-clamp-2 text-white">{{ $jurnalHariIni->kendala }}</p>
                        </div>
                        @endif
                        @if(in_array($jurnalHariIni->status, ['menunggu', 'revisi']))
                        <a href="{{ route('siswa.jurnal.edit', $jurnalHariIni->id) }}"
                            class="inline-flex items-center gap-2 bg-crypto-accent hover:bg-crypto-accentHover text-white text-sm font-bold px-4 py-2.5 rounded-2xl transition-colors active:scale-95 shadow-[0_0_10px_rgba(112,0,255,0.3)]">
                            {{ $jurnalHariIni->status === 'revisi' ? '🔄 Perbaiki Jurnal' : '✏️ Edit Jurnal Hari Ini' }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @elseif($absensiHariIni && in_array($absensiHariIni->status, ['hadir', 'terlambat']))
            {{-- Belum isi jurnal hari ini & sudah absen Hadir/Terlambat: tampilkan form --}}
            <div class="glass-panel border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] rounded-3xl overflow-hidden">
                <div class="bg-gradient-to-r from-crypto-accent/80 to-blue-600/80 px-5 py-4 border-b border-white/10">
                    <h3 class="text-white font-bold text-base">📖 Input Jurnal Kegiatan Hari Ini</h3>
                    <p class="text-blue-100 text-xs mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="p-5">
                    <form action="{{ route('siswa.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider">Kegiatan Hari Ini <span class="text-crypto-accent">*</span></label>
                                @if(isset($jurnalTerakhir) && $jurnalTerakhir)
                                <button type="button" onclick="autoFillJurnal()" class="text-[10px] bg-crypto-accent/20 text-crypto-accent px-2 py-1 rounded-md border border-crypto-accent/30 hover:bg-crypto-accent/40 transition-colors flex items-center gap-1 font-bold active:scale-95">
                                    <span>🔄</span> Salin Kegiatan Terakhir
                                </button>
                                @endif
                            </div>
                            <textarea id="jurnalKegiatan" name="kegiatan" placeholder="Deskripsikan kegiatan PKL yang Anda lakukan hari ini..." required
                                class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-2xl px-3 py-2.5 text-sm h-28 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent resize-none transition-colors">{{ old('kegiatan') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1 uppercase tracking-wider">Kendala / Catatan <span class="text-gray-500 font-normal">(Opsional)</span></label>
                            <textarea name="kendala" placeholder="Tuliskan kendala atau catatan tambahan jika ada..."
                                class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-2xl px-3 py-2.5 text-sm h-20 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent resize-none transition-colors">{{ old('kendala') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1 uppercase tracking-wider">Foto Dokumentasi <span class="text-gray-500 font-normal">(Opsional, maks 20MB)</span></label>
                            <div class="text-gray-300">
                                <x-upload-foto
                                    name="foto"
                                    id="jurnal_foto_input"
                                    accept="image/*"
                                    :required="false"
                                    :max-mb="20"
                                    btn-color="indigo"
                                    hint="📸 Upload foto dokumentasi kegiatan. Akan dikompres otomatis. Format: JPG, PNG."
                                />
                            </div>
                        </div>
                        <button type="submit" id="jurnal_submit_btn" class="w-full bg-crypto-success hover:bg-emerald-500 text-white font-bold px-4 py-3 rounded-2xl transition-colors text-sm active:scale-95 shadow-[0_0_15px_rgba(14,203,129,0.3)] flex items-center justify-center gap-2">
                            <span id="jurnal_submit_icon">💾</span>
                            <span id="jurnal_submit_text">Simpan Jurnal</span>
                            <span id="jurnal_submit_spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="glass-panel border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] rounded-3xl overflow-hidden">
                <div class="p-5 border-b border-white/10 bg-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 relative">
                    <div>
                        <h3 class="font-bold text-white text-base">📜 Riwayat Jurnal</h3>
                        <p class="text-xs text-gray-400 mt-1">Review ringkas jurnal harian, tanpa menampilkan keseluruhan teks atau preview foto.</p>
                    </div>
                    <span class="text-xs text-gray-300 bg-white/10 px-2.5 py-1 rounded-full border border-white/20 font-bold">Total: {{ $jurnals->total() }}</span>
                </div>
                <div class="p-5 space-y-4 relative">
                    @forelse($jurnals as $j)
                    @php
                        $cardStyle = match($j->status) {
                            'disetujui' => 'bg-crypto-success/10 border-crypto-success/30 text-white shadow-inner',
                            'revisi'    => 'bg-red-500/10 border-red-500/30 text-white shadow-inner',
                            default     => 'bg-yellow-500/10 border-yellow-500/30 text-white shadow-inner',
                        };
                    @endphp
                    <div class="rounded-3xl border p-4 shadow-sm {{ $cardStyle }}">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                            <div>
                                <p class="text-sm font-semibold">{{ $j->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    @if($j->status === 'disetujui') <span class="text-crypto-success font-semibold drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">Disetujui</span>
                                    @elseif($j->status === 'revisi') <span class="text-red-400 font-semibold drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">Perlu direvisi</span>
                                    @else <span class="text-yellow-400 font-semibold drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]">Menunggu review</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2 items-center text-xs">
                                @if($j->foto)
                                <span class="inline-flex items-center gap-1 bg-white/5 border border-white/10 px-2.5 py-1 rounded-full text-gray-300">📷 Foto tersedia</span>
                                @endif
                                @if($j->nilai)
                                <span class="inline-flex items-center gap-1 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full text-white font-bold drop-shadow-[0_0_5px_rgba(255,255,255,0.3)]">🏆 Nilai {{ $j->nilai }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-200 line-clamp-2">
                            {{ Str::limit($j->kegiatan, 100) }}
                        </div>
                        @if($j->kendala)
                        <div class="mt-3 text-xs text-gray-400 line-clamp-2">
                            {{ Str::limit($j->kendala, 70) }}
                        </div>
                        @endif
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                            <span>Review ringkas jurnal.</span>
                            @if(in_array($j->status, ['menunggu', 'revisi']))
                            <a href="{{ route('siswa.jurnal.edit', $j->id) }}"
                                class="inline-flex items-center gap-1 bg-crypto-accent hover:bg-crypto-accentHover text-white px-3 py-2 rounded-full font-semibold transition-colors active:scale-95 shadow-[0_0_10px_rgba(112,0,255,0.3)]">
                                {{ $j->status === 'revisi' ? '🔄 Perbaiki' : '✏️ Edit' }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 text-center shadow-inner">
                        <div class="text-4xl mb-3 opacity-50 drop-shadow-[0_0_10px_rgba(255,255,255,0.2)]">📝</div>
                        <p class="text-gray-300 font-medium">Belum ada jurnal</p>
                        <p class="text-sm text-gray-500 mt-1">Mulai isi jurnal harian Anda di atas.</p>
                    </div>
                    @endforelse
                </div>
                @if($jurnals->hasPages())
                <div class="px-5 py-4 border-t border-white/10 bg-white/5 relative">
                    {{ $jurnals->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.querySelector('form[action="{{ route('siswa.jurnal.store') }}"]');
    const submitBtn = document.getElementById('jurnal_submit_btn');

    if (form && submitBtn) {
        attachUploadProgress(form, ['jurnal_foto_input'], submitBtn);

        // Fallback spinner jika tidak ada file
        form.addEventListener('submit', function () {
            const hasFile = document.getElementById('jurnal_foto_input')?.files?.length > 0;
            if (!hasFile) {
                submitBtn.disabled = true;
                document.getElementById('jurnal_submit_text').textContent  = 'Menyimpan...';
                document.getElementById('jurnal_submit_spinner').classList.remove('hidden');
                document.getElementById('jurnal_submit_icon').classList.add('hidden');
            }
        });
    }
});

@if(isset($jurnalTerakhir) && $jurnalTerakhir)
function autoFillJurnal() {
    const textarea = document.getElementById('jurnalKegiatan');
    if(textarea) {
        textarea.value = {!! json_encode($jurnalTerakhir->kegiatan) !!};
        
        textarea.classList.add('bg-indigo-50', 'border-indigo-400');
        setTimeout(() => textarea.classList.remove('bg-indigo-50', 'border-indigo-400'), 600);
    }
}
@endif
</script>
@endpush
@endsection