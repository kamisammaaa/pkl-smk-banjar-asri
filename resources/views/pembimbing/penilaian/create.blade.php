@extends('layouts.app')
@section('page-title', 'Penilaian Akhir PKL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('pembimbing.dashboard') }}" class="text-gray-400 hover:text-white transition-colors">← Kembali</a>
        <h2 class="text-xl font-bold text-white drop-shadow-md">🎯 Penilaian Akhir PKL</h2>
    </div>

    <!-- Info Siswa -->
    <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-crypto-accent to-purple-800 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                {{ strtoupper(substr($siswa->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="font-bold text-lg text-white">{{ $siswa->name }}</h3>
                <p class="text-sm text-gray-300">🎓 {{ $profile->jurusan->nama }} | 🏢 {{ $profile->perusahaan->nama }}</p>
                <p class="text-xs text-gray-400">NIS: {{ $profile->nis }} • Pembimbing: {{ $profile->pembimbing->name }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.2)]">
            <p class="font-semibold">✅ {{ session('success') }}</p>
        </div>
    @endif

    @php
        $sikapNilai = old('nilai_sikap', $penilaian->nilai_sikap ?? 0);
    @endphp

    <form action="{{ route('pembimbing.penilaian.final.store', $siswa->id) }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- 1. Nilai Absensi (Auto) -->
        <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
            <h4 class="font-bold text-white drop-shadow-md mb-4 flex items-center gap-2">📅 Nilai Absensi (30%)</h4>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
                <div class="bg-crypto-success/20 border border-crypto-success/30 p-3 rounded-lg text-center shadow-inner">
                    <div class="text-xl font-bold text-crypto-success">{{ $hadir }}</div>
                    <div class="text-xs text-emerald-400">✅ Hadir</div>
                </div>
                <div class="bg-amber-500/20 border border-amber-500/30 p-3 rounded-lg text-center shadow-inner">
                    <div class="text-xl font-bold text-amber-400">{{ $terlambat }}</div>
                    <div class="text-xs text-amber-400">⏰ Terlambat</div>
                </div>
                <div class="bg-blue-500/20 border border-blue-500/30 p-3 rounded-lg text-center shadow-inner">
                    <div class="text-xl font-bold text-blue-400">{{ $izin }}</div>
                    <div class="text-xs text-blue-400">📝 Izin</div>
                </div>
                <div class="bg-orange-500/20 border border-orange-500/30 p-3 rounded-lg text-center shadow-inner">
                    <div class="text-xl font-bold text-orange-400">{{ $sakit }}</div>
                    <div class="text-xs text-orange-400">🤒 Sakit</div>
                </div>
                <div class="bg-red-500/20 border border-red-500/30 p-3 rounded-lg text-center shadow-inner">
                    <div class="text-xl font-bold text-red-400">{{ $alpha }}</div>
                    <div class="text-xs text-red-400">❌ Alpha</div>
                </div>
            </div>
            <input type="hidden" name="nilai_absensi" value="{{ $nilaiAbsensi }}">
            <div class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-lg shadow-inner">
                <span class="text-sm text-gray-300">Nilai Absensi Otomatis:</span>
                <span class="text-2xl font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">{{ $nilaiAbsensi }}/100</span>
            </div>
            @if($hadir === 0 && $terlambat === 0 && $izin === 0 && $sakit === 0 && $alpha === 0)
                <p class="text-xs text-amber-400 mt-2">⚠️ Belum ada data absensi terverifikasi, sehingga nilai absensi masih 0.</p>
            @endif
        </div>

        <!-- 2. Nilai Jurnal (Auto) -->
        <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
            <h4 class="font-bold text-white drop-shadow-md mb-4 flex items-center gap-2">📖 Nilai Jurnal (40%)</h4>
            <div class="flex items-center justify-between mb-3 text-gray-200">
                <span class="text-sm text-gray-300">Jurnal Disetujui:</span>
                <span class="font-bold">{{ $jurnalDisetujui->count() }} entri</span>
            </div>
            <input type="hidden" name="nilai_jurnal" value="{{ $rataJurnal }}">
            <div class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-lg shadow-inner">
                <span class="text-sm text-gray-300">Rata-rata Nilai Jurnal:</span>
                <span class="text-2xl font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">{{ $rataJurnal }}/100</span>
            </div>
            @if($jurnalDisetujui->count() > 0)
                <p class="text-xs text-gray-400 mt-2">💡 Nilai dihitung dari jurnal yang sudah disetujui.</p>
            @else
                <p class="text-xs text-amber-400 mt-2">⚠️ Belum ada jurnal yang disetujui, sehingga nilai jurnal saat ini 0.</p>
            @endif
        </div>

        <!-- 3. Nilai Sikap & Kinerja (Manual) -->
        <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
            <h4 class="font-bold text-white drop-shadow-md mb-4 flex items-center gap-2">👤 Sikap & Kinerja (30%)</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nilai Sikap & Kinerja (0-100)</label>
                    <div class="flex items-center gap-4">
                        <input type="range" name="nilai_sikap" min="0" max="100" value="{{ $sikapNilai }}" 
                               class="flex-1 h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer"
                               oninput="document.getElementById('sikap-value').textContent = this.value">
                        <span id="sikap-value" class="text-2xl font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)] w-16 text-center">
                            {{ $sikapNilai }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>0</span><span>50</span><span>100</span>
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-2 text-center text-xs">
                    <div class="p-2 bg-red-500/20 border border-red-500/30 text-red-400 rounded shadow-inner">0-59<br>E</div>
                    <div class="p-2 bg-yellow-500/20 border border-yellow-500/30 text-yellow-400 rounded shadow-inner">60-69<br>D</div>
                    <div class="p-2 bg-blue-500/20 border border-blue-500/30 text-blue-400 rounded shadow-inner">70-79<br>C</div>
                    <div class="p-2 bg-green-500/20 border border-green-500/30 text-green-400 rounded shadow-inner">80-89<br>B</div>
                    <div class="p-2 bg-crypto-success/20 border border-crypto-success/30 text-crypto-success rounded shadow-inner">90-100<br>A</div>
                </div>
            </div>
        </div>

        <!-- 4. Nilai Akhir (Auto-Calc) -->
        <div class="glass-panel bg-gradient-to-r from-crypto-dark/80 to-purple-900/30 border border-crypto-accent/50 shadow-[0_0_15px_rgba(112,0,255,0.2)] p-5 rounded-xl">
            <h4 class="font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)] mb-4">🎯 Perhitungan Nilai Akhir</h4>
            <div class="space-y-2 text-sm text-gray-200">
                <div class="flex justify-between">
                    <span>Absensi (30%):</span>
                    <span class="font-medium">{{ $nilaiAbsensi }} × 0.3 = <span id="calc-absen">{{ round($nilaiAbsensi * 0.3) }}</span></span>
                </div>
                <div class="flex justify-between">
                    <span>Jurnal (40%):</span>
                    <span class="font-medium">{{ $rataJurnal }} × 0.4 = <span id="calc-jurnal">{{ round($rataJurnal * 0.4) }}</span></span>
                </div>
                <div class="flex justify-between">
                    <span>Sikap (30%):</span>
                    <span class="font-medium"><span id="input-sikap">{{ $sikapNilai }}</span> × 0.3 = <span id="calc-sikap">{{ round($sikapNilai * 0.3) }}</span></span>
                </div>
                <div class="border-t border-white/20 pt-3 mt-3 flex justify-between items-center">
                    <span class="font-bold text-lg text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">NILAI AKHIR:</span>
                    <span id="nilai-akhir-display" class="text-3xl font-bold text-white drop-shadow-[0_0_5px_rgba(255,255,255,0.5)]">
                        {{ round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * $sikapNilai)) }}
                    </span>
                    <span id="grade-display" class="px-3 py-1 rounded-full text-sm font-bold border shadow-inner {{ 
                        round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * $sikapNilai)) >= 80 ? 'bg-crypto-success/20 text-crypto-success border-crypto-success/30' : 'bg-blue-500/20 text-blue-400 border-blue-500/30' 
                    }}">
                        {{ 
                            match(true) {
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * $sikapNilai)) >= 90 => 'A',
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * $sikapNilai)) >= 80 => 'B',
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * $sikapNilai)) >= 70 => 'C',
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * $sikapNilai)) >= 60 => 'D',
                                default => 'E'
                            }
                        }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Catatan Akhir -->
        <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
            <label class="block text-sm font-medium text-gray-300 mb-2">📝 Catatan Akhir untuk Siswa</label>
            <textarea name="catatan_akhir" rows="4" placeholder="Berikan motivasi, saran pengembangan, atau apresiasi untuk siswa..." 
                      class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">{{ old('catatan_akhir', $penilaian->catatan_akhir ?? '') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('pembimbing.dashboard') }}" class="px-6 py-3 bg-white/10 border border-white/20 text-gray-300 hover:text-white rounded-lg hover:bg-white/20 font-bold transition-colors active:scale-95">Batal</a>
            <button type="submit" class="px-6 py-3 bg-crypto-success text-white rounded-lg hover:bg-emerald-500 font-bold shadow-[0_0_15px_rgba(14,203,129,0.3)] active:scale-95 transition-colors">
                💾 Simpan Nilai Akhir
            </button>
        </div>
    </form>
</div>

<!-- JavaScript: Auto-calc nilai akhir -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('input[name="nilai_sikap"]');
    const sikapValue = document.getElementById('sikap-value');
    const inputSikap = document.getElementById('input-sikap');
    const calcSikap = document.getElementById('calc-sikap');
    const calcAbsen = document.getElementById('calc-absen');
    const calcJurnal = document.getElementById('calc-jurnal');
    const nilaiAkhirDisplay = document.getElementById('nilai-akhir-display');
    const gradeDisplay = document.getElementById('grade-display');
    
    const nilaiAbsen = {{ $nilaiAbsensi }};
    const nilaiJurnal = {{ $rataJurnal }};
    
    function updateCalc() {
        const sikap = parseInt(slider.value);
        sikapValue.textContent = sikap;
        inputSikap.textContent = sikap;
        calcSikap.textContent = Math.round(sikap * 0.3);
        
        const akhir = Math.round((0.3 * nilaiAbsen) + (0.4 * nilaiJurnal) + (0.3 * sikap));
        nilaiAkhirDisplay.textContent = akhir;
        
        // Update grade & color
        let grade, colorClass;
        if (akhir >= 90) { grade = 'A'; colorClass = 'bg-crypto-success/20 text-crypto-success border-crypto-success/30'; }
        else if (akhir >= 80) { grade = 'B'; colorClass = 'bg-green-500/20 text-green-400 border-green-500/30'; }
        else if (akhir >= 70) { grade = 'C'; colorClass = 'bg-blue-500/20 text-blue-400 border-blue-500/30'; }
        else if (akhir >= 60) { grade = 'D'; colorClass = 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30'; }
        else { grade = 'E'; colorClass = 'bg-red-500/20 text-red-400 border-red-500/30'; }
        
        gradeDisplay.textContent = grade;
        gradeDisplay.className = `px-3 py-1 rounded-full text-sm font-bold border shadow-inner ${colorClass}`;
    }
    
    slider.addEventListener('input', updateCalc);
    
    // Set initial values
    calcAbsen.textContent = Math.round(nilaiAbsen * 0.3);
    calcJurnal.textContent = Math.round(nilaiJurnal * 0.4);
    updateCalc();
});
</script>
@endsection