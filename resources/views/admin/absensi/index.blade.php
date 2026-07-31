@extends('layouts.app')
@section('page-title', 'Kelola Absensi Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white drop-shadow-md">📅 Kelola & Hapus Absensi Siswa</h2>
            <p class="text-sm text-gray-400 mt-1">Lihat detail absensi harian siswa dan hapus jika terdapat kesalahan data.</p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="glass-panel border-l-4 border-green-500 p-4 rounded-xl shadow-[0_0_15px_rgba(34,197,94,0.1)] font-bold text-green-400">
            {!! session('success') !!}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
            {!! session('error') !!}
        </div>
    @endif

    <!-- 🔍 Search & Filter Bar -->
    <div class="glass-panel p-5 rounded-xl shadow-sm border border-white/5">
        <form action="{{ route('admin.absensi.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Cari Siswa</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Nama atau NIS..." 
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>
                
                <!-- Filter: Jurusan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Jurusan</label>
                    <select name="jurusan_id" class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filter: Status -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Status Kehadiran</label>
                    <select name="status" class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter: Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Dari Tanggal</label>
                    <input 
                        type="date" 
                        name="start_date" 
                        value="{{ request('start_date') }}"
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>

                <!-- Filter: Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Sampai Tanggal</label>
                    <input 
                        type="date" 
                        name="end_date" 
                        value="{{ request('end_date') }}"
                        class="w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>
            </div>
            
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.absensi.index') }}" class="bg-gray-150 hover:bg-gray-200 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition active:scale-95">
                    🔄 Reset Filter
                </a>
                <button type="submit" class="bg-crypto-accent hover:bg-purple-600 text-white shadow-[0_0_15px_rgba(112,0,255,0.3)] px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm active:scale-95">
                    🔍 Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Action & Table Form -->
    <form id="bulk-delete-form" action="{{ route('admin.absensi.bulk-destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data absensi terpilih?')">
        @csrf
        <div class="glass-panel rounded-xl shadow-sm border border-white/5 overflow-hidden">
            <!-- Table Action Header -->
            <div class="px-4 py-3 glass-panel/5 border-b border-gray-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="select-all-checkbox" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                    <label for="select-all-checkbox" class="text-xs font-semibold text-gray-400 uppercase cursor-pointer select-none">Pilih Semua</label>
                </div>
                <button type="submit" id="bulk-delete-btn" disabled class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-2 rounded shadow transition flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    🗑️ Hapus Terpilih
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[1000px]">
                    <thead class="glass-panel/5 text-xs font-semibold text-gray-400 border-b border-white/10">
                        <tr>
                            <th class="w-12 px-4 py-3 text-center">Pilih</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Jam Masuk/Pulang</th>
                            <th class="px-4 py-3">Lokasi / IP</th>
                            <th class="px-4 py-3 text-center">Foto / Bukti</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Verifikator</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($absensi as $a)
                        <tr class="hover:glass-panel/5/70 transition">
                            <!-- Checkbox -->
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $a->id }}" class="row-checkbox rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                            </td>

                            <!-- Siswa -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-white drop-shadow-md">{{ $a->siswa->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">NIS: {{ $a->siswa->siswaProfile->nis ?? '-' }}</div>
                                <div class="text-xs text-gray-400">Jurusan: {{ $a->siswa->siswaProfile->jurusan->nama ?? '-' }}</div>
                            </td>

                            <!-- Tanggal -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-gray-300">{{ $a->tanggal->format('d M Y') }}</span>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $a->tanggal->translatedFormat('l') }}</div>
                            </td>

                            <!-- Jam -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-gray-300">
                                    <span class="font-semibold text-green-700">Masuk:</span> 
                                    {{ $a->check_in ? $a->check_in->format('H:i:s') : '-' }}
                                </div>
                                <div class="text-xs text-gray-300 mt-1">
                                    <span class="font-semibold text-red-700">Pulang:</span> 
                                    {{ $a->check_out ? $a->check_out->format('H:i:s') : '-' }}
                                </div>
                            </td>

                            <!-- Lokasi & IP -->
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-300 font-medium truncate max-w-[200px]" title="{{ $a->lokasi_nama ?? '-' }}">
                                    📍 {{ $a->lokasi_nama ?: 'Tidak ada GPS' }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1 font-mono">
                                    🖥️ IP: {{ $a->ip_address ?: '-' }}
                                </div>
                            </td>

                            <!-- Foto / Bukti -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($a->status === 'hadir' && $a->foto)
                                    <a href="{{ asset('storage/' . $a->foto) }}" target="_blank" class="inline-block relative group" title="Lihat Foto Selfie">
                                        <img src="{{ asset('storage/' . $a->foto) }}" class="w-10 h-10 object-cover rounded-lg border border-white/20 hover:scale-110 transition shadow-sm">
                                        <span class="absolute -bottom-1 -right-1 bg-green-500 text-white rounded-full p-0.5 text-[8px]">📸</span>
                                    </a>
                                @elseif(in_array($a->status, ['sakit', 'izin']) && $a->bukti_file)
                                    <a href="{{ asset('storage/' . $a->bukti_file) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-400 hover:text-white hover:underline bg-blue-50 px-2.5 py-1.5 rounded-lg border border-blue-100 font-medium transition" title="Lihat Berkas Bukti">
                                        📄 Bukti File
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full border {{ $a->getStatusBadgeClass() }}">
                                    {{ $a->getStatusLabel() }}
                                </span>
                                @if($a->status === 'libur')
                                    <div class="text-[10px] text-purple-500 mt-0.5">Tidak dihitung % hadir</div>
                                @elseif($a->status === 'alpha' && str_contains($a->lokasi_nama ?? '', 'Otomatis'))
                                    <div class="text-[10px] text-red-400 mt-0.5">Otomatis sistem</div>
                                @endif
                            </td>

                            <!-- Verifikator -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($a->is_verified)
                                    <span class="text-xs font-semibold text-green-700 flex items-center gap-1">
                                        ✔ Disetujui
                                    </span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">Oleh: {{ $a->verifier->name ?? 'Sistem (Auto)' }}</div>
                                    @if($a->verified_at)
                                        <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($a->verified_at)->format('d/m H:i') }}</div>
                                    @endif
                                @else
                                    <span class="text-xs font-semibold text-orange-600 flex items-center gap-1">
                                        ⏳ Pending Pembimbing
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button type="button" onclick="confirmDelete('{{ route('admin.absensi.destroy', $a) }}', '{{ $a->siswa->name ?? 'Siswa' }}', '{{ $a->tanggal->format('d-m-Y') }}')" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 text-xs font-semibold px-2.5 py-1.5 rounded hover:bg-red-50 border border-transparent hover:border-red-100 transition">
                                    🗑️ Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <div class="text-gray-400 text-4xl mb-3">🔍</div>
                                <div class="text-base font-semibold text-gray-300">Tidak Ada Data Absensi</div>
                                <div class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter tanggal.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($absensi->hasPages())
                <div class="px-4 py-3 border-t glass-panel/5">
                    {{ $absensi->links() }}
                </div>
            @endif
        </div>
    </form>

    <!-- Hidden Individual Delete Form -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<!-- Scripts for Bulk Select and Alert Confirm -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

        // Function to update "Bulk Delete" button status
        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.innerHTML = `🗑️ Hapus Terpilih (${checkedCount})`;
            } else {
                bulkDeleteBtn.disabled = true;
                bulkDeleteBtn.innerHTML = `🗑️ Hapus Terpilih`;
            }
        }

        // Event listener for Select All
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                const isChecked = this.checked;
                rowCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateBulkDeleteButton();
            });
        }

        // Event listener for individual checkboxes
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                // If one checkbox is unchecked, uncheck the select all checkbox
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    // Check if all other checkboxes are checked
                    const allChecked = Array.from(rowCheckboxes).every(el => el.checked);
                    selectAllCheckbox.checked = allChecked;
                }
                updateBulkDeleteButton();
            });
        });
    });

    // Confirm individual delete
    function confirmDelete(url, name, date) {
        if (confirm(`Apakah Anda yakin ingin menghapus data absensi siswa "${name}" pada tanggal ${date}?\nTindakan ini permanen dan akan menghapus berkas foto/bukti.`)) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection
