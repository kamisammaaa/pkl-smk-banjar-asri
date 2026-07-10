@extends('layouts.app')
@section('page-title', 'Kelola Industri Mitra')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">🏢 Kelola Industri Mitra (Perusahaan)</h2>
            <p class="text-sm text-gray-500 mt-1">Daftarkan industri mitra PKL dan tentukan pembimbing binaan masing-masing industri.</p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            {!! session('success') !!}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Tambah Perusahaan -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">➕ Tambah Industri Mitra Baru</h3>
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
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
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
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>

                <!-- Kontak PIC -->
                <div>
                    <input 
                        type="text" 
                        name="kontak" 
                        value="{{ old('kontak') }}"
                        placeholder="Kontak PIC (cth: WA/Email)" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>

                <!-- Pembimbing Binaan -->
                <div>
                    <select name="pembimbing_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
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
                    <select name="periode_pkl_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="col-span-4">
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">⏰ Pengaturan Jam Masuk</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jam Masuk <span class="text-red-500">*</span></label>
                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk', '07:30') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Toleransi (menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="toleransi_menit" value="{{ old('toleransi_menit', 15) }}" min="0" max="120" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-500">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm active:scale-95 text-center">
                        Tambah Mitra
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Perusahaan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[700px]">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-600 border-b">
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
                <tbody class="divide-y divide-gray-100">
                    @forelse($perusahaan as $p)
                    <tr class="hover:bg-gray-50/70 transition">
                        <!-- Nama Perusahaan -->
                        <td class="px-4 py-3 font-bold text-gray-800">
                            🏢 {{ $p->nama }}
                        </td>

                        <!-- Alamat -->
                        <td class="px-4 py-3 text-gray-600 text-xs max-w-[250px] truncate" title="{{ $p->alamat }}">
                            {{ $p->alamat }}
                        </td>

                        <!-- Kontak PIC -->
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                            {{ $p->kontak ?: '-' }}
                        </td>

                        <!-- Pembimbing Binaan -->
                        <td class="px-4 py-3">
                            @if($p->pembimbing)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                                    👤 {{ $p->pembimbing->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 font-medium">Belum ditentukan</span>
                            @endif
                        </td>

                        <!-- Periode PKL -->
                        <td class="px-4 py-3">
                            @if($p->periodePKL)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $p->periodePKL->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    📅 {{ $p->periodePKL->nama }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 font-medium">Belum ditentukan</span>
                            @endif
                        </td>

                        <!-- Jam Masuk -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex flex-col items-center">
                                <span class="text-sm font-bold text-gray-800">
                                    {{ \Carbon\Carbon::today()->setTimeFromTimeString($p->jam_masuk ?? '07:30:00')->format('H:i') }}
                                </span>
                                <span class="text-[10px] text-gray-400">tol. {{ $p->toleransi_menit ?? 15 }} mnt</span>
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <!-- Edit -->
                                <a href="{{ route('admin.perusahaan.edit', $p) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900 text-xs font-bold px-2 py-1.5 rounded hover:bg-blue-50 border border-transparent hover:border-blue-100 transition">
                                    ✏️ Edit
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('admin.perusahaan.destroy', $p) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data industri & mitra {{ $p->nama }} ini? Data penempatan siswa mungkin akan terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 text-xs font-bold px-2 py-1.5 rounded hover:bg-red-50 border border-transparent hover:border-red-100 transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                            <div class="text-gray-400 text-4xl mb-3">🔍</div>
                            <div class="text-base font-semibold text-gray-700">Belum Ada Industri Mitra</div>
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