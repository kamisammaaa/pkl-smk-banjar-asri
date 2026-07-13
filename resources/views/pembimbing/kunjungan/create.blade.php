@extends('layouts.app')
@section('page-title', 'Input Kunjungan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('pembimbing.kunjungan') }}" class="text-gray-500 hover:text-gray-700">← Kembali</a>
        <h2 class="text-2xl font-bold text-gray-800">🏢 Input Kunjungan Industri</h2>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg">
            <p class="font-semibold">⚠️ Terjadi Kesalahan:</p>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('pembimbing.kunjungan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Perusahaan Binaan</label>
                <select name="perusahaan_id" required class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200">
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach($perusahaanBinaan as $perusahaan)
                        <option value="{{ $perusahaan->id }}" {{ old('perusahaan_id') == $perusahaan->id ? 'selected' : '' }}>
                            {{ $perusahaan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>
                    <input type="date" name="tanggal" required value="{{ old('tanggal', date('Y-m-d')) }}" 
                           class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Kunjungan</label>
                    <select name="status" id="status-select" required class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200">
                        <option value="rencana" {{ old('status', 'rencana') == 'rencana' ? 'selected' : '' }}>Rencana Kunjungan (Akan Datang)</option>
                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Kunjungan Selesai (Terlaksana)</option>
                    </select>
                </div>
            </div>

            <!-- CONTAINER KHUSUS STATUS: RENCANA -->
            <div id="rencana-container" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Rencana Kunjungan</label>
                    <textarea name="catatan_rencana" id="catatan-rencana-input" rows="4" 
                              placeholder="Deskripsikan agenda rencana kunjungan..."
                              class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200">{{ old('catatan_rencana') }}</textarea>
                </div>
            </div>

            <!-- CONTAINER KHUSUS STATUS: SELESAI -->
            <div id="selesai-container" class="space-y-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Hasil Kunjungan</label>
                    <textarea name="catatan" id="catatan-input" rows="4" 
                              placeholder="Deskripsikan hasil kunjungan, kondisi siswa, hasil evaluasi, dll..."
                              class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200">{{ old('catatan') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Dokumentasi Kunjungan</label>
                    <x-upload-foto
                        name="foto"
                        id="kunjungan_foto_input"
                        accept="image/*"
                        :required="false"
                        :max-mb="20"
                        btn-color="blue"
                        hint="📸 Upload foto dokumentasi kunjungan. Akan dikompres otomatis. Format: JPG, PNG."
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('pembimbing.kunjungan') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Batal</a>
                <button type="submit" id="kunjungan_create_submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition flex items-center gap-2">
                    <span id="kunjungan_create_icon">💾</span>
                    <span id="kunjungan_create_text">Simpan Kunjungan</span>
                    <span id="kunjungan_create_spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
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
        const form      = document.querySelector('form[action="{{ route('pembimbing.kunjungan.store') }}"]');
        const submitBtn = document.getElementById('kunjungan_create_submit');
        if (form && submitBtn) {
            attachUploadProgress(form, ['kunjungan_foto_input'], submitBtn);
            form.addEventListener('submit', function () {
                const hasFile = document.getElementById('kunjungan_foto_input')?.files?.length > 0;
                if (!hasFile) {
                    submitBtn.disabled = true;
                    document.getElementById('kunjungan_create_text').textContent  = 'Menyimpan...';
                    document.getElementById('kunjungan_create_spinner').classList.remove('hidden');
                    document.getElementById('kunjungan_create_icon').classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection