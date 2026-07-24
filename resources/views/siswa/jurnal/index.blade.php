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
                $statusAbsensi = $absensiHariIni?->status;
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

            {{-- INFO STATUS ABSENSI & DAMPAK KE PENILAIAN JURNAL --}}
            @if($statusAbsensi === 'alpha')
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-3xl p-4 shadow-sm">
                <span class="text-2xl mt-0.5">🚨</span>
                <div>
                    <p class="font-bold text-red-700 text-sm">Anda tercatat <span class="uppercase">Alpha</span> hari ini</p>
                    <p class="text-xs text-red-600 mt-1 leading-relaxed">
                        Karena alpha, Anda <strong>tidak dapat mengisi jurnal</strong> hari ini.
                        Namun hari ini <strong>tetap dihitung sebagai kewajiban jurnal</strong> dalam penilaian —
                        sehingga <strong>nilai jurnal Anda akan berkurang</strong>.
                    </p>
                    <p class="text-xs text-red-500 mt-2">💡 Hubungi pembimbing jika ada kekeliruan pada status absensi.</p>
                </div>
            </div>

            @elseif($statusAbsensi === 'sakit')
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-3xl p-4 shadow-sm">
                <span class="text-2xl mt-0.5">🤒</span>
                <div>
                    <p class="font-bold text-blue-700 text-sm">Anda tercatat <span class="uppercase">Sakit</span> hari ini</p>
                    <p class="text-xs text-blue-600 mt-1 leading-relaxed">
                        Karena sakit, Anda <strong>tidak dapat mengisi jurnal</strong> hari ini.
                        Hari ini <strong>tidak dihitung</strong> sebagai kewajiban jurnal —
                        jadi <strong>nilai jurnal Anda tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>

            @elseif($statusAbsensi === 'izin')
            <div class="flex items-start gap-3 bg-sky-50 border border-sky-200 rounded-3xl p-4 shadow-sm">
                <span class="text-2xl mt-0.5">📋</span>
                <div>
                    <p class="font-bold text-sky-700 text-sm">Anda tercatat <span class="uppercase">Izin</span> hari ini</p>
                    <p class="text-xs text-sky-600 mt-1 leading-relaxed">
                        Karena izin, Anda <strong>tidak dapat mengisi jurnal</strong> hari ini.
                        Hari ini <strong>tidak dihitung</strong> sebagai kewajiban jurnal —
                        jadi <strong>nilai jurnal Anda tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>

            @elseif($statusAbsensi === 'libur')
            <div class="flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-3xl p-4 shadow-sm">
                <span class="text-2xl mt-0.5">🏖️</span>
                <div>
                    <p class="font-bold text-slate-600 text-sm">Hari ini adalah <span class="uppercase">Hari Libur</span></p>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Anda <strong>tidak perlu mengisi jurnal</strong> hari ini.
                        Hari libur <strong>tidak dihitung</strong> sebagai kewajiban jurnal —
                        jadi <strong>nilai jurnal Anda tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>

            @elseif(!$absensiHariIni)
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-3xl p-4 shadow-sm">
                <span class="text-2xl mt-0.5">⚠️</span>
                <div>
                    <p class="font-bold text-amber-700 text-sm">Absensi hari ini belum diisi</p>
                    <p class="text-xs text-amber-600 mt-1 leading-relaxed">
                        Anda harus <strong>mengisi absensi terlebih dahulu</strong> sebelum dapat mengisi jurnal.
                        Jurnal hanya bisa diisi saat absensi berstatus <strong>hadir</strong> atau <strong>terlambat</strong>.
                    </p>
                </div>
            </div>
            @endif

            {{-- FORM INPUT JURNAL --}}
            @if($sudahIsiHariIni && $jurnalHariIni)
            {{-- Sudah isi jurnal hari ini: tampilkan info --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-5 py-4">
                    <h3 class="text-white font-bold text-base">✅ Jurnal Hari Ini Sudah Diisi</h3>
                    <p class="text-emerald-100 text-xs mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-4">
                        <span class="text-2xl">📖</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 mb-1">Jurnal sudah tercatat untuk hari ini</p>
                            <p class="text-xs text-slate-500">Anda hanya dapat mengisi jurnal satu kali per hari. Jurnal hari ini dapat diedit jika statusnya <span class="font-semibold">menunggu</span> atau <span class="font-semibold">revisi</span>.</p>
                        </div>
                    </div>
                    @php
                        $statusColor = match($jurnalHariIni->status) {
                            'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'revisi'    => 'bg-red-100 text-red-700 border-red-200',
                            default     => 'bg-amber-100 text-amber-700 border-amber-200',
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
                            <span class="text-xs bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-full">📷 Ada foto</span>
                            @endif
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-3 text-sm text-slate-700">
                            <p class="font-semibold text-xs text-slate-500 uppercase tracking-wider mb-1">Kegiatan</p>
                            <p class="line-clamp-3">{{ $jurnalHariIni->kegiatan }}</p>
                        </div>
                        @if($jurnalHariIni->kendala)
                        <div class="bg-slate-50 rounded-2xl p-3 text-sm text-slate-600">
                            <p class="font-semibold text-xs text-slate-500 uppercase tracking-wider mb-1">Kendala / Catatan</p>
                            <p class="line-clamp-2">{{ $jurnalHariIni->kendala }}</p>
                        </div>
                        @endif
                        @if(in_array($jurnalHariIni->status, ['menunggu', 'revisi']))
                        <a href="{{ route('siswa.jurnal.edit', $jurnalHariIni->id) }}"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-2xl transition active:scale-95 shadow-sm">
                            {{ $jurnalHariIni->status === 'revisi' ? '🔄 Perbaiki Jurnal' : '✏️ Edit Jurnal Hari Ini' }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @elseif($absensiHariIni && in_array($absensiHariIni->status, ['hadir', 'terlambat']))
            {{-- Belum isi jurnal hari ini & sudah absen Hadir/Terlambat: tampilkan form --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4">
                    <h3 class="text-white font-bold text-base">📖 Input Jurnal Kegiatan Hari Ini</h3>
                    <p class="text-blue-100 text-xs mt-0.5">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="p-5">
                    <form action="{{ route('siswa.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kegiatan Hari Ini <span class="text-red-500">*</span></label>
                                @if(isset($jurnalTerakhir) && $jurnalTerakhir)
                                <button type="button" onclick="autoFillJurnal()" class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md border border-indigo-200 hover:bg-indigo-100 transition flex items-center gap-1 font-bold active:scale-95">
                                    <span>🔄</span> Salin Kegiatan Terakhir
                                </button>
                                @endif
                            </div>
                            <textarea id="jurnalKegiatan" name="kegiatan" placeholder="Deskripsikan kegiatan PKL yang Anda lakukan hari ini..." required
                                class="w-full border border-slate-300 rounded-2xl px-3 py-2.5 text-sm h-28 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kegiatan') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Kendala / Catatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <textarea name="kendala" placeholder="Tuliskan kendala atau catatan tambahan jika ada..."
                                class="w-full border border-slate-300 rounded-2xl px-3 py-2.5 text-sm h-20 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kendala') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Foto Dokumentasi <span class="text-slate-400 font-normal">(Opsional, maks 20MB)</span></label>
                            <x-upload-foto
                                name="foto"
                                id="jurnal_foto_input"
                                accept="image/*"
                                :required="false"
                                :max-mb="20"
                                btn-color="blue"
                                hint="📸 Upload foto dokumentasi kegiatan. Akan dikompres otomatis. Format: JPG, PNG."
                            />
                        </div>
                        <button type="submit" id="jurnal_submit_btn" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold px-4 py-3 rounded-2xl hover:from-green-600 hover:to-emerald-700 transition text-sm active:scale-95 shadow-sm flex items-center justify-center gap-2">
                            <span id="jurnal_submit_icon">💾</span>
                            <span id="jurnal_submit_text">Simpan Jurnal</span>
                            <span id="jurnal_submit_spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

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