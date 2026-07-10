@extends('layouts.app')
@section('page-title', 'Penilaian Akhir Siswa')

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-6 px-2">
    
    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500">
        <a href="{{ route('pembimbing.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('pembimbing.siswa-binaan') }}" class="hover:text-blue-600">Siswa Binaan</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Penilaian Akhir</span>
    </nav>

    {{-- Header Siswa --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white text-xl font-bold shadow-md">
                {{ strtoupper(substr($siswa->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $siswa->name }}</h2>
                <p class="text-sm text-gray-500">
                    NIS: {{ $profile?->nis ?? '-' }} | 
                    {{ $profile?->jurusan?->nama ?? '-' }} / 
                    {{ $profile?->kelas ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Ringkasan Perhitungan Nilai --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center gap-2">
            Komponen Nilai
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- 1. Nilai Absensi (Persentase) --}}
            <div class="p-4 bg-green-50 rounded-lg border border-green-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-2 opacity-10">
                    <svg class="w-16 h-16 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                </div>
                <div class="text-sm text-green-700 font-medium">Nilai Absensi (30%)</div>
                <div class="text-4xl font-extrabold text-green-800 mt-2">{{ $nilaiAbsensi }}%</div>
                
                <div class="mt-3 space-y-1 text-xs text-green-700 border-t border-green-200 pt-2">
                    <div class="flex justify-between">
                        <span>Periode PKL:</span>
                        <span class="font-semibold">{{ $absensiSummary['start_date'] }} s/d {{ $absensiSummary['end_date'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Hari Kerja:</span>
                        <span class="font-semibold">{{ $absensiSummary['total_working_days'] }} hari</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hadir Terverifikasi:</span>
                        <span class="font-semibold">{{ $absensiSummary['verified_days'] }} hari</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tidak Hadir:</span>
                        <span class="font-semibold text-red-600">{{ $absensiSummary['missing_days'] }} hari</span>
                    </div>
                </div>
                
                <div class="mt-2 w-full bg-green-200 rounded-full h-1.5">
                    <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $nilaiAbsensi }}%"></div>
                </div>
            </div>

            {{-- 2. Nilai Jurnal (Rata-rata) --}}
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-2 opacity-10">
                    <svg class="w-16 h-16 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                </div>
                <div class="text-sm text-blue-700 font-medium">Nilai Jurnal (40%)</div>
                <div class="text-4xl font-extrabold text-blue-800 mt-2">{{ round($nilaiJurnal) }}</div>
                <div class="mt-3 text-xs text-blue-700 border-t border-blue-200 pt-2">
                    <div class="flex justify-between">
                        <span>Jurnal Disetujui:</span>
                        <span class="font-semibold">{{ $siswa->jurnals->where('status', 'disetujui')->count() }} jurnal</span>
                    </div>
                    <p class="mt-2 text-gray-500 italic">Dihitung dari rata-rata nilai jurnal yang telah di-approve.</p>
                </div>
            </div>

            {{-- 3. Input Nilai Sikap (Manual) --}}
            <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                <div class="text-sm text-purple-700 font-medium">Nilai Sikap (30%)</div>
                <div class="mt-2">
                    <label class="block text-xs font-bold text-purple-800 mb-1">Input Nilai Sikap:</label>
                    <input type="number" name="nilai_sikap" min="0" max="100" 
                           value="{{ old('nilai_sikap', $penilaian->nilai_sikap ?? 80) }}"
                           required
                           id="inputSikap"
                           class="w-full border-2 border-purple-300 rounded-lg px-3 py-2 text-3xl font-bold text-center text-purple-800 focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition">
                </div>
                <div class="mt-3 text-xs text-purple-700 border-t border-purple-200 pt-2">
                    <p class="mb-1">��� <strong>Panduan:</strong></p>
                    <ul class="list-disc list-inside space-y-0.5 text-gray-600">
                        <li>Kedisiplinan waktu (terlambat)</li>
                        <li>Sikap sopan santun</li>
                        <li>Inisiatif & Kerjasama</li>
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- Preview Nilai Akhir --}}
        <div class="mt-4 p-3 bg-gray-100 rounded-lg text-center border border-gray-200">
            <span class="text-sm text-gray-600">Estimasi Nilai Akhir: </span>
            <span id="previewNilai" class="text-2xl font-bold text-gray-800">
                {{ round((0.3 * $nilaiAbsensi) + (0.4 * $nilaiJurnal) + (0.3 * ($penilaian->nilai_sikap ?? 80))) }}
            </span>
            <span class="text-sm text-gray-500">/ 100</span>
        </div>
    </div>

    {{-- Form Simpan Penilaian --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('pembimbing.nilai.store', $siswa->id) }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Hidden Inputs: Membawa data perhitungan ke controller --}}
            <input type="hidden" name="nilai_absensi" value="{{ $nilaiAbsensi }}">
            <input type="hidden" name="nilai_jurnal" value="{{ round($nilaiJurnal) }}">
            
            {{-- Catatan Akhir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Akhir Pembimbing
                </label>
                <textarea name="catatan_akhir" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                          placeholder="Tuliskan catatan atau saran untuk siswa...">{{ old('catatan_akhir', $penilaian->catatan_akhir ?? '') }}</textarea>
            </div>
            
            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white font-bold px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl active:scale-95 flex items-center justify-center gap-2">
                    <span>���</span> Simpan Penilaian
                </button>
                <a href="{{ route('pembimbing.nilai.index') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition text-center flex items-center justify-center gap-2">
                    <span>❌</span> Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Script Realtime Calculation --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil nilai tetap dari controller (PHP -> JS)
    const nilaiAbsensi = {{ $nilaiAbsensi }};
    const nilaiJurnal = {{ round($nilaiJurnal) }};
    
    // Element
    const inputSikap = document.getElementById('inputSikap');
    const previewNilai = document.getElementById('previewNilai');
    
    // Fungsi Hitung
    function updatePreview() {
        let sikap = parseInt(inputSikap.value) || 0;
        
        // Clamp value 0-100
        if (sikap > 100) sikap = 100;
        if (sikap < 0) sikap = 0;
        
        // Rumus: 30% Absensi + 40% Jurnal + 30% Sikap
        const akhir = Math.round((0.3 * nilaiAbsensi) + (0.4 * nilaiJurnal) + (0.3 * sikap));
        
        previewNilai.textContent = akhir;
        
        // Ubah warna preview berdasarkan nilai
        if (akhir >= 85) previewNilai.className = 'text-2xl font-bold text-green-600';
        else if (akhir >= 70) previewNilai.className = 'text-2xl font-bold text-blue-600';
        else previewNilai.className = 'text-2xl font-bold text-red-600';
    }
    
    // Event Listener
    inputSikap.addEventListener('input', updatePreview);
    
    // Initial run
    updatePreview();
});
</script>
@endsection
