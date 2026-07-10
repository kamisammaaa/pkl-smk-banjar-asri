@extends('layouts.app')
@section('page-title', 'Tambah Absensi Harian')

@section('content')
<div class="w-full max-w-2xl mx-auto space-y-6 px-2">
    
    {{-- Back Button & Title --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            📅 Input Absensi Hari Ini
            <span class="text-sm font-normal text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
        </h2>
        <a href="{{ route('siswa.absensi.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition border border-gray-300">
            ⬅️ Kembali
        </a>
    </div>

    {{-- ⏰ Jam Realtime --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-4 text-white shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">Waktu Sekarang</p>
                <p id="realtimeClock" class="text-3xl font-bold tabular-nums tracking-tight">--:--:--</p>
                <p id="realtimeDate" class="text-blue-200 text-xs mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            @if(isset($perusahaan))
            <div class="text-right">
                <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">Jam Masuk</p>
                <p class="text-2xl font-bold">{{ $perusahaan->getJamMasukLabel() }}</p>
                <p class="text-blue-200 text-xs">Toleransi {{ $perusahaan->toleransi_menit ?? 15 }} menit</p>
            </div>
            @endif
        </div>
        @if(isset($perusahaan))
        <div id="lateWarningBanner" class="hidden mt-3 bg-red-500/30 border border-red-300/50 rounded-lg px-3 py-2 text-sm font-semibold text-white flex items-center gap-2">
            ⚠️ <span id="lateWarningText">Anda sudah terlambat!</span>
        </div>
        @endif
    </div>

    {{-- Info Periode PKL --}}
    @if(isset($periode))
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700 flex items-start gap-3">
        <span class="text-lg">📅</span>
        <div>
            <p class="font-semibold">Periode PKL Aktif</p>
            <p class="text-blue-600">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} – {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}</p>
        </div>
    </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any()) 
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div> 
    @endif

    {{-- Form Absensi --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('siswa.absensi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="absensiForm">
            @csrf

            {{-- 1. Pilihan Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Status Absensi Hari Ini</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    
                    {{-- Hadir --}}
                    <label class="border-2 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer transition hover:border-green-400 @error('status') border-red-300 @else border-gray-200 @enderror" id="label_hadir">
                        <input type="radio" name="status" value="hadir" class="sr-only" {{ old('status', 'hadir') === 'hadir' ? 'checked' : '' }}>
                        <span class="text-2xl mb-1">✅</span>
                        <span class="text-sm font-bold text-gray-700">Hadir</span>
                        <span class="text-[10px] text-gray-400 mt-0.5">Upload selfie</span>
                    </label>
                    
                    {{-- Sakit --}}
                    <label class="border-2 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer transition hover:border-orange-400 @error('status') border-red-300 @else border-gray-200 @enderror" id="label_sakit">
                        <input type="radio" name="status" value="sakit" class="sr-only" {{ old('status') === 'sakit' ? 'checked' : '' }}>
                        <span class="text-2xl mb-1">🤒</span>
                        <span class="text-sm font-bold text-gray-700">Sakit</span>
                        <span class="text-[10px] text-gray-400 mt-0.5">Surat dokter</span>
                    </label>
                    
                    {{-- Izin --}}
                    <label class="border-2 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer transition hover:border-blue-400 @error('status') border-red-300 @else border-gray-200 @enderror" id="label_izin">
                        <input type="radio" name="status" value="izin" class="sr-only" {{ old('status') === 'izin' ? 'checked' : '' }}>
                        <span class="text-2xl mb-1">📝</span>
                        <span class="text-sm font-bold text-gray-700">Izin</span>
                        <span class="text-[10px] text-gray-400 mt-0.5">Surat izin</span>
                    </label>

                    {{-- Libur --}}
                    <label class="border-2 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer transition hover:border-purple-400 @error('status') border-red-300 @else border-gray-200 @enderror" id="label_libur">
                        <input type="radio" name="status" value="libur" class="sr-only" {{ old('status') === 'libur' ? 'checked' : '' }}>
                        <span class="text-2xl mb-1">🏖️</span>
                        <span class="text-sm font-bold text-gray-700">Libur</span>
                        <span class="text-[10px] text-gray-400 mt-0.5">Hari libur</span>
                    </label>
                </div>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ==========================================
                 DIV FOR HADIR (Selfie Foto)
                 ========================================== --}}
            <div id="section_hadir" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Foto Selfie Lokasi PKL <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="foto" 
                           id="input_foto"
                           accept="image/*" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">📸 Upload foto selfie sebagai bukti kehadiran. Waktu check-in akan direkam secara otomatis saat tombol <strong>Kirim Absensi</strong> ditekan.</p>
                    @error('foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ==========================================
                 DIV FOR SAKIT / IZIN (Reason & Document)
                 ========================================== --}}
            <div id="section_sakit_izin" class="hidden space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" id="label_alasan">
                        Alasan Tidak Hadir <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan" 
                              id="input_alasan"
                              rows="3" 
                              placeholder="Tuliskan keterangan/alasan secara detail..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition">{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="section_bukti">
                    <label class="block text-sm font-semibold text-gray-700 mb-1" id="label_bukti">
                        Dokumen Bukti <span id="bukti_required_mark" class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="bukti" 
                           id="input_bukti"
                           accept="image/*,.pdf" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1" id="bukti_hint">📄 Format: JPG, PNG, PDF. Maks. 2MB.</p>
                    @error('bukti')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ==========================================
                 DIV FOR LIBUR
                 ========================================== --}}
            <div id="section_libur" class="hidden space-y-4">
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <p class="text-sm text-purple-700 font-semibold mb-1">🏖️ Laporan Hari Libur</p>
                    <p class="text-xs text-purple-600">Gunakan status ini jika hari ini adalah hari libur nasional, libur perusahaan, atau libur resmi lainnya. Hari libur tidak dihitung dalam persentase kehadiran.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Keterangan Libur <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="alasan"
                           id="input_alasan_libur"
                           placeholder="Contoh: Libur Nasional Hari Kemerdekaan, Libur Perusahaan, dll."
                           value="{{ old('alasan') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-500 transition">
                    @error('alasan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" 
                    id="btnSubmitAbsensi"
                    class="w-full bg-blue-600 text-white font-bold px-4 py-3.5 rounded-lg hover:bg-blue-700 transition text-base active:scale-95 shadow-sm flex items-center justify-center gap-2">
                <span id="submitIcon">📤</span>
                <span id="submitText">Kirim Absensi</span>
                <span id="submitSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
            </button>

            <p class="text-center text-xs text-gray-400">
                Waktu absensi direkam <strong>saat tombol ditekan</strong> — pastikan Anda sudah siap sebelum submit.
            </p>
        </form>
    </div>
</div>

{{-- Dynamic Toggling Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form         = document.getElementById('absensiForm');
        const submitBtn    = document.getElementById('btnSubmitAbsensi');
        const submitText   = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');
        const submitIcon   = document.getElementById('submitIcon');
        
        const radioStatus     = document.getElementsByName('status');
        const sectionHadir    = document.getElementById('section_hadir');
        const sectionSakitIzin = document.getElementById('section_sakit_izin');
        const sectionLibur    = document.getElementById('section_libur');
        
        const inputFoto       = document.getElementById('input_foto');
        const inputAlasan     = document.getElementById('input_alasan');
        const inputAlasanLibur = document.getElementById('input_alasan_libur');
        const inputBukti      = document.getElementById('input_bukti');
        
        const labelHadir  = document.getElementById('label_hadir');
        const labelSakit  = document.getElementById('label_sakit');
        const labelIzin   = document.getElementById('label_izin');
        const labelLibur  = document.getElementById('label_libur');
        
        const labelAlasan = document.getElementById('label_alasan');
        const labelBukti  = document.getElementById('label_bukti');
        const buktiHint   = document.getElementById('bukti_hint');
        const buktiReqMark = document.getElementById('bukti_required_mark');

        // Color maps per status
        const statusColors = {
            'hadir': { border: 'border-green-500', bg: 'bg-green-50' },
            'sakit': { border: 'border-orange-500', bg: 'bg-orange-50' },
            'izin':  { border: 'border-blue-500',   bg: 'bg-blue-50' },
            'libur': { border: 'border-purple-500',  bg: 'bg-purple-50' },
        };

        // Initialize on load
        updateRadioSelectionDesign();
        toggleFormSections(getSelectedStatus());

        radioStatus.forEach(radio => {
            radio.addEventListener('change', function() {
                updateRadioSelectionDesign();
                toggleFormSections(this.value);
            });
        });

        function getSelectedStatus() {
            for (const r of radioStatus) {
                if (r.checked) return r.value;
            }
            return 'hadir';
        }

        function updateRadioSelectionDesign() {
            const labels = { hadir: labelHadir, sakit: labelSakit, izin: labelIzin, libur: labelLibur };
            const colors = statusColors;

            // Reset all
            Object.values(labels).forEach(lbl => {
                lbl.classList.remove('border-green-500', 'bg-green-50', 'border-orange-500', 'bg-orange-50', 
                                     'border-blue-500', 'bg-blue-50', 'border-purple-500', 'bg-purple-50');
                lbl.classList.add('border-gray-200');
            });

            const sel = getSelectedStatus();
            if (labels[sel] && colors[sel]) {
                labels[sel].classList.remove('border-gray-200');
                labels[sel].classList.add(colors[sel].border, colors[sel].bg);
            }
        }

        function toggleFormSections(statusValue) {
            // Hide all
            sectionHadir.classList.add('hidden');
            sectionSakitIzin.classList.add('hidden');
            sectionLibur.classList.add('hidden');

            // Reset required and disabled
            if (inputFoto) { inputFoto.required = false; inputFoto.disabled = true; }
            if (inputAlasan) { inputAlasan.required = false; inputAlasan.disabled = true; }
            if (inputBukti) { inputBukti.required = false; inputBukti.disabled = true; }
            if (inputAlasanLibur) { inputAlasanLibur.required = false; inputAlasanLibur.disabled = true; }

            if (statusValue === 'hadir') {
                sectionHadir.classList.remove('hidden');
                if (inputFoto) { inputFoto.required = true; inputFoto.disabled = false; }

            } else if (statusValue === 'sakit') {
                sectionSakitIzin.classList.remove('hidden');
                if (inputAlasan) { inputAlasan.required = true; inputAlasan.disabled = false; inputAlasan.placeholder = 'Tuliskan penyakit yang dialami (misal: Demam tinggi, flu berat, dll)...'; }
                if (labelAlasan) labelAlasan.innerHTML = 'Alasan Sakit <span class="text-red-500">*</span>';
                if (labelBukti)  labelBukti.innerHTML  = 'Surat Keterangan Dokter (Foto/PDF) <span class="text-red-500">*</span>';
                if (inputBukti)  { inputBukti.required = true; inputBukti.disabled = false; }
                if (buktiHint)   buktiHint.textContent = '📄 Upload surat keterangan dokter. Format: JPG, PNG, PDF. Maks. 2MB.';
                if (buktiReqMark) buktiReqMark.classList.remove('hidden');

            } else if (statusValue === 'izin') {
                sectionSakitIzin.classList.remove('hidden');
                if (inputAlasan) { inputAlasan.required = true; inputAlasan.disabled = false; inputAlasan.placeholder = 'Tuliskan alasan izin secara jelas (misal: Urusan keluarga mendesak, dll)...'; }
                if (labelAlasan) labelAlasan.innerHTML = 'Alasan Keperluan Izin <span class="text-red-500">*</span>';
                if (labelBukti)  labelBukti.innerHTML  = 'Surat Izin / Dokumen Pendukung (Opsional)';
                if (inputBukti)  { inputBukti.required = false; inputBukti.disabled = false; }
                if (buktiHint)   buktiHint.textContent = '📄 Upload dokumen pendukung jika ada. Format: JPG, PNG, PDF. Maks. 2MB.';
                if (buktiReqMark) buktiReqMark.classList.add('hidden');

            } else if (statusValue === 'libur') {
                sectionLibur.classList.remove('hidden');
                if (inputAlasanLibur) { inputAlasanLibur.required = true; inputAlasanLibur.disabled = false; }
            }
        }

        // ⏰ Realtime Clock + Deteksi Keterlambatan
        const clockEl   = document.getElementById('realtimeClock');
        const bannerEl  = document.getElementById('lateWarningBanner');
        const bannerTxt = document.getElementById('lateWarningText');

        @if(isset($perusahaan))
        // Data jam masuk dari server (format H:i)
        const jamMasukStr     = '{{ $perusahaan->getJamMasukLabel() }}'; // e.g. "07:30"
        const toleransiMenit  = {{ $perusahaan->toleransi_menit ?? 15 }};

        function getDeadlineToday() {
            const now = new Date();
            const [hh, mm] = jamMasukStr.split(':').map(Number);
            const deadline = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hh, mm + toleransiMenit, 0);
            return deadline;
        }
        @endif

        function tickClock() {
            if (!clockEl) return;
            const now = new Date();
            const hh  = String(now.getHours()).padStart(2, '0');
            const mm  = String(now.getMinutes()).padStart(2, '0');
            const ss  = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hh}:${mm}:${ss}`;

            @if(isset($perusahaan))
            // Cek apakah sudah terlambat
            const deadline = getDeadlineToday();
            if (bannerEl && now > deadline) {
                const diffMs   = now - deadline;
                const diffMenit = Math.floor(diffMs / 60000);
                bannerEl.classList.remove('hidden');
                if (bannerTxt) {
                    bannerTxt.textContent = diffMenit > 0
                        ? `⚠️ Anda sudah terlambat ${diffMenit} menit dari batas toleransi!`
                        : '⚠️ Anda sudah melewati batas toleransi waktu masuk!';
                }
            } else if (bannerEl) {
                bannerEl.classList.add('hidden');
            }
            @endif
        }

        tickClock(); // Langsung tampil tanpa jeda
        setInterval(tickClock, 1000);

        // Loading visual effect on submit
        form.addEventListener('submit', function() {
            setTimeout(function() {
                submitBtn.disabled = true;
            }, 1);
            submitText.innerHTML = 'Mengirim...';
            submitSpinner.classList.remove('hidden');
            submitIcon.classList.add('hidden');
        });
    });
</script>
@endsection
