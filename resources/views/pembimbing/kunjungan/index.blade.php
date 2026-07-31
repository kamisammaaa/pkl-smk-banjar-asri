@extends('layouts.app')
@section('page-title', 'Input Kunjungan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <h2 class="text-2xl font-bold text-white drop-shadow-md">🏢 Daftar Kunjungan Industri</h2>
        <div class="flex items-center gap-3">
            <!-- Toggles -->
            <div class="bg-black/20 p-1 rounded-lg flex items-center border border-white/10 shadow-inner">
                <button type="button" id="btn-table" class="px-4 py-1.5 text-sm font-bold rounded-md bg-crypto-dark text-white border border-white/20 shadow-[0_0_15px_rgba(255,255,255,0.1)] transition-colors active:scale-95">Tabel</button>
                <button type="button" id="btn-calendar" class="px-4 py-1.5 text-sm font-bold rounded-md text-gray-400 hover:text-white transition-colors active:scale-95">Kalender</button>
            </div>
            
            <a href="{{ route('pembimbing.kunjungan.create') }}" class="bg-crypto-accent text-white px-4 py-1.5 rounded-lg text-sm hover:bg-crypto-accentHover transition-colors h-full flex items-center shadow-[0_0_15px_rgba(112,0,255,0.3)] font-bold active:scale-95">
                + Input Kunjungan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.2)]">
            <p class="font-semibold">✅ {{ session('success') }}</p>
        </div>
    @endif

    <div id="table-container" class="space-y-6">
        <!-- Filter Card -->
        <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Perusahaan</label>
                <select name="perusahaan_id" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Perusahaan</option>
                    @foreach($perusahaanBinaan as $p)
                        <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Status</option>
                    <option value="rencana" {{ request('status') == 'rencana' ? 'selected' : '' }}>Rencana</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-crypto-accent text-white px-4 py-2 rounded-lg text-sm hover:bg-crypto-accentHover font-bold shadow-[0_0_15px_rgba(112,0,255,0.3)] transition-colors active:scale-95">🔍 Filter</button>
                <a href="{{ route('pembimbing.kunjungan') }}" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-sm hover:bg-white/20 text-white font-bold text-center transition-colors active:scale-95">↺</a>
            </div>
        </form>
    </div>

    <!-- Table/List Kunjungan -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Siswa Binaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Catatan / Rencana</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Foto</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($kunjungans as $k)
                    <tr class="hover:bg-white/5 transition-colors {{ $k->status === 'rencana' ? 'bg-blue-500/10' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-200">{{ $k->tanggal->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-200">{{ $k->perusahaan->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $k->perusahaan->alamat ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $siswaBinaanDiPerusahaan = $k->perusahaan 
                                    ? $k->perusahaan->siswaProfiles->where('pembimbing_id', auth()->id()) 
                                    : collect();
                            @endphp
                            @forelse($siswaBinaanDiPerusahaan as $sp)
                                <div class="text-xs font-semibold text-gray-300 mb-0.5">• {{ $sp->user->name ?? '-' }}</div>
                            @empty
                                <span class="text-gray-500 text-xs">—</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3">
                            @if($k->status === 'rencana')
                                <p class="text-xs text-blue-300 bg-blue-500/20 border border-blue-500/30 shadow-inner p-2 rounded">
                                    <strong class="text-blue-400">Rencana:</strong> {{ Str::limit($k->catatan_rencana, 100) }}
                                </p>
                            @else
                                <p class="text-xs text-gray-300 bg-white/5 border border-white/10 shadow-inner p-2 rounded">
                                    <strong class="text-white">Hasil:</strong> {{ Str::limit($k->catatan, 100) }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->foto)
                                <a href="{{ Storage::url($k->foto) }}" target="_blank" class="text-crypto-accent hover:text-white font-bold drop-shadow-[0_0_5px_rgba(112,0,255,0.5)] transition-colors text-xs">📷 Lihat</a>
                            @else
                                <span class="text-gray-500 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->status === 'rencana')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner">Rencana</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-crypto-success/20 text-crypto-success border border-crypto-success/30 shadow-inner">Selesai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('pembimbing.kunjungan.edit', $k->id) }}" class="px-3 py-1.5 bg-amber-500/20 text-amber-400 hover:bg-amber-500 hover:text-white border border-amber-500/30 shadow-inner text-xs font-bold rounded transition-colors active:scale-95">
                                    Edit / Selesaikan
                                </a>
                                <form action="{{ route('pembimbing.kunjungan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kunjungan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/30 shadow-inner text-xs font-bold rounded transition-colors active:scale-95">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2 opacity-50 drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">📭</div>
                            <p class="font-medium">Belum ada data kunjungan</p>
                            <a href="{{ route('pembimbing.kunjungan.create') }}" class="text-crypto-accent hover:text-white font-bold transition-colors text-sm mt-2 inline-block">
                                + Input Kunjungan Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/10 bg-white/5">
            {{ $kunjungans->links() }}
        </div>
    </div>
    </div> <!-- end table-container -->

    <!-- Calendar Container -->
    <div id="calendar-container" class="glass-panel p-5 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 hidden">
        <div class="flex items-center justify-end gap-4 mb-4">
            <span class="flex items-center text-xs font-bold text-gray-300"><span class="w-3 h-3 rounded-full bg-blue-500 mr-1.5 shadow-[0_0_10px_rgba(59,130,246,0.6)]"></span> Rencana</span>
            <span class="flex items-center text-xs font-bold text-gray-300"><span class="w-3 h-3 rounded-full bg-crypto-success mr-1.5 shadow-[0_0_10px_rgba(14,203,129,0.6)]"></span> Selesai</span>
        </div>
        <div id="calendar" class="w-full min-h-[600px] text-gray-200"></div>
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
        
        btnTable.classList.add('bg-crypto-dark', 'border-white/20', 'text-white', 'shadow-[0_0_15px_rgba(255,255,255,0.1)]');
        btnTable.classList.remove('text-gray-400', 'border-transparent');
        
        btnCalendar.classList.remove('bg-crypto-dark', 'border-white/20', 'text-white', 'shadow-[0_0_15px_rgba(255,255,255,0.1)]');
        btnCalendar.classList.add('text-gray-400', 'border-transparent');
    });

    btnCalendar.addEventListener('click', () => {
        tableContainer.classList.add('hidden');
        calendarContainer.classList.remove('hidden');
        
        btnCalendar.classList.add('bg-crypto-dark', 'border-white/20', 'text-white', 'shadow-[0_0_15px_rgba(255,255,255,0.1)]');
        btnCalendar.classList.remove('text-gray-400', 'border-transparent');
        
        btnTable.classList.remove('bg-crypto-dark', 'border-white/20', 'text-white', 'shadow-[0_0_15px_rgba(255,255,255,0.1)]');
        btnTable.classList.add('text-gray-400', 'border-transparent');

        renderCalendar();
    });
});
</script>
@endpush