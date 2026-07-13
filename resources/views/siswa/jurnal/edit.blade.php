@extends('layouts.app')
@section('page-title', 'Edit Jurnal')
@section('content')
<div class="min-h-screen py-6 bg-slate-50">
    <div class="max-w-2xl mx-auto space-y-6">

    {{-- Back Button --}}
    <div>
        <a href="{{ route('siswa.jurnal.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition">
            ← Kembali ke Jurnal
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-start gap-3 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
        <span class="text-xl">✅</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
        <span class="text-xl">⚠️</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Catatan Revisi dari Pembimbing (jika ada) --}}
    @if($jurnal->catatan_revisi && $jurnal->status === 'revisi')
    <div class="bg-red-50 border border-red-300 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-lg">📋</span>
            <p class="font-semibold text-red-800">Catatan Revisi dari Pembimbing</p>
        </div>
        <p class="text-sm text-red-700 bg-white border border-red-200 rounded-lg p-3">{{ $jurnal->catatan_revisi }}</p>
        <p class="text-xs text-red-600 mt-2">Perbaiki jurnal kamu sesuai catatan di atas, kemudian simpan.</p>
    </div>
    @endif

    {{-- Form Edit --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r {{ $jurnal->status === 'revisi' ? 'from-red-600 to-orange-600' : 'from-blue-600 to-indigo-600' }} px-5 py-4">
            <h3 class="text-white font-bold text-lg">
                {{ $jurnal->status === 'revisi' ? '🔄 Revisi Jurnal' : '✏️ Edit Jurnal' }}
            </h3>
            <p class="text-white/80 text-sm mt-0.5">
                Jurnal tanggal: {{ $jurnal->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </p>
        </div>
        <div class="p-5">
            <form action="{{ route('siswa.jurnal.update', $jurnal->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan Hari Ini <span class="text-red-500">*</span></label>
                    <textarea name="kegiatan" required placeholder="Deskripsikan kegiatan PKL..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-sm h-32 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kegiatan', $jurnal->kegiatan) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kendala / Catatan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <textarea name="kendala" placeholder="Tuliskan kendala atau catatan tambahan (jika ada)..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-sm h-20 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none transition">{{ old('kendala', $jurnal->kendala) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Dokumentasi <span class="text-gray-400 font-normal">(Opsional, ganti jika perlu)</span></label>
                    <x-upload-foto
                        name="foto"
                        id="jurnal_edit_foto"
                        accept="image/*"
                        :required="false"
                        :max-mb="20"
                        btn-color="blue"
                        hint="📸 Upload foto baru untuk mengganti. Akan dikompres otomatis. Format: JPG, PNG."
                        :existing-url="$jurnal->foto ? Storage::url($jurnal->foto) : null"
                        existing-label="Foto jurnal saat ini tersimpan. Upload baru untuk mengganti."
                    />
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" id="jurnal_edit_submit" class="flex-1 bg-gradient-to-r {{ $jurnal->status === 'revisi' ? 'from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600' : 'from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700' }} text-white font-bold px-4 py-3 rounded-lg transition text-sm active:scale-95 shadow-sm flex items-center justify-center gap-2">
                        <span id="jurnal_edit_icon">💾</span>
                        <span id="jurnal_edit_text">{{ $jurnal->status === 'revisi' ? 'Simpan Revisi & Kirim Ulang' : 'Simpan Perubahan' }}</span>
                        <span id="jurnal_edit_spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                    </button>
                    <a href="{{ route('siswa.jurnal.index') }}" class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-3 rounded-lg transition text-sm active:scale-95">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('jurnal_edit_submit')?.closest('form');
    const submitBtn = document.getElementById('jurnal_edit_submit');
    if (form && submitBtn) {
        attachUploadProgress(form, ['jurnal_edit_foto'], submitBtn);
        form.addEventListener('submit', function () {
            const hasFile = document.getElementById('jurnal_edit_foto')?.files?.length > 0;
            if (!hasFile) {
                submitBtn.disabled = true;
                document.getElementById('jurnal_edit_text').textContent  = 'Menyimpan...';
                document.getElementById('jurnal_edit_spinner').classList.remove('hidden');
                document.getElementById('jurnal_edit_icon').classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection
