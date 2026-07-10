@extends('layouts.app')
@section('page-title', 'Penilaian Akhir PKL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('pembimbing.dashboard') }}" class="text-gray-500 hover:text-gray-700">← Kembali</a>
        <h2 class="text-xl font-bold text-gray-800">🎯 Penilaian Akhir PKL</h2>
    </div>

    <!-- Info Siswa -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xl font-bold">
                {{ strtoupper(substr($siswa->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="font-bold text-lg text-gray-800">{{ $siswa->name }}</h3>
                <p class="text-sm text-gray-600">🎓 {{ $profile->jurusan->nama }} | 🏢 {{ $profile->perusahaan->nama }}</p>
                <p class="text-xs text-gray-500">NIS: {{ $profile->nis }} • Pembimbing: {{ $profile->pembimbing->name }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg">
            <p class="font-semibold">✅ {{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('pembimbing.penilaian.final.store', $siswa->id) }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- 1. Nilai Absensi (Auto) -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">📅 Nilai Absensi (30%)</h4>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
                <div class="bg-green-50 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-green-700">{{ $hadir }}</div>
                    <div class="text-xs text-green-600">✅ Hadir</div>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-yellow-700">{{ $terlambat }}</div>
                    <div class="text-xs text-yellow-600">⏰ Terlambat</div>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-blue-700">{{ $izin }}</div>
                    <div class="text-xs text-blue-600">📝 Izin</div>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-orange-700">{{ $sakit }}</div>
                    <div class="text-xs text-orange-600">🤒 Sakit</div>
                </div>
                <div class="bg-red-50 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-red-700">{{ $alpha }}</div>
                    <div class="text-xs text-red-600">❌ Alpha</div>
                </div>
            </div>
            <input type="hidden" name="nilai_absensi" value="{{ $nilaiAbsensi }}">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">Nilai Absensi Otomatis:</span>
                <span class="text-2xl font-bold text-blue-600">{{ $nilaiAbsensi }}/100</span>
            </div>
        </div>

        <!-- 2. Nilai Jurnal (Auto) -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">📖 Nilai Jurnal (40%)</h4>
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Jurnal Disetujui:</span>
                <span class="font-semibold">{{ $jurnalDisetujui->count() }} entri</span>
            </div>
            <input type="hidden" name="nilai_jurnal" value="{{ $rataJurnal }}">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-600">Rata-rata Nilai Jurnal:</span>
                <span class="text-2xl font-bold text-green-600">{{ $rataJurnal }}/100</span>
            </div>
            @if($jurnalDisetujui->count() > 0)
                <p class="text-xs text-gray-500 mt-2">💡 Nilai dihitung dari jurnal yang sudah disetujui + nilai harian yang Anda input</p>
            @endif
        </div>

        <!-- 3. Nilai Sikap & Kinerja (Manual) -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">👤 Sikap & Kinerja (30%)</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Sikap & Kinerja (0-100)</label>
                    <div class="flex items-center gap-4">
                        <input type="range" name="nilai_sikap" min="0" max="100" value="{{ old('nilai_sikap', $penilaian->nilai_sikap ?? 80) }}" 
                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                               oninput="document.getElementById('sikap-value').textContent = this.value">
                        <span id="sikap-value" class="text-2xl font-bold text-orange-600 w-16 text-center">
                            {{ old('nilai_sikap', $penilaian->nilai_sikap ?? 80) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>0</span><span>50</span><span>100</span>
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-2 text-center text-xs">
                    <div class="p-2 bg-red-50 rounded">0-59<br>E</div>
                    <div class="p-2 bg-yellow-50 rounded">60-69<br>D</div>
                    <div class="p-2 bg-blue-50 rounded">70-79<br>C</div>
                    <div class="p-2 bg-green-50 rounded">80-89<br>B</div>
                    <div class="p-2 bg-emerald-50 rounded">90-100<br>A</div>
                </div>
            </div>
        </div>

        <!-- 4. Nilai Akhir (Auto-Calc) -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-xl border border-blue-200">
            <h4 class="font-bold text-blue-800 mb-4">🎯 Perhitungan Nilai Akhir</h4>
            <div class="space-y-2 text-sm">
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
                    <span class="font-medium"><span id="input-sikap">{{ old('nilai_sikap', $penilaian->nilai_sikap ?? 80) }}</span> × 0.3 = <span id="calc-sikap">{{ round((old('nilai_sikap', $penilaian->nilai_sikap ?? 80)) * 0.3) }}</span></span>
                </div>
                <div class="border-t pt-3 mt-3 flex justify-between items-center">
                    <span class="font-bold text-lg text-blue-800">NILAI AKHIR:</span>
                    <span id="nilai-akhir-display" class="text-3xl font-bold text-blue-700">
                        {{ round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * (old('nilai_sikap', $penilaian->nilai_sikap ?? 80)))) }}
                    </span>
                    <span id="grade-display" class="px-3 py-1 rounded-full text-sm font-bold {{ 
                        round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * (old('nilai_sikap', $penilaian->nilai_sikap ?? 80)))) >= 80 ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800' 
                    }}">
                        {{ 
                            match(true) {
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * (old('nilai_sikap', $penilaian->nilai_sikap ?? 80)))) >= 90 => 'A',
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * (old('nilai_sikap', $penilaian->nilai_sikap ?? 80)))) >= 80 => 'B',
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * (old('nilai_sikap', $penilaian->nilai_sikap ?? 80)))) >= 70 => 'C',
                                round((0.3 * $nilaiAbsensi) + (0.4 * $rataJurnal) + (0.3 * (old('nilai_sikap', $penilaian->nilai_sikap ?? 80)))) >= 60 => 'D',
                                default => 'E'
                            }
                        }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Catatan Akhir -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">📝 Catatan Akhir untuk Siswa</label>
            <textarea name="catatan_akhir" rows="4" placeholder="Berikan motivasi, saran pengembangan, atau apresiasi untuk siswa..." 
                      class="w-full border rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">{{ old('catatan_akhir', $penilaian->catatan_akhir ?? '') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('pembimbing.dashboard') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Batal</a>
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg active:scale-95 transition">
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
        if (akhir >= 90) { grade = 'A'; colorClass = 'bg-emerald-200 text-emerald-800'; }
        else if (akhir >= 80) { grade = 'B'; colorClass = 'bg-green-200 text-green-800'; }
        else if (akhir >= 70) { grade = 'C'; colorClass = 'bg-blue-200 text-blue-800'; }
        else if (akhir >= 60) { grade = 'D'; colorClass = 'bg-yellow-200 text-yellow-800'; }
        else { grade = 'E'; colorClass = 'bg-red-200 text-red-800'; }
        
        gradeDisplay.textContent = grade;
        gradeDisplay.className = `px-3 py-1 rounded-full text-sm font-bold ${colorClass}`;
    }
    
    slider.addEventListener('input', updateCalc);
    
    // Set initial values
    calcAbsen.textContent = Math.round(nilaiAbsen * 0.3);
    calcJurnal.textContent = Math.round(nilaiJurnal * 0.4);
    updateCalc();
});
</script>
@endsection