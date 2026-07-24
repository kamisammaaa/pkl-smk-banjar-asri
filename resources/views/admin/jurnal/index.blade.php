@extends('layouts.app')
@section('page-title', 'Kelola Jurnal Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">📖 Kelola & Hapus Jurnal Siswa</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau jurnal kegiatan harian siswa PKL dan bersihkan data jika diperlukan.</p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            {!! session('success') !!}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
            {!! session('error') !!}
        </div>
    @endif

    <!-- 🔍 Search & Filter Bar -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.jurnal.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cari Siswa</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Nama atau NIS..." 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>
                
                <!-- Filter: Jurusan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jurusan</label>
                    <select name="jurusan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
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
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status Jurnal</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
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
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                    <input 
                        type="date" 
                        name="start_date" 
                        value="{{ request('start_date') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>

                <!-- Filter: Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                    <input 
                        type="date" 
                        name="end_date" 
                        value="{{ request('end_date') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    >
                </div>
            </div>
            
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 font-semibold active:scale-95 text-sm">
                    🔍 <span class="hidden sm:inline">Filter</span>
                </button>
                <a href="{{ route('admin.jurnal.index') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 font-semibold active:scale-95 text-sm">
                    🔄 <span class="hidden sm:inline">Reset</span>
                </a>
                
                @php
                    $exportUrl = route('admin.jurnal.export', request()->all());
                @endphp
                <a href="{{ $exportUrl }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 font-semibold active:scale-95 text-sm whitespace-nowrap">
                    📥 <span class="hidden sm:inline">Export CSV</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Action & Table Form -->
    <form id="bulk-delete-form" action="{{ route('admin.jurnal.bulk-destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua jurnal terpilih?')">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Action Header -->
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="select-all-checkbox" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                    <label for="select-all-checkbox" class="text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none">Pilih Semua</label>
                </div>
                <button type="submit" id="bulk-delete-btn" disabled class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-2 rounded shadow transition flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    🗑️ Hapus Terpilih
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[1000px]">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600 border-b">
                        <tr>
                            <th class="w-12 px-4 py-3 text-center">Pilih</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Tanggal & Hari</th>
                            <th class="px-4 py-3">Kegiatan Harian</th>
                            <th class="px-4 py-3">Kendala / Masalah</th>
                            <th class="px-4 py-3 text-center">Foto Jurnal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Nilai</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jurnals as $j)
                        <tr class="hover:bg-gray-50/70 transition">
                            <!-- Checkbox -->
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $j->id }}" class="row-checkbox rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                            </td>

                            <!-- Siswa -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $j->siswa->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">NIS: {{ $j->siswa->siswaProfile->nis ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Jurusan: {{ $j->siswa->siswaProfile->jurusan->nama ?? '-' }}</div>
                            </td>

                            <!-- Tanggal -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-gray-700">{{ $j->tanggal->format('d M Y') }}</span>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $j->tanggal->translatedFormat('l') }}</div>
                            </td>

                            <!-- Kegiatan -->
                            <td class="px-4 py-3">
                                <p class="text-gray-700 leading-relaxed font-normal whitespace-pre-line text-xs max-w-[300px] break-words">{{ $j->kegiatan }}</p>
                            </td>

                            <!-- Kendala -->
                            <td class="px-4 py-3">
                                @if($j->kendala)
                                    <p class="text-orange-700 leading-relaxed font-normal whitespace-pre-line text-xs max-w-[200px] break-words">⚠️ {{ $j->kendala }}</p>
                                @else
                                    <span class="text-xs text-gray-400 font-normal">Tidak ada kendala</span>
                                @endif
                            </td>

                            <!-- Foto -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($j->foto)
                                    <a href="{{ asset('storage/' . $j->foto) }}" target="_blank" class="inline-block relative" title="Lihat Foto Dokumentasi Jurnal">
                                        <img src="{{ asset('storage/' . $j->foto) }}" class="w-10 h-10 object-cover rounded-lg border border-gray-300 hover:scale-110 transition shadow-sm">
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($j->status === 'disetujui')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border bg-green-100 text-green-800 border-green-200">
                                        ✅ Disetujui
                                    </span>
                                @elseif($j->status === 'revisi')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border bg-red-100 text-red-800 border-red-200" title="{{ $j->catatan_revisi }}">
                                        🔄 Revisi
                                    </span>
                                    @if($j->catatan_revisi)
                                        <div class="text-[9px] text-gray-400 mt-1 max-w-[120px] truncate" title="{{ $j->catatan_revisi }}">Catatan: {{ $j->catatan_revisi }}</div>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border bg-orange-100 text-orange-800 border-orange-200">
                                        ⏳ Menunggu
                                    </span>
                                @endif
                            </td>

                            <!-- Nilai -->
                            <td class="px-4 py-3 text-center whitespace-nowrap font-bold text-gray-700">
                                {{ $j->nilai !== null ? $j->nilai : '-' }}
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button type="button" onclick="confirmDelete('{{ route('admin.jurnal.destroy', $j) }}', '{{ $j->siswa->name ?? 'Siswa' }}', '{{ $j->tanggal->format('d-m-Y') }}')" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 text-xs font-semibold px-2.5 py-1.5 rounded hover:bg-red-50 border border-transparent hover:border-red-100 transition">
                                    🗑️ Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                <div class="text-gray-400 text-4xl mb-3">🔍</div>
                                <div class="text-base font-semibold text-gray-700">Tidak Ada Data Jurnal</div>
                                <div class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter tanggal.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jurnals->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $jurnals->links() }}
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
        if (confirm(`Apakah Anda yakin ingin menghapus jurnal harian siswa "${name}" pada tanggal ${date}?\nTindakan ini permanen dan akan menghapus berkas foto dokumentasi.`)) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection
