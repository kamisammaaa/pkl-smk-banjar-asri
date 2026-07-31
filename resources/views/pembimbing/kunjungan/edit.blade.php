@extends('layouts.app')
@section('page-title', 'Edit Kunjungan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('pembimbing.kunjungan') }}" class="text-gray-400 hover:text-white transition-colors">← Kembali</a>
        <h2 class="text-2xl font-bold text-white drop-shadow-md">🏢 Edit Kunjungan Industri</h2>
    </div>

    @if($errors->any())
        <div class="glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-lg shadow-[0_0_15px_rgba(239,68,68,0.2)]">
            <p class="font-bold drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">⚠️ Terjadi Kesalahan:</p>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-panel p-6 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form action="{{ route('pembimbing.kunjungan.update', $kunjungan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Perusahaan</label>
                <select name="perusahaan_id" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach($perusahaanBinaan as $perusahaan)
                        <option value="{{ $perusahaan->id }}" {{ old('perusahaan_id', $kunjungan->perusahaan_id) == $perusahaan->id ? 'selected' : '' }}>
                            {{ $perusahaan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal Kunjungan</label>
                    <input type="date" name="tanggal" required value="{{ old('tanggal', $kunjungan->tanggal->format('Y-m-d')) }}" 
                           class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Status Kunjungan</label>
                    <select name="status" id="status-select" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                        <option value="rencana" {{ old('status', $kunjungan->status) == 'rencana' ? 'selected' : '' }}>Rencana Kunjungan (Akan Datang)</option>
                        <option value="selesai" {{ old('status', $kunjungan->status) == 'selesai' ? 'selected' : '' }}>Kunjungan Selesai (Terlaksana)</option>
                    </select>
                </div>
            </div>

            <!-- CONTAINER KHUSUS STATUS: RENCANA -->
            <div id="rencana-container" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Catatan Rencana Kunjungan</label>
                    <textarea name="catatan_rencana" id="catatan-rencana-input" rows="4" 
                              placeholder="Deskripsikan agenda rencana kunjungan..."
                              class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">{{ old('catatan_rencana', $kunjungan->catatan_rencana) }}</textarea>
                </div>
            </div>

            <!-- CONTAINER KHUSUS STATUS: SELESAI -->
            <div id="selesai-container" class="space-y-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Catatan Hasil Kunjungan</label>
                    <textarea name="catatan" id="catatan-input" rows="4" 
                              placeholder="Deskripsikan hasil kunjungan, kondisi siswa, hasil evaluasi, dll..."
                              class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">{{ old('catatan', $kunjungan->catatan) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Foto Dokumentasi Kunjungan</label>
                    <div class="glass-panel p-4 rounded-xl shadow-inner border border-white/5">
                        <x-upload-foto
                            name="foto"
                            id="kunjungan_edit_foto"
                            accept="image/*"
                            :required="false"
                            :max-mb="20"
                            btn-color="emerald"
                            hint="📸 Pilih file baru jika ingin mengganti foto. Akan dikompres otomatis. Format: JPG, PNG."
                            :existing-url="$kunjungan->foto ? Storage::url($kunjungan->foto) : null"
                            existing-label="Foto dokumentasi kunjungan saat ini tersimpan."
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('pembimbing.kunjungan') }}" class="px-6 py-2.5 bg-white/10 text-gray-300 hover:text-white border border-white/20 rounded-lg hover:bg-white/20 transition-colors font-bold active:scale-95">Batal</a>
                <button type="submit" id="kunjungan_edit_submit" class="px-6 py-2.5 bg-crypto-success hover:bg-emerald-500 text-white rounded-lg font-bold transition-colors flex items-center gap-2 shadow-[0_0_15px_rgba(14,203,129,0.3)] active:scale-95">
                    <span id="kunjungan_edit_icon">💾</span>
                    <span id="kunjungan_edit_text">Simpan Perubahan</span>
                    <span id="kunjungan_edit_spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.getElementById('status-select');
        const rencanaContainer = document.getElementById('rencana-container');
        const selesaiContainer = document.getElementById('selesai-container');
        const catatanRencanaInput = document.getElementById('catatan-rencana-input');
        const catatanInput = document.getElementById('catatan-input');

        function toggleFields() {
            if (statusSelect.value === 'rencana') {
                rencanaContainer.classList.remove('hidden');
                selesaiContainer.classList.add('hidden');
                catatanRencanaInput.required = true;
                catatanInput.required = false;
            } else {
                rencanaContainer.classList.add('hidden');
                selesaiContainer.classList.remove('hidden');
                catatanRencanaInput.required = false;
                catatanInput.required = true;
            }
        }

        statusSelect.addEventListener('change', toggleFields);
        toggleFields(); // Jalankan saat load pertama kali

        // Upload progress
        const form      = document.querySelector('form');
        const submitBtn = document.getElementById('kunjungan_edit_submit');
        if (form && submitBtn) {
            attachUploadProgress(form, ['kunjungan_edit_foto'], submitBtn);
            form.addEventListener('submit', function () {
                const hasFile = document.getElementById('kunjungan_edit_foto')?.files?.length > 0;
                if (!hasFile) {
                    submitBtn.disabled = true;
                    document.getElementById('kunjungan_edit_text').textContent  = 'Menyimpan...';
                    document.getElementById('kunjungan_edit_spinner').classList.remove('hidden');
                    document.getElementById('kunjungan_edit_icon').classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
