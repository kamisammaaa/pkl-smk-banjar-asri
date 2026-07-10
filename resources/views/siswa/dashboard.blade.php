@extends('layouts.app')
@section('page-title', 'Dashboard Siswa')

@push('styles')
<style>
    /* ── Dashboard Gradient Background ── */
    .dash-bg {
        background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 50%, #f0fdf4 100%);
        min-height: calc(100vh - 4rem);
    }

    /* ── Hero Welcome Card ── */
    .hero-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-card::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .hero-card::after {
        content: '';
        position: absolute;
        bottom: -80px; left: -40px;
        width: 250px; height: 250px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    /* ── Stat Cards ── */
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.04);
    }
    .stat-card .accent-bar {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
    }

    /* ── Attendance Mini Cards ── */
    .att-badge {
        border-radius: 12px;
        padding: 0.75rem 0.5rem;
        text-align: center;
        transition: all 0.2s ease;
        cursor: default;
    }
    .att-badge:hover { transform: scale(1.05); }

    /* ── Section Cards ── */
    .section-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }
    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(to right, #fafbff, #ffffff);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ── Progress Bar ── */
    .progress-bar {
        height: 6px;
        border-radius: 9999px;
        background: #e2e8f0;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .progress-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 1s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── Quick Action Buttons ── */
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 0.5rem;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        border: 1px solid transparent;
    }
    .quick-action:hover {
        transform: translateY(-3px);
        border-color: rgba(79,70,229,0.2);
        box-shadow: 0 8px 20px -4px rgba(79,70,229,0.15);
    }
    .quick-action .icon-wrap {
        width: 3rem; height: 3rem;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        transition: transform 0.2s ease;
    }
    .quick-action:hover .icon-wrap { transform: scale(1.1) rotate(-5deg); }
    .quick-action span:last-child {
        font-size: 0.68rem;
        font-weight: 700;
        color: #475569;
        text-align: center;
        line-height: 1.2;
    }

    /* ── Announcement Items ── */
    .announcement-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.2s ease;
        display: flex;
        gap: 0.875rem;
        align-items: flex-start;
    }
    .announcement-item:last-child { border-bottom: none; }
    .announcement-item:hover { background: #fafbff; }

    /* ── Visit Timeline ── */
    .visit-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        gap: 0.875rem;
        align-items: flex-start;
        transition: background 0.2s ease;
    }
    .visit-item:last-child { border-bottom: none; }
    .visit-item:hover { background: #fafbff; }

    /* ── Info Chips ── */
    .info-chip {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1rem;
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
    }
    .info-chip:hover {
        border-color: #c7d2fe;
        box-shadow: 0 4px 12px -2px rgba(79,70,229,0.1);
    }

    /* ── Status Badge ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.025em;
    }

    /* ── Pulse dot animation ── */
    .pulse-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        animation: pulse-anim 2s ease-in-out infinite;
    }
    @keyframes pulse-anim {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.85); }
    }

    /* ── Fade-in animation ── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-in   { animation: fadeInUp 0.4s ease both; }
    .fade-in-1 { animation-delay: 0.05s; }
    .fade-in-2 { animation-delay: 0.1s; }
    .fade-in-3 { animation-delay: 0.15s; }
    .fade-in-4 { animation-delay: 0.2s; }
    .fade-in-5 { animation-delay: 0.25s; }

    /* ── Modal ── */
    .modal-backdrop {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4 pb-6">

    {{-- HERO WELCOME CARD --}}
    <div class="hero-card rounded-2xl p-5 text-white shadow-lg fade-in fade-in-1">
        <div class="relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl font-black shadow-inner border border-white/30 flex-shrink-0">
                    {{ strtoupper(substr($infoSiswa['nama'], 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-white/70 text-xs font-semibold uppercase tracking-widest">Selamat Datang</p>
                    <h2 class="text-lg font-black text-white leading-tight truncate">{{ $infoSiswa['nama'] }}</h2>
                    <p class="text-white/60 text-xs mt-0.5 flex items-center gap-1.5">
                        <span>{{ $infoSiswa['jurusan'] }}</span>
                        @if($infoSiswa['nis'] !== '-')
                            <span class="w-1 h-1 rounded-full bg-white/40 inline-block"></span>
                            <span>NIS: {{ $infoSiswa['nis'] }}</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-1.5 bg-white/15 rounded-full px-2.5 py-1 border border-white/20 flex-shrink-0">
                    <div class="pulse-dot" style="background:#86efac;"></div>
                    <span class="text-[10px] font-bold text-white/90">Aktif</span>
                </div>
            </div>

            <div class="my-4 border-t border-white/15"></div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('siswa.absensi.index') }}"
                   class="flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-indigo-50 transition active:scale-95 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Absensi Sekarang
                </a>
                <a href="{{ route('siswa.jurnal.index') }}"
                   class="flex items-center gap-2 bg-white/15 text-white border border-white/30 px-4 py-2 rounded-xl text-sm font-bold hover:bg-white/25 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Tulis Jurnal
                </a>
            </div>

            <p class="mt-3 text-white/50 text-[11px] font-medium">
                📅 {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </p>
        </div>
    </div>

    {{-- INFO CHIPS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 fade-in fade-in-2">
        <div class="info-chip">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Perusahaan</p>
                <p class="text-sm font-bold text-slate-800 truncate">{{ $infoSiswa['perusahaan'] }}</p>
            </div>
        </div>
        <div class="info-chip">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Pembimbing</p>
                <p class="text-sm font-bold text-slate-800 truncate">{{ $infoSiswa['pembimbing'] !== '-' ? $infoSiswa['pembimbing'] : 'Belum ditentukan' }}</p>
            </div>
        </div>
        <div class="info-chip">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Periode PKL</p>
                <p class="text-sm font-bold text-slate-800">{{ now()->locale('id')->isoFormat('MMMM Y') }}</p>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="section-card fade-in fade-in-2">
        <div class="section-header">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center text-xs">⚡</span>
                Aksi Cepat
            </h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
            <a href="{{ route('siswa.absensi.index') }}" class="quick-action bg-indigo-50/50">
                <div class="icon-wrap bg-indigo-100">📅</div>
                <span>Absensi</span>
            </a>
            <a href="{{ route('siswa.jurnal.index') }}" class="quick-action bg-emerald-50/50">
                <div class="icon-wrap bg-emerald-100">📖</div>
                <span>Jurnal Harian</span>
            </a>
            <a href="{{ route('siswa.perusahaan') }}" class="quick-action bg-blue-50/50">
                <div class="icon-wrap bg-blue-100">🏢</div>
                <span>Data Mitra</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="quick-action bg-violet-50/50">
                <div class="icon-wrap bg-violet-100">👤</div>
                <span>Profil Saya</span>
            </a>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 fade-in fade-in-3">
        {{-- STATISTIK ABSENSI --}}
        @php
            $totalAbsensi = max(1, $statsAbsensi['hadir'] + $statsAbsensi['terlambat'] + $statsAbsensi['izin'] + $statsAbsensi['sakit'] + $statsAbsensi['alpha']);
            $hadirPct = round(($statsAbsensi['hadir'] / $totalAbsensi) * 100);
        @endphp
        <div class="section-card flex flex-col justify-between">
            <div class="section-header">
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
        <div class="section-card flex flex-col justify-between">
            <div class="section-header">
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
    </div>ap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- GRID: PENGUMUMAN + KUNJUNGAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- PENGUMUMAN --}}
        <div class="section-card fade-in fade-in-4">
            <div class="section-header">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center text-xs">📢</span>
                    <h3 class="font-bold text-slate-800 text-sm">Pengumuman</h3>
                </div>
                @if($pengumuman->count() > 0)
                <span class="w-5 h-5 bg-indigo-600 text-white text-[10px] font-black rounded-full flex items-center justify-center">{{ $pengumuman->count() }}</span>
                @endif
            </div>

            <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                @forelse($pengumuman as $p)
                <div class="announcement-item">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-sm flex-shrink-0 mt-0.5">📣</div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-sm leading-snug" style="word-break:break-word;">{{ $p->judul }}</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $p->published_at?->format('d/m/Y') }} · {{ $p->admin->name }}
                            @if($p->target !== 'semua')
                                <span class="status-badge bg-purple-100 text-purple-700 ml-1">{{ ucfirst($p->target) }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($p->isi), 120) }}</p>
                        <button type="button"
                                onclick="openAnnouncementModal({{ json_encode(['judul' => $p->judul, 'isi' => nl2br(e($p->isi)), 'tanggal' => $p->published_at?->format('d/m/Y H:i'), 'admin' => $p->admin->name, 'target' => ucfirst($p->target)]) }})"
                                class="text-[11px] text-indigo-600 hover:text-indigo-800 font-bold mt-1.5 flex items-center gap-1 transition">
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

        {{-- JADWAL KUNJUNGAN --}}
        <div class="section-card fade-in fade-in-5">
            <div class="section-header">
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

            @if($kunjunganMendatang)
            <div class="m-3 p-3.5 bg-gradient-to-br from-indigo-50 to-violet-50 rounded-xl border border-indigo-100">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center text-base flex-shrink-0">📅</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">Kunjungan Berikutnya</p>
                        <p class="font-black text-indigo-800 text-sm mt-0.5">{{ $kunjunganMendatang->tanggal->format('d/m/Y') }}</p>
                        <p class="text-xs text-indigo-600 mt-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $kunjunganMendatang->pembimbing?->name ?? '-' }}
                        </p>
                        @if($kunjunganMendatang->catatan_rencana)
                            <p class="text-[11px] text-indigo-700 mt-2 bg-white/60 p-2 rounded-lg">
                                <span class="font-bold">Catatan:</span> {{ $kunjunganMendatang->catatan_rencana }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                @forelse($semuaKunjungan as $k)
                <div class="visit-item">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm flex-shrink-0 mt-0.5 {{ $k->status === 'rencana' ? 'bg-blue-100' : 'bg-green-100' }}">
                        {{ $k->status === 'rencana' ? '🔜' : '✅' }}
                    </div>
                    <div class="flex-1 min-w-0">
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
