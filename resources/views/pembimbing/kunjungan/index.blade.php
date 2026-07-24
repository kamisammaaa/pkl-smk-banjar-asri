@extends('layouts.app')
@section('page-title', 'Input Kunjungan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <h2 class="text-2xl font-bold text-gray-800">🏢 Daftar Kunjungan Industri</h2>
        <div class="flex items-center gap-3">
            <!-- Toggles -->
            <div class="bg-gray-200 p-1 rounded-lg flex items-center">
                <button type="button" id="btn-table" class="px-4 py-1.5 text-sm font-medium rounded-md bg-white shadow-sm text-gray-800 transition">Tabel</button>
                <button type="button" id="btn-calendar" class="px-4 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:text-gray-800 transition">Kalender</button>
            </div>
            
            <a href="{{ route('pembimbing.kunjungan.create') }}" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition h-full flex items-center">
                + Input Kunjungan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg">
            <p class="font-semibold">✅ {{ session('success') }}</p>
        </div>
    @endif

    <div id="table-container" class="space-y-6">
        <!-- Filter Card -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanBinaan as $p)
                        <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    <option value="rencana" {{ request('status') == 'rencana' ? 'selected' : '' }}>Rencana</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">🔍 Filter</button>
                <a href="{{ route('pembimbing.kunjungan') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 text-center">↺</a>
            </div>
        </form>
    </div>

    <!-- Table/List Kunjungan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold">Siswa Binaan</th>
                        <th class="px-4 py-3 font-semibold">Catatan / Rencana</th>
                        <th class="px-4 py-3 font-semibold text-center">Foto</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($kunjungans as $k)
                    <tr class="hover:bg-gray-50 {{ $k->status === 'rencana' ? 'bg-blue-50/20' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $k->tanggal->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $k->perusahaan->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $k->perusahaan->alamat ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $siswaBinaanDiPerusahaan = $k->perusahaan 
                                    ? $k->perusahaan->siswaProfiles->where('pembimbing_id', auth()->id()) 
                                    : collect();
                            @endphp
                            @forelse($siswaBinaanDiPerusahaan as $sp)
                                <div class="text-xs font-medium text-gray-800">• {{ $sp->user->name ?? '-' }}</div>
                            @empty
                                <span class="text-gray-400 text-xs">—</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3">
                            @if($k->status === 'rencana')
                                <p class="text-xs text-blue-800 bg-blue-50 p-2 rounded">
                                    <strong>Rencana:</strong> {{ Str::limit($k->catatan_rencana, 100) }}
                                </p>
                            @else
                                <p class="text-xs text-gray-700 bg-gray-50 p-2 rounded">
                                    <strong>Hasil:</strong> {{ Str::limit($k->catatan, 100) }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->foto)
                                <a href="{{ Storage::url($k->foto) }}" target="_blank" class="text-blue-600 hover:underline text-xs">📷 Lihat</a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->status === 'rencana')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Rencana</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Selesai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('pembimbing.kunjungan.edit', $k->id) }}" class="px-3 py-1 bg-yellow-500 text-white text-xs font-medium rounded hover:bg-yellow-600 transition">
                                    Edit / Selesaikan
                                </a>
                                <form action="{{ route('pembimbing.kunjungan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kunjungan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Belum ada data kunjungan</p>
                            <a href="{{ route('pembimbing.kunjungan.create') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                                + Input Kunjungan Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $kunjungans->links() }}
        </div>
    </div>
    </div> <!-- end table-container -->

    <!-- Calendar Container -->
    <div id="calendar-container" class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hidden">
        <div class="flex items-center justify-end gap-4 mb-4">
            <span class="flex items-center text-xs font-medium"><span class="w-3 h-3 rounded-full bg-blue-500 mr-1.5"></span> Rencana</span>
            <span class="flex items-center text-xs font-medium"><span class="w-3 h-3 rounded-full bg-green-500 mr-1.5"></span> Selesai</span>
        </div>
        <div id="calendar" class="w-full min-h-[600px]"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/fullcalendar.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Toggling
    const btnTable = document.getElementById('btn-table');
    const btnCalendar = document.getElementById('btn-calendar');
    const tableContainer = document.getElementById('table-container');
    const calendarContainer = document.getElementById('calendar-container');

    let calendarRendered = false;
    let calendar = null;

    function renderCalendar() {
        if (calendarRendered) return;
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu'
            },
            events: {!! json_encode($calendarEvents) !!},
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            }
        });
        calendar.render();
        calendarRendered = true;
    }

    btnTable.addEventListener('click', () => {
        tableContainer.classList.remove('hidden');
        calendarContainer.classList.add('hidden');
        
        btnTable.classList.add('bg-white', 'shadow-sm', 'text-gray-800');
        btnTable.classList.remove('text-gray-600');
        
        btnCalendar.classList.remove('bg-white', 'shadow-sm', 'text-gray-800');
        btnCalendar.classList.add('text-gray-600');
    });

    btnCalendar.addEventListener('click', () => {
        tableContainer.classList.add('hidden');
        calendarContainer.classList.remove('hidden');
        
        btnCalendar.classList.add('bg-white', 'shadow-sm', 'text-gray-800');
        btnCalendar.classList.remove('text-gray-600');
        
        btnTable.classList.remove('bg-white', 'shadow-sm', 'text-gray-800');
        btnTable.classList.add('text-gray-600');

        renderCalendar();
    });
});
</script>
@endpush