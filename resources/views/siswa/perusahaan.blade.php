@extends('layouts.app')
@section('page-title', 'Data Perusahaan PKL')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4">
    <div class="flex items-center gap-3">
        <h2 class="text-xl font-black text-gray-800">🏢 Data Perusahaan PKL</h2>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl shadow-sm flex items-start gap-3">
            <span class="text-xl">✅</span>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- LEFT SIDE: FORM INPUT (col-span-7) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-slate-800 text-sm mb-4 pb-2 border-b">📝 Form Detail Perusahaan</h3>
                <form action="{{ route('siswa.perusahaan.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Nama Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $data->nama_perusahaan ?? '') }}" required 
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition"
                               placeholder="Contoh: PT Teknologi Indonesia">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Alamat Pembimbing <span class="text-red-500">*</span></label>
                        <textarea name="alamat_pembimbing" rows="3" required
                                  class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition resize-none"
                                  placeholder="Alamat kantor tempat pembimbing bertugas...">{{ old('alamat_pembimbing', $data->alamat_pembimbing ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Nama Pembimbing Industri <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pembimbing" value="{{ old('nama_pembimbing', $data->nama_pembimbing ?? '') }}" required 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition"
                                   placeholder="Nama pembimbing lapangan Anda">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Tempat Tanggal Lahir Pembimbing <span class="text-red-500">*</span></label>
                            <input type="text" name="ttl_pembimbing" value="{{ old('ttl_pembimbing', $data->ttl_pembimbing ?? '') }}" required 
                                   placeholder="Contoh: Bandung, 15 Juli 1985"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="no_telp" value="{{ old('no_telp', $data->no_telp ?? '') }}" required 
                               placeholder="Contoh: 081234567890"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition">
                    </div>

                    <div class="flex justify-end pt-3 border-t border-slate-50">
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl transition shadow-sm active:scale-95 text-sm flex items-center gap-1.5">
                            💾 Simpan Data Mitra
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT SIDE: STATUS & INSTRUCTIONS (col-span-5) -->
        <div class="lg:col-span-5 space-y-6">
            {{-- Status Card --}}
            @if($data)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-slate-800 text-sm mb-3">Status Verifikasi</h3>
                @if($data->is_verified === true)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                        <span class="text-xl">✅</span>
                        <div>
                            <p class="font-bold text-green-800 text-sm">Data Disetujui</p>
                            <p class="text-xs text-green-600 mt-1 leading-relaxed">Data perusahaan valid dan disetujui oleh admin.</p>
                        </div>
                    </div>
                @elseif($data->is_verified === false)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                        <span class="text-xl">❌</span>
                        <div>
                            <p class="font-bold text-red-800 text-sm">Data Perlu Perbaikan</p>
                            <p class="text-xs text-red-600 mt-1 leading-relaxed">Data tidak disetujui oleh admin. Silakan periksa kembali detail perusahaan Anda.</p>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                        <span class="text-xl">⏳</span>
                        <div>
                            <p class="font-bold text-yellow-800 text-sm">Menunggu Verifikasi</p>
                            <p class="text-xs text-yellow-600 mt-1 leading-relaxed">Data sedang dalam antrean verifikasi oleh pihak sekolah.</p>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- Guidance Card --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-slate-800 text-sm mb-3">💡 Petunjuk Pengisian</h3>
                <ul class="space-y-3 text-xs text-slate-600 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">•</span>
                        <span>Isi nama perusahaan secara lengkap sesuai nama resmi instansi/perusahaan tempat Anda PKL.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">•</span>
                        <span>Alamat pembimbing diisi dengan alamat lengkap kantor/tempat kerja pembimbing lapangan Anda.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">•</span>
                        <span>Nama pembimbing lapangan adalah nama staf/karyawan perusahaan yang membimbing Anda secara langsung.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">•</span>
                        <span>Nomor telepon pembimbing yang dapat dihubungi untuk konfirmasi kunjungan monitoring.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
