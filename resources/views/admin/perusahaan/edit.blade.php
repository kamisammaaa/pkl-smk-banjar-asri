@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white drop-shadow-md">🏢 Edit Perusahaan/Industri</h2>
            <p class="text-sm text-gray-400 mt-1">Ubah informasi profil mitra industri dan pembimbing terkait.</p>
        </div>
        <a href="{{ route('admin.perusahaan.index') }}" class="glass-panel/10 hover:bg-gray-200 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-panel p-6 rounded-xl shadow-sm border border-white/5">
        <form action="{{ route('admin.perusahaan.update', $perusahaan) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-1">Nama Perusahaan</label>
                <input 
                    type="text" 
                    name="nama" 
                    value="{{ old('nama', $perusahaan->nama) }}" 
                    required 
                    class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-1">Alamat</label>
                <textarea 
                    name="alamat" 
                    required 
                    rows="3"
                    class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >{{ old('alamat', $perusahaan->alamat) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-1">Kontak PIC</label>
                <input 
                    type="text" 
                    name="kontak" 
                    value="{{ old('kontak', $perusahaan->kontak) }}" 
                    class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-1">Pembimbing Binaan</label>
                <select name="pembimbing_id" required class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Pilih Pembimbing</option>
                    @foreach($pembimbing as $pb) 
                        <option value="{{ $pb->id }}" {{ old('pembimbing_id', $perusahaan->pembimbing_id) == $pb->id ? 'selected' : '' }}>
                            {{ $pb->name }}
                        </option> 
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-1">Periode PKL</label>
                <select name="periode_pkl_id" class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Pilih Periode PKL (Opsional)</option>
                    @foreach($periode as $per) 
                        <option value="{{ $per->id }}" {{ old('periode_pkl_id', $perusahaan->periode_pkl_id) == $per->id ? 'selected' : '' }}>
                            {{ $per->nama }} ({{ $per->is_active ? 'Aktif' : 'Nonaktif' }})
                        </option> 
                    @endforeach
                </select>
            </div>

            {{-- Jam Masuk & Toleransi --}}
            <div class="grid grid-cols-2 gap-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="col-span-2">
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-3">⏰ Pengaturan Jam Masuk</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">
                        Jam Masuk <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="time" 
                        name="jam_masuk" 
                        value="{{ old('jam_masuk', \Carbon\Carbon::today()->setTimeFromTimeString($perusahaan->jam_masuk ?? '07:30:00')->format('H:i')) }}" 
                        required 
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500"
                    >
                    <p class="text-xs text-gray-400 mt-1">Jam wajib masuk siswa PKL</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-1">
                        Toleransi Keterlambatan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="toleransi_menit" 
                            value="{{ old('toleransi_menit', $perusahaan->toleransi_menit ?? 15) }}" 
                            min="0" max="120" required
                            class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-4 py-2.5 pr-16 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500"
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">menit</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">0 = tidak ada toleransi</p>
                </div>
            </div>


            <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                <a href="{{ route('admin.perusahaan.index') }}" class="glass-panel/10 hover:bg-gray-200 text-gray-300 px-5 py-2.5 rounded-lg text-sm font-medium transition active:scale-95">
                    Batal
                </a>
                <button type="submit" class="bg-crypto-accent hover:bg-purple-600 text-white shadow-[0_0_15px_rgba(112,0,255,0.3)] px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
