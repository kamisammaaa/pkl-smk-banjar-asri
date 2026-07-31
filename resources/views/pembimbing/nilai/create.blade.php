@extends('layouts.app')
@section('page-title', 'Penilaian Akhir Siswa')

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-6 px-2">
    
    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400">
        <a href="{{ route('pembimbing.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="{{ route('pembimbing.siswa-binaan') }}" class="hover:text-white transition-colors">Siswa Binaan</a>
        <span class="mx-2">/</span>
        <span class="text-gray-200 font-bold">Penilaian Akhir</span>
    </nav>

    {{-- Header Siswa --}}
    <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-crypto-accent to-purple-800 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                {{ strtoupper(substr($siswa->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-white drop-shadow-md">{{ $siswa->name }}</h2>
                <p class="text-sm text-gray-400 mt-1">
                    NIS: {{ $profile?->nis ?? '-' }} | 
                    {{ $profile?->jurusan?->nama ?? '-' }} / 
                    {{ $profile?->kelas ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Ringkasan Perhitungan Nilai --}}
    <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <h3 class="text-lg font-bold mb-4 text-white drop-shadow-md flex items-center gap-2">
            Komponen Nilai
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- 1. Nilai Absensi (Persentase) --}}
            <div class="p-4 bg-green-500/10 rounded-lg border border-green-500/30 relative overflow-hidden shadow-inner">
                <div class="absolute top-0 right-0 p-2 opacity-10">
                    <svg class="w-16 h-16 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                </div>
                <div class="text-sm text-green-400 font-bold">Nilai Absensi (30%)</div>
                <div class="text-4xl font-extrabold text-green-400 mt-2 drop-shadow-[0_0_5px_rgba(34,197,94,0.5)]">{{ $nilaiAbsensi }}%</div>
                
                <div class="mt-3 space-y-1 text-xs text-green-300 border-t border-green-500/30 pt-2">
                    <div class="flex justify-between">
                        <span>Periode PKL:</span>
                        <span class="font-bold">{{ $absensiSummary['start_date'] }} s/d {{ $absensiSummary['end_date'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Hari Kerja:</span>
                        <span class="font-bold">{{ $absensiSummary['total_working_days'] }} hari</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hadir Terverifikasi:</span>
                        <span class="font-bold">{{ $absensiSummary['verified_days'] }} hari</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tidak Hadir:</span>
                        <span class="font-bold text-red-400">{{ $absensiSummary['missing_days'] }} hari</span>
                    </div>
                </div>
                
                <div class="mt-2 w-full bg-green-900/50 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-green-500 h-1.5 rounded-full shadow-[0_0_10px_rgba(34,197,94,0.8)]" style="width: {{ $nilaiAbsensi }}%"></div>
                </div>
            </div>

            {{-- 2. Nilai Jurnal (Rata-rata) --}}
            <div class="p-4 bg-blue-500/10 rounded-lg border border-blue-500/30 relative overflow-hidden shadow-inner">
                <div class="absolute top-0 right-0 p-2 opacity-10">
                    <svg class="w-16 h-16 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                </div>
                <div class="text-sm text-blue-400 font-bold">Nilai Jurnal (40%)</div>
                <div class="text-4xl font-extrabold text-blue-400 mt-2 drop-shadow-[0_0_5px_rgba(59,130,246,0.5)]">{{ round($nilaiJurnal) }}</div>
                <div class="mt-3 text-xs text-blue-300 border-t border-blue-500/30 pt-2">
                    <div class="flex justify-between">
                        <span>Jurnal Disetujui:</span>
                        <span class="font-bold">{{ $siswa->jurnals->where('status', 'disetujui')->count() }} jurnal</span>
                    </div>
                    <p class="mt-2 text-blue-300/70 italic">Dihitung dari rata-rata nilai jurnal yang telah di-approve.</p>
                </div>
            </div>

            {{-- 3. Input Nilai Sikap (Manual) --}}
            <div class="p-4 bg-crypto-accent/10 rounded-lg border border-crypto-accent/30 shadow-inner">
                <div class="text-sm text-crypto-accent font-bold">Nilai Sikap (30%)</div>
                <div class="mt-2">
                    <label class="block text-xs font-bold text-purple-300 mb-1">Input Nilai Sikap:</label>
                    <input type="number" name="nilai_sikap" min="0" max="100" 
                           value="{{ old('nilai_sikap', $penilaian->nilai_sikap ?? 0) }}"
                           required
                           id="inputSikap"
                           class="w-full bg-crypto-dark border-2 border-crypto-accent/50 rounded-lg px-3 py-2 text-3xl font-bold text-center text-white placeholder-purple-400 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent shadow-inner transition-colors">
                </div>
                <div class="mt-3 text-xs text-purple-300 border-t border-crypto-accent/30 pt-2">
                    <p class="mb-1 font-bold">💡 Panduan:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-purple-200">
                        <li>Kedisiplinan waktu (terlambat)</li>
                        <li>Sikap sopan santun</li>
                        <li>Inisiatif & Kerjasama</li>
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- Preview Nilai Akhir --}}
        <div class="mt-4 p-3 bg-white/5 rounded-lg text-center border border-white/10 shadow-inner">
            <span class="text-sm text-gray-300">Estimasi Nilai Akhir: </span>
            <span id="previewNilai" class="text-2xl font-bold text-white drop-shadow-[0_0_5px_rgba(255,255,255,0.5)]">
                {{ round((0.3 * $nilaiAbsensi) + (0.4 * $nilaiJurnal) + (0.3 * ($penilaian->nilai_sikap ?? 0))) }}
            </span>
            <span class="text-sm text-gray-400">/ 100</span>
        </div>
    </div>

    {{-- Form Simpan Penilaian --}}
    <div class="glass-panel p-6 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form action="{{ route('pembimbing.nilai.store', $siswa->id) }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Hidden Inputs: Membawa data perhitungan ke controller --}}
            <input type="hidden" name="nilai_absensi" value="{{ $nilaiAbsensi }}">
            <input type="hidden" name="nilai_jurnal" value="{{ round($nilaiJurnal) }}">
            
            {{-- Catatan Akhir --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Catatan Akhir Pembimbing
                </label>
                <textarea name="catatan_akhir" rows="3" 
                          class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-3 text-base focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                          placeholder="Tuliskan catatan atau saran untuk siswa...">{{ old('catatan_akhir', $penilaian->catatan_akhir ?? '') }}</textarea>
            </div>
            
            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-white/10">
                <button type="submit" 
                        class="flex-1 bg-crypto-success text-white font-bold px-6 py-3 rounded-lg hover:bg-emerald-500 transition-colors shadow-[0_0_15px_rgba(14,203,129,0.3)] active:scale-95 flex items-center justify-center gap-2">
                    <span>💾</span> Simpan Penilaian
                </button>
                <a href="{{ route('pembimbing.nilai.index') }}" 
                   class="px-6 py-3 bg-white/10 text-gray-300 border border-white/20 font-bold rounded-lg hover:bg-white/20 hover:text-white transition-colors text-center flex items-center justify-center gap-2 active:scale-95">
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
        if (akhir >= 85) previewNilai.className = 'text-2xl font-bold text-crypto-success drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]';
        else if (akhir >= 70) previewNilai.className = 'text-2xl font-bold text-blue-400 drop-shadow-[0_0_5px_rgba(59,130,246,0.5)]';
        else previewNilai.className = 'text-2xl font-bold text-red-400 drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]';
    }
    
    // Event Listener
    inputSikap.addEventListener('input', updatePreview);
    
    // Initial run
    updatePreview();
});
</script>
@endsection
