@extends('layouts.app')
@section('page-title', 'Data Perusahaan PKL')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-black text-white">🏢 Data Perusahaan PKL</h1>
                <p class="text-sm text-gray-400 mt-1">Kelola informasi mitra perusahaan dan cek status verifikasi data Anda.</p>
            </div>
            @if(isset($data))
                <span class="text-xs font-bold uppercase tracking-wide text-gray-300 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                    Status: {{ $data->is_verified === true ? 'Disetujui' : ($data->is_verified === false ? 'Perlu Perbaikan' : 'Menunggu Verifikasi') }}
                </span>
            @endif
        </div>

    @if(session('success'))
        <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-xl shadow-[0_0_15px_rgba(14,203,129,0.2)] flex items-start gap-3">
            <span class="text-xl">✅</span>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- LEFT SIDE: FORM INPUT (col-span-7) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="glass-panel p-6 rounded-2xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
                <h3 class="font-bold text-white text-sm mb-4 pb-2 border-b border-white/10">📝 Form Detail Perusahaan</h3>
                <form action="{{ route('siswa.perusahaan.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1.5 uppercase tracking-wider">Nama Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $data->nama_perusahaan ?? '') }}" required 
                               class="w-full bg-crypto-dark border border-white/20 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                               placeholder="Contoh: PT Teknologi Indonesia">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1.5 uppercase tracking-wider">Alamat Pembimbing <span class="text-red-500">*</span></label>
                        <textarea name="alamat_pembimbing" rows="3" required
                                  class="w-full bg-crypto-dark border border-white/20 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors resize-none"
                                  placeholder="Alamat kantor tempat pembimbing bertugas...">{{ old('alamat_pembimbing', $data->alamat_pembimbing ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1.5 uppercase tracking-wider">Nama Pembimbing Industri <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pembimbing" value="{{ old('nama_pembimbing', $data->nama_pembimbing ?? '') }}" required 
                                   class="w-full bg-crypto-dark border border-white/20 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                                   placeholder="Nama pembimbing lapangan Anda">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1.5 uppercase tracking-wider">Tempat Tanggal Lahir Pembimbing <span class="text-red-500">*</span></label>
                            <input type="text" name="ttl_pembimbing" value="{{ old('ttl_pembimbing', $data->ttl_pembimbing ?? '') }}" required 
                                   placeholder="Contoh: Bandung, 15 Juli 1985"
                                   class="w-full bg-crypto-dark border border-white/20 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1.5 uppercase tracking-wider">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="no_telp" value="{{ old('no_telp', $data->no_telp ?? '') }}" required 
                               placeholder="Contoh: 081234567890"
                               class="w-full bg-crypto-dark border border-white/20 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    </div>

                    <div class="flex justify-end pt-3 border-t border-white/10">
                        <button type="submit" class="px-5 py-2.5 bg-crypto-accent hover:bg-crypto-accentHover text-white font-bold rounded-xl transition-colors shadow-[0_0_15px_rgba(112,0,255,0.3)] active:scale-95 text-sm flex items-center gap-1.5">
                            <span class="drop-shadow-md">💾</span> Simpan Data Mitra
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT SIDE: STATUS & INSTRUCTIONS (col-span-5) -->
        <div class="lg:col-span-5 space-y-6">
            {{-- Status Card --}}
            @if($data)
            <div class="glass-panel p-6 rounded-2xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
                <h3 class="font-bold text-white text-sm mb-3">Status Verifikasi</h3>
                @if($data->is_verified === true)
                    <div class="bg-crypto-success/10 border border-crypto-success/30 rounded-xl p-4 flex items-start gap-3 shadow-inner">
                        <span class="text-xl drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">✅</span>
                        <div>
                            <p class="font-bold text-crypto-success text-sm">Data Disetujui</p>
                            <p class="text-xs text-green-100 mt-1 leading-relaxed">Data perusahaan valid dan disetujui oleh admin.</p>
                        </div>
                    </div>
                @elseif($data->is_verified === false)
                    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 flex items-start gap-3 shadow-inner">
                        <span class="text-xl drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">❌</span>
                        <div>
                            <p class="font-bold text-red-400 text-sm">Data Perlu Perbaikan</p>
                            <p class="text-xs text-red-200 mt-1 leading-relaxed">Data tidak disetujui oleh admin. Silakan periksa kembali detail perusahaan Anda.</p>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4 flex items-start gap-3 shadow-inner">
                        <span class="text-xl drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]">⏳</span>
                        <div>
                            <p class="font-bold text-yellow-400 text-sm">Menunggu Verifikasi</p>
                            <p class="text-xs text-yellow-200 mt-1 leading-relaxed">Data sedang dalam antrean verifikasi oleh pihak sekolah.</p>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- Guidance Card --}}
            <div class="glass-panel p-6 rounded-2xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
                <h3 class="font-bold text-white text-sm mb-3">💡 Petunjuk Pengisian</h3>
                <ul class="space-y-3 text-xs text-gray-300 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="text-crypto-accent font-bold drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">•</span>
                        <span>Isi nama perusahaan secara lengkap sesuai nama resmi instansi/perusahaan tempat Anda PKL.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-crypto-accent font-bold drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">•</span>
                        <span>Alamat pembimbing diisi dengan alamat lengkap kantor/tempat kerja pembimbing lapangan Anda.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-crypto-accent font-bold drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">•</span>
                        <span>Nama pembimbing lapangan adalah nama staf/karyawan perusahaan yang membimbing Anda secara langsung.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-crypto-accent font-bold drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">•</span>
                        <span>Nomor telepon pembimbing yang dapat dihubungi untuk konfirmasi kunjungan monitoring.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
