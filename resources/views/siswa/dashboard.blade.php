@extends('layouts.app')
@section('page-title', 'Dashboard Siswa')

@push('styles')
<style>
    /* ── Dashboard Background ── */
    .dash-bg {
        background: #f8fafc;
        min-height: calc(100vh - 4rem);
    }

    /* ── Card Panels ── */
    .section-card,
    .stat-card,
    .info-chip,
    .announcement-item,
    .visit-item {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid rgba(148, 163, 184, 0.9);
        box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08);
    }
    .section-card {
        overflow: hidden;
        min-height: 16rem;
    }
    .section-header {
        padding: 1rem 1.25rem;
        background: #ffffff;
        border-bottom: 1px solid #eef2ff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ── Stats Card ── */
    .stat-card {
        padding: 1.25rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
    }
    .stat-card .accent-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 24px 24px 0 0;
    }

    /* ── Attendance Badges ── */
    .att-badge {
        border-radius: 18px;
        padding: 1rem 0.75rem;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid transparent;
    }
    .att-badge:hover {
        transform: translateY(-2px);
        border-color: rgba(79, 70, 229, 0.16);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }

    /* ── Progress Bar ── */
    .progress-bar {
        height: 8px;
        border-radius: 9999px;
        background: #eef2ff;
        overflow: hidden;
        margin-top: 0.75rem;
    }
    .progress-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.9s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── Quick Actions ── */
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1.1rem 0.75rem;
        border-radius: 22px;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .quick-action:hover {
        transform: translateY(-3px);
        border-color: rgba(79, 70, 229, 0.2);
        box-shadow: 0 12px 32px rgba(99, 102, 241, 0.08);
    }
    .quick-action .icon-wrap {
        width: 3rem;
        height: 3rem;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .quick-action span:last-child {
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
        text-align: center;
        line-height: 1.3;
    }

    /* ── Announcement + Visit items ── */
    .announcement-item,
    .visit-item {
        padding: 0.95rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s ease;
    }
    .announcement-item:last-child,
    .visit-item:last-child {
        border-bottom: none;
    }
    .announcement-item:hover,
    .visit-item:hover {
        background: #f8fafc;
    }

    .announcement-item p,
    .visit-item p {
        color: #475569;
    }

    /* ── Info Chips ── */
    .info-chip {
        border-radius: 18px;
        padding: 0.95rem 1rem;
        gap: 0.85rem;
    }

    /* ── Status Badge ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    /* ── Animation ── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-in   { animation: fadeInUp 0.35s ease both; }
    .fade-in-1 { animation-delay: 0.05s; }
    .fade-in-2 { animation-delay: 0.1s; }
    .fade-in-3 { animation-delay: 0.15s; }
    .fade-in-4 { animation-delay: 0.2s; }
    .fade-in-5 { animation-delay: 0.25s; }

    .modal-backdrop {
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(6px);
    }
</style>
@endpush

@section('content')
<div class="dash-bg min-h-screen py-6">
    <div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4 pb-6">

    {{-- HERO WELCOME CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 fade-in fade-in-1">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Selamat Datang</p>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 truncate">{{ $infoSiswa['nama'] }}</h1>
                <p class="mt-2 text-sm text-slate-600 max-w-2xl">
                    Ringkasan aktivitas PKL kamu di <strong>{{ $infoSiswa['perusahaan'] }}</strong> - {{ $infoSiswa['jurusan'] }}.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl font-black text-slate-800">
                    {{ strtoupper(substr($infoSiswa['nama'], 0, 1)) }}
                </div>
                <div class="rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-700 border border-emerald-100">
                    Aktif
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('siswa.absensi.index') }}" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-xl">📅</div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-[0.16em]">Absensi</p>
                    <p class="text-xl font-black text-slate-900">Lihat</p>
                </div>
            </a>
            <a href="{{ route('siswa.jurnal.index') }}" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-xl">📖</div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-[0.16em]">Jurnal</p>
                    <p class="text-xl font-black text-slate-900">Tulis</p>
                </div>
            </a>
            <a href="{{ route('siswa.perusahaan') }}" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-blue-100 flex items-center justify-center text-xl">🏢</div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-[0.16em]">Data Mitra</p>
                    <p class="text-xl font-black text-slate-900">Cek</p>
                </div>
            </a>
 
        </div>
    </div>

    {{-- INFO CARDS --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-5 fade-in fade-in-2">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="section-card p-4 flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl">🏢</div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Perusahaan</p>
                    <p class="text-sm font-black text-slate-900 truncate">{{ $infoSiswa['perusahaan'] }}</p>
                </div>
            </div>
            <div class="section-card p-4 flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-violet-50 flex items-center justify-center text-violet-600 text-xl">👤</div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Pembimbing</p>
                    <p class="text-sm font-black text-slate-900 truncate">{{ $infoSiswa['pembimbing'] !== '-' ? $infoSiswa['pembimbing'] : 'Belum ditentukan' }}</p>
                </div>
            </div>
            <div class="section-card p-4 flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl">🗓️</div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Periode PKL</p>
                    <p class="text-sm font-black text-slate-900">{{ now()->locale('id')->isoFormat('MMMM Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 fade-in fade-in-3">
        {{-- STATISTIK ABSENSI --}}
        @php
            $totalAbsensi = max(1, $statsAbsensi['hadir'] + $statsAbsensi['terlambat'] + $statsAbsensi['izin'] + $statsAbsensi['sakit'] + $statsAbsensi['alpha']);
            $hadirPct = round(($statsAbsensi['hadir'] / $totalAbsensi) * 100);
        @endphp
        <div class="section-card bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[360px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center text-xs">📊</span>
                    <h3 class="font-bold text-slate-800 text-sm">Statistik Absensi</h3>
                </div>
                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full font-bold border border-indigo-100">
                    {{ now()->locale('id')->isoFormat('MMMM Y') }}
                </span>
            </div>
            <div class="p-4 space-y-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-semibold text-slate-600">Tingkat Kehadiran</span>
                        <span class="font-black text-indigo-600">{{ $hadirPct }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-gradient-to-r from-indigo-500 to-violet-500" id="hadirBar" data-target="{{ $hadirPct }}" style="width:0%;"></div>
                    </div>
                </div>

                <div class="grid grid-cols-5 gap-2 pt-1">
                    <div class="att-badge bg-emerald-50 border border-emerald-100">
                        <div class="text-xl font-black text-emerald-600">{{ $statsAbsensi['hadir'] }}</div>
                        <div class="text-[9px] text-emerald-700 font-bold mt-0.5">Hadir</div>
                    </div>
                    <div class="att-badge bg-amber-50 border border-amber-100">
                        <div class="text-xl font-black text-amber-600">{{ $statsAbsensi['terlambat'] }}</div>
                        <div class="text-[9px] text-amber-700 font-bold mt-0.5">Terlambat</div>
                    </div>
                    <div class="att-badge bg-sky-50 border border-sky-100">
                        <div class="text-xl font-black text-sky-600">{{ $statsAbsensi['izin'] }}</div>
                        <div class="text-[9px] text-sky-700 font-bold mt-0.5">Izin</div>
                    </div>
                    <div class="att-badge bg-orange-50 border border-orange-100">
                        <div class="text-xl font-black text-orange-600">{{ $statsAbsensi['sakit'] }}</div>
                        <div class="text-[9px] text-orange-700 font-bold mt-0.5">Sakit</div>
                    </div>
                    <div class="att-badge bg-red-50 border border-red-100">
                        <div class="text-xl font-black text-red-600">{{ $statsAbsensi['alpha'] }}</div>
                        <div class="text-[9px] text-red-700 font-bold mt-0.5">Alpha</div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex justify-end">
                    <a href="{{ route('siswa.absensi.index') }}" class="text-xs text-indigo-600 font-bold hover:underline flex items-center gap-1">
                        Lihat detail absensi
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- STATISTIK JURNAL --}}
        @php
            $totalJurnal = max(1, $statsJurnal['disetujui'] + $statsJurnal['menunggu'] + $statsJurnal['revisi']);
            $disetujuiPct = round(($statsJurnal['disetujui'] / $totalJurnal) * 100);
        @endphp
        <div class="section-card bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[360px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center text-xs">📖</span>
                    <h3 class="font-bold text-slate-800 text-sm">Statistik Jurnal</h3>
                </div>
                <span class="text-[10px] bg-violet-50 text-violet-600 px-2.5 py-1 rounded-full font-bold border border-violet-100">
                    {{ now()->locale('id')->isoFormat('MMMM Y') }}
                </span>
            </div>
            <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                <div class="grid grid-cols-3 gap-3">
                    <div class="stat-card text-center">
                        <div class="accent-bar bg-gradient-to-r from-emerald-400 to-green-500"></div>
                        <div class="text-3xl font-black text-emerald-600 mt-1">{{ $statsJurnal['disetujui'] }}</div>
                        <div class="text-[10px] text-emerald-700 font-bold mt-1 uppercase tracking-wide">Disetujui</div>
                    </div>
                    <div class="stat-card text-center">
                        <div class="accent-bar bg-gradient-to-r from-amber-400 to-yellow-500"></div>
                        <div class="text-3xl font-black text-amber-600 mt-1">{{ $statsJurnal['menunggu'] }}</div>
                        <div class="text-[10px] text-amber-700 font-bold mt-1 uppercase tracking-wide">Menunggu</div>
                    </div>
                    <div class="stat-card text-center">
                        <div class="accent-bar bg-gradient-to-r from-red-400 to-rose-500"></div>
                        <div class="text-3xl font-black text-red-600 mt-1">{{ $statsJurnal['revisi'] }}</div>
                        <div class="text-[10px] text-red-700 font-bold mt-1 uppercase tracking-wide">Revisi</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-slate-500 font-semibold">{{ $statsJurnal['disetujui'] }} dari {{ $statsJurnal['total'] }} jurnal disetujui</span>
                        <span class="font-black text-violet-600">{{ $disetujuiPct }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-gradient-to-r from-violet-500 to-purple-500" id="jurnalBar" data-target="{{ $disetujuiPct }}" style="width:0%;"></div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                    @if($statsJurnal['revisi'] > 0)
                        <span class="text-[11px] text-red-600 font-semibold">⚠️ {{ $statsJurnal['revisi'] }} jurnal perlu direvisi</span>
                    @elseif($statsJurnal['menunggu'] > 0)
                        <span class="text-[11px] text-amber-600 font-semibold">⏳ {{ $statsJurnal['menunggu'] }} menunggu review</span>
                    @else
                        <span class="text-[11px] text-emerald-600 font-semibold">✅ Semua jurnal disetujui</span>
                    @endif
                    <a href="{{ route('siswa.jurnal.index') }}" class="text-xs text-violet-600 font-bold hover:underline flex items-center gap-1">
                        Buka jurnal
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID: PENGUMUMAN + KUNJUNGAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- PENGUMUMAN --}}
        <div class="section-card fade-in fade-in-4 bg-white rounded-3xl border border-slate-200 shadow-sm min-h-[320px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center text-xs">📢</span>
                    <h3 class="font-bold text-slate-800 text-sm">Pengumuman</h3>
                </div>
                @if($pengumuman->count() > 0)
                <span class="w-5 h-5 bg-indigo-600 text-white text-[10px] font-black rounded-full flex items-center justify-center">{{ $pengumuman->count() }}</span>
                @endif
            </div>

            <div class="p-4">
                <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                    @forelse($pengumuman as $p)
                    <div class="announcement-item py-4 flex items-start gap-4">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-sm flex-shrink-0 mt-0.5">📣</div>
                        <div class="flex-1 min-w-0 space-y-2">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm leading-snug" style="word-break:break-word;">{{ $p->judul }}</h4>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ $p->published_at?->format('d/m/Y') }} · {{ $p->admin->name }}
                                    @if($p->target !== 'semua')
                                        <span class="status-badge bg-purple-100 text-purple-700 ml-1">{{ ucfirst($p->target) }}</span>
                                    @endif
                                </p>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($p->isi), 120) }}</p>
                            <button type="button"
                                    onclick="openAnnouncementModal({{ json_encode(['judul' => $p->judul, 'isi' => nl2br(e($p->isi)), 'tanggal' => $p->published_at?->format('d/m/Y H:i'), 'admin' => $p->admin->name, 'target' => ucfirst($p->target)]) }})"
                                    class="text-[11px] text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 transition">
                                Baca selengkapnya
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mb-3">📭</div>
                        <p class="text-sm font-semibold text-slate-500">Belum ada pengumuman</p>
                        <p class="text-xs text-slate-400 mt-1">Pengumuman dari admin akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- JADWAL KUNJUNGAN --}}
        <div class="section-card fade-in fade-in-5 bg-white rounded-3xl border border-slate-200 shadow-sm min-h-[320px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center text-xs">🏫</span>
                    <h3 class="font-bold text-slate-800 text-sm">Jadwal Kunjungan</h3>
                </div>
                @if($kunjunganMendatang)
                    <span class="status-badge bg-blue-100 text-blue-700 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>
                        Aktif
                    </span>
                @endif
            </div>

            <div class="p-4 space-y-4">
                @if($kunjunganMendatang)
                <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-3xl border border-indigo-100 p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 flex items-center justify-center text-base flex-shrink-0">📅</div>
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">Kunjungan Berikutnya</p>
                            <p class="font-black text-indigo-800 text-sm">{{ $kunjunganMendatang->tanggal->format('d/m/Y') }}</p>
                            <p class="text-xs text-indigo-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $kunjunganMendatang->pembimbing?->name ?? '-' }}
                            </p>
                            @if($kunjunganMendatang->catatan_rencana)
                                <p class="text-[11px] text-indigo-700 mt-2 bg-white/70 p-3 rounded-2xl">
                                    <span class="font-bold">Catatan:</span> {{ $kunjunganMendatang->catatan_rencana }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                    @forelse($semuaKunjungan as $k)
                    <div class="visit-item py-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0 mt-0.5 {{ $k->status === 'rencana' ? 'bg-blue-100' : 'bg-green-100' }}">
                            {{ $k->status === 'rencana' ? '🔜' : '✅' }}
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-slate-800 text-sm">{{ $k->tanggal->format('d/m/Y') }}</h4>
                            @if($k->status === 'rencana')
                                <span class="status-badge bg-blue-100 text-blue-700">Rencana</span>
                            @else
                                <span class="status-badge bg-green-100 text-green-700">Selesai</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                            {{ $k->pembimbing?->name ?? '-' }} · {{ $k->perusahaan?->nama ?? '-' }}
                        </p>
                        @if($k->status === 'rencana' && $k->catatan_rencana)
                            <p class="text-[11px] text-slate-600 mt-1 bg-slate-50 p-1.5 rounded-lg"><strong>Catatan:</strong> {{ $k->catatan_rencana }}</p>
                        @elseif($k->status === 'selesai' && $k->catatan)
                            <p class="text-[11px] text-slate-600 mt-1 bg-green-50 p-1.5 rounded-lg"><strong>Hasil:</strong> {{ $k->catatan }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mb-3">🗓️</div>
                    <p class="text-sm font-semibold text-slate-500">Belum ada jadwal kunjungan</p>
                    <p class="text-xs text-slate-400 mt-1">Jadwal kunjungan akan tampil di sini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- MODAL PENGUMUMAN --}}
<div id="announcementModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="modal-backdrop fixed inset-0" onclick="closeAnnouncementModal()"></div>
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-4 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-4">
                    <h3 class="text-base font-black text-white" id="modal-title">Pengumuman</h3>
                    <p class="text-indigo-200 text-xs mt-1" id="modal-meta"></p>
                </div>
                <button type="button" onclick="closeAnnouncementModal()"
                        class="w-8 h-8 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center transition flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-5 py-4 max-h-[55vh] overflow-y-auto">
                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed" id="modal-content" style="word-break:break-word;"></div>
            </div>
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeAnnouncementModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-bold transition active:scale-95 shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAnnouncementModal(data) {
    const modal = document.getElementById('announcementModal');
    document.getElementById('modal-title').textContent = data.judul;
    document.getElementById('modal-meta').textContent = data.tanggal + ' · Oleh: ' + data.admin + ' · Target: ' + data.target;
    document.getElementById('modal-content').innerHTML = data.isi;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeAnnouncementModal() {
    document.getElementById('announcementModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAnnouncementModal();
});

// Animate progress bars
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.progress-fill[data-target]').forEach(function(el) {
            el.style.width = el.dataset.target + '%';
        });
    }, 300);
});
</script>
@endpush
