@extends('layouts.app')
@section('page-title', 'Kelola Industri Mitra')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white drop-shadow-md">🏢 Kelola Industri Mitra (Perusahaan)</h2>
            <p class="text-sm text-gray-400 mt-1">Daftarkan industri mitra PKL dan tentukan pembimbing binaan masing-masing industri.</p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="glass-panel border-l-4 border-green-500 p-4 rounded-xl shadow-[0_0_15px_rgba(34,197,94,0.1)]">
            <div class="flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <p class="font-bold text-green-400">{!! session('success') !!}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="glass-panel border-l-4 border-red-500 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.1)]">
            <p class="font-bold text-red-400 mb-2">⚠️ Terdapat Kesalahan:</p>
            <ul class="list-disc pl-5 text-sm text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Tambah Perusahaan -->
    <div class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <h3 class="text-sm font-bold text-gray-300 uppercase mb-4 drop-shadow-md">➕ Tambah Industri Mitra Baru</h3>
        <form action="{{ route('admin.perusahaan.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Nama -->
                <div>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama') }}"
                        placeholder="Nama Perusahaan" 
                        required 
                        class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                    >
                </div>

                <!-- Alamat -->
                <div>
                    <input 
                        type="text" 
                        name="alamat" 
                        value="{{ old('alamat') }}"
                        placeholder="Alamat Lengkap" 
                        required 
                        class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                    >
                </div>

                <!-- Kontak PIC -->
                <div>
                    <input 
                        type="text" 
                        name="kontak" 
                        value="{{ old('kontak') }}"
                        placeholder="Kontak PIC (cth: WA/Email)" 
                        class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"
                    >
                </div>

                <!-- Pembimbing Binaan -->
                <div>
                    <select name="pembimbing_id" required class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                        <option value="">Pilih Pembimbing</option>
                        @foreach(\App\Models\User::where('role','pembimbing')->where('is_active', 1)->orderBy('name')->get() as $pb) 
                            <option value="{{ $pb->id }}" {{ old('pembimbing_id') == $pb->id ? 'selected' : '' }}>
                                {{ $pb->name }}
                            </option> 
                        @endforeach
                    </select>
                </div>

                <!-- Periode PKL -->
                <div>
                    <select name="periode_pkl_id" class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                        <option value="">Pilih Periode PKL</option>
                        @foreach($periode as $per) 
                            <option value="{{ $per->id }}" {{ old('periode_pkl_id') == $per->id ? 'selected' : '' }}>
                                {{ $per->nama }} ({{ $per->is_active ? 'Aktif' : 'Nonaktif' }})
                            </option> 
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Jam Masuk & Toleransi -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-3 bg-white/5 border border-white/10 rounded-lg">
                <div class="col-span-4">
                    <p class="text-xs font-bold text-yellow-400 drop-shadow-md uppercase tracking-wide">⏰ Pengaturan Jam Masuk</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Jam Masuk <span class="text-red-400">*</span></label>
                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk', '07:30') }}" required
                           class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Toleransi (menit) <span class="text-red-400">*</span></label>
                    <input type="number" name="toleransi_menit" value="{{ old('toleransi_menit', 15) }}" min="0" max="120" required
                           class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="w-full bg-crypto-accent hover:bg-purple-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-[0_0_15px_rgba(112,0,255,0.3)] active:scale-95 text-center">
                        Tambah Mitra
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Perusahaan -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[700px]">
                <thead class="bg-white/5 text-xs font-semibold text-gray-300 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3">Nama Industri</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3">Kontak PIC</th>
                        <th class="px-4 py-3">Pembimbing Binaan</th>
                        <th class="px-4 py-3">Periode PKL</th>
                        <th class="px-4 py-3 text-center">⏰ Jam Masuk</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($perusahaan as $p)
                    <tr class="hover:bg-white/5 transition-colors">
                        <!-- Nama Perusahaan -->
                        <td class="px-4 py-3 font-bold text-white">
                            🏢 {{ $p->nama }}
                        </td>

                        <!-- Alamat -->
                        <td class="px-4 py-3 text-gray-400 text-xs max-w-[250px] truncate" title="{{ $p->alamat }}">
                            {{ $p->alamat }}
                        </td>

                        <!-- Kontak PIC -->
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                            {{ $p->kontak ?: '-' }}
                        </td>

                        <!-- Pembimbing Binaan -->
                        <td class="px-4 py-3">
                            @if($p->pembimbing)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-crypto-accent/20 text-crypto-accent border border-crypto-accent/30 shadow-inner">
                                    👤 {{ $p->pembimbing->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500 font-bold italic">Belum ditentukan</span>
                            @endif
                        </td>

                        <!-- Periode PKL -->
                        <td class="px-4 py-3">
                            @if($p->periodePKL)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold shadow-inner border {{ $p->periodePKL->is_active ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-gray-500/20 text-gray-400 border-gray-500/30' }}">
                                    📅 {{ $p->periodePKL->nama }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500 font-bold italic">Belum ditentukan</span>
                            @endif
                        </td>

                        <!-- Jam Masuk -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex flex-col items-center">
                                <span class="text-sm font-bold text-white">
                                    {{ \Carbon\Carbon::today()->setTimeFromTimeString($p->jam_masuk ?? '07:30:00')->format('H:i') }}
                                </span>
                                <span class="text-[10px] text-gray-400">tol. {{ $p->toleransi_menit ?? 15 }} mnt</span>
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <!-- Edit -->
                                <a href="{{ route('admin.perusahaan.edit', $p) }}" class="inline-flex items-center gap-1 text-blue-400 hover:text-white text-xs font-bold px-2 py-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/30 border border-blue-500/20 shadow-inner transition-colors">
                                    ✏️ Edit
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('admin.perusahaan.destroy', $p) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data industri & mitra {{ $p->nama }} ini? Data penempatan siswa mungkin akan terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-400 hover:text-white text-xs font-bold px-2 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/30 border border-red-500/20 shadow-inner transition-colors">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <div class="text-4xl mb-3 opacity-50 drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">🔍</div>
                            <div class="text-base font-bold text-white drop-shadow-md">Belum Ada Industri Mitra</div>
                            <div class="text-xs text-gray-400 mt-1">Gunakan form di atas untuk menambahkan industri mitra baru.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection