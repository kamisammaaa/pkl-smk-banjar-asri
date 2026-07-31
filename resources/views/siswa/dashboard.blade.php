@extends('layouts.app')
@section('page-title', 'Dashboard Siswa')

@push('styles')
<style>
    /* ── Dashboard Background ── */
    .dash-bg {
        min-height: calc(100vh - 4rem);
    }

    /* ── Card Panels ── */
    .section-card,
    .stat-card,
    .info-chip,
    .announcement-item,
    .visit-item {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }
    .section-card {
        overflow: hidden;
        min-height: 16rem;
    }
    .section-header {
        padding: 1rem 1.25rem;
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
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
        box-shadow: 0 18px 50px rgba(112, 0, 255, 0.15);
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
        border-color: rgba(14, 203, 129, 0.3);
        box-shadow: 0 10px 24px rgba(14, 203, 129, 0.1);
    }

    /* ── Progress Bar ── */
    .progress-bar {
        height: 8px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.1);
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
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .quick-action:hover {
        transform: translateY(-3px);
        border-color: rgba(112, 0, 255, 0.3);
        box-shadow: 0 12px 32px rgba(112, 0, 255, 0.15);
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
        color: #94a3b8;
        text-align: center;
        line-height: 1.3;
    }

    /* ── Announcement + Visit items ── */
    .announcement-item,
    .visit-item {
        padding: 0.95rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.2s ease;
    }
    .announcement-item:last-child,
    .visit-item:last-child {
        border-bottom: none;
    }
    .announcement-item:hover,
    .visit-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .announcement-item p,
    .visit-item p {
        color: #94a3b8;
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
        background: rgba(10, 15, 29, 0.8);
        backdrop-filter: blur(10px);
    }
</style>
@endpush

@section('content')
<div class="dash-bg min-h-screen py-6">
    <div class="max-w-6xl mx-auto space-y-6 px-2 md:px-4 pb-6">

    {{-- HERO WELCOME CARD --}}
    <div class="glass-panel rounded-2xl shadow-[0_0_20px_rgba(0,0,0,0.1)] border border-white/5 p-6 fade-in fade-in-1 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-crypto-accent/5 to-transparent pointer-events-none"></div>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between relative z-10">
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-[0.24em] text-gray-400">Selamat Datang</p>
                <h1 class="text-2xl sm:text-3xl font-black text-white truncate">{{ $infoSiswa['nama'] }}</h1>
                <p class="mt-2 text-sm text-gray-400 max-w-2xl">
                    Ringkasan aktivitas PKL kamu di <strong class="text-crypto-success">{{ $infoSiswa['perusahaan'] }}</strong> - {{ $infoSiswa['jurusan'] }}.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 border border-white/20 text-xl font-black text-white shadow-[0_0_15px_rgba(255,255,255,0.05)]">
                    {{ strtoupper(substr($infoSiswa['nama'], 0, 1)) }}
                </div>
                <div class="rounded-full bg-crypto-success/20 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-crypto-success border border-crypto-success/30 shadow-[0_0_15px_rgba(14,203,129,0.2)]">
                    Aktif
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
            <a href="{{ route('siswa.absensi.index') }}" class="glass-panel border border-white/5 rounded-2xl p-4 hover:border-crypto-accent/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(112,0,255,0.2)] transition-all duration-300 flex items-center gap-3 group">
                <div class="h-12 w-12 rounded-2xl bg-crypto-accent/20 border border-crypto-accent/30 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">📅</div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-[0.16em]">Absensi</p>
                    <p class="text-xl font-black text-white">Lihat</p>
                </div>
            </a>
            <a href="{{ route('siswa.jurnal.index') }}" class="glass-panel border border-white/5 rounded-2xl p-4 hover:border-crypto-success/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(14,203,129,0.2)] transition-all duration-300 flex items-center gap-3 group">
                <div class="h-12 w-12 rounded-2xl bg-crypto-success/20 border border-crypto-success/30 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">📖</div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-[0.16em]">Jurnal</p>
                    <p class="text-xl font-black text-white">Tulis</p>
                </div>
            </a>
            <a href="{{ route('siswa.perusahaan') }}" class="glass-panel border border-white/5 rounded-2xl p-4 hover:border-blue-500/50 hover:bg-white/5 hover:shadow-[0_0_15px_rgba(59,130,246,0.2)] transition-all duration-300 flex items-center gap-3 group">
                <div class="h-12 w-12 rounded-2xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">🏢</div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-[0.16em]">Data Mitra</p>
                    <p class="text-xl font-black text-white">Cek</p>
                </div>
            </a>
        </div>
    </div>

    {{-- PROGRESS & SMART REMINDERS --}}
    <div class="space-y-4 fade-in fade-in-2">
        @if(isset($progress) && $progress['isActive'])
        <div class="glass-panel rounded-3xl p-5 shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 relative overflow-hidden">
            <div class="flex justify-between items-end mb-3 relative z-10">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-crypto-success border border-white/10 text-white flex items-center justify-center text-sm shadow-[0_0_10px_rgba(14,203,129,0.3)]">🚀</span>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide">Progress Masa PKL</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Telah berjalan <strong class="text-crypto-success">{{ $progress['hari_berjalan'] }} hari</strong> dari total {{ $progress['total_hari'] }} hari</p>
                    </div>
                </div>
                <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-crypto-success drop-shadow-[0_0_8px_rgba(14,203,129,0.3)]">{{ $progress['persentase'] }}%</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-4 border border-white/5 overflow-hidden relative z-10 shadow-inner">
                <div class="bg-gradient-to-r from-blue-500 to-crypto-success h-full rounded-full transition-all duration-1000 relative overflow-hidden shadow-[0_0_10px_rgba(14,203,129,0.5)]" style="width: {{ $progress['persentase'] }}%">
                    <!-- Shimmer effect -->
                    <div class="absolute top-0 left-0 w-full h-full bg-white opacity-20 transform -skew-x-12 animate-[shimmer_2s_infinite]"></div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($belumAbsen) && $belumAbsen)
        <div class="bg-red-900/20 border border-red-500/50 p-5 rounded-3xl shadow-[0_0_15px_rgba(239,68,68,0.1)] flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-2 backdrop-blur-md">
            <div class="flex items-start sm:items-center gap-3">
                <span class="text-3xl animate-bounce drop-shadow-[0_0_8px_rgba(239,68,68,0.4)]">⚠️</span>
                <div>
                    <p class="font-bold text-red-200 text-sm md:text-base">Perhatian: Anda belum absen hari ini!</p>
                    <p class="text-xs text-red-300 mt-0.5 leading-relaxed">Segera isi absensi harian Anda sebelum terlambat agar tidak dihitung Alpha.</p>
                </div>
            </div>
            <a href="{{ route('siswa.absensi.create') }}" class="bg-red-600/80 hover:bg-red-500 border border-red-500/50 text-white text-xs font-bold px-6 py-3 rounded-2xl transition whitespace-nowrap shadow-sm active:scale-95 text-center">Isi Absen Sekarang</a>
        </div>
        @elseif(isset($belumJurnal) && $belumJurnal)
        <div class="bg-amber-900/20 border border-amber-500/50 p-5 rounded-3xl shadow-[0_0_15px_rgba(245,158,11,0.1)] flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-2 backdrop-blur-md">
            <div class="flex items-start sm:items-center gap-3">
                <span class="text-3xl animate-bounce drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]">📝</span>
                <div>
                    <p class="font-bold text-amber-200 text-sm md:text-base">Pengingat: Jangan lupa isi Jurnal Harian!</p>
                    <p class="text-xs text-amber-300 mt-0.5 leading-relaxed">Anda sudah melakukan absensi, pastikan Anda juga mencatat kegiatan harian di Jurnal agar mendapatkan nilai maksimal.</p>
                </div>
            </div>
            <a href="{{ route('siswa.jurnal.index') }}" class="bg-amber-500/80 hover:bg-amber-400 border border-amber-500/50 text-white text-xs font-bold px-6 py-3 rounded-2xl transition whitespace-nowrap shadow-sm active:scale-95 text-center">Isi Jurnal Sekarang</a>
        </div>
        @endif
    </div>

    {{-- INFO CARDS --}}
    <div class="glass-panel rounded-3xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 p-5 fade-in fade-in-2">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="section-card p-4 flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 text-xl shadow-[0_0_10px_rgba(59,130,246,0.2)]">🏢</div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Perusahaan</p>
                    <p class="text-sm font-black text-white truncate">{{ $infoSiswa['perusahaan'] }}</p>
                </div>
            </div>
            <div class="section-card p-4 flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-crypto-accent/20 border border-crypto-accent/30 flex items-center justify-center text-crypto-accent text-xl shadow-[0_0_10px_rgba(112,0,255,0.2)]">👤</div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Pembimbing</p>
                    <p class="text-sm font-black text-white truncate">{{ $infoSiswa['pembimbing'] !== '-' ? $infoSiswa['pembimbing'] : 'Belum ditentukan' }}</p>
                </div>
            </div>
            <div class="section-card p-4 flex items-start gap-4">
                <div class="w-11 h-11 rounded-2xl bg-crypto-success/20 border border-crypto-success/30 flex items-center justify-center text-crypto-success text-xl shadow-[0_0_10px_rgba(14,203,129,0.2)]">🗓️</div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Periode PKL</p>
                    <p class="text-sm font-black text-white">{{ now()->locale('id')->isoFormat('MMMM Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 fade-in fade-in-3">
        {{-- STATISTIK ABSENSI --}}
        @php
            $hariAktifBulan = max(1, $statsAbsensi['total'] - $statsAbsensi['libur']);
            // Persentase = (hadir tepat waktu + terlambat) / hari aktif — konsisten dengan laporan pembimbing
            $hadirPct = round((($statsAbsensi['hadir'] + $statsAbsensi['terlambat']) / $hariAktifBulan) * 100);
            $hadirPct = min($hadirPct, 100); // cap 100%
        @endphp
        <div class="section-card glass-panel rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] flex flex-col justify-between min-h-[360px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-crypto-success/20 flex items-center justify-center text-xs shadow-[0_0_8px_rgba(14,203,129,0.2)]">📊</span>
                    <h3 class="font-bold text-white text-sm">Statistik Absensi</h3>
                </div>
                <span class="text-[10px] bg-crypto-accent/20 text-crypto-accent px-2.5 py-1 rounded-full font-bold border border-crypto-accent/30 shadow-[0_0_8px_rgba(112,0,255,0.2)]">
                    {{ now()->locale('id')->isoFormat('MMMM Y') }}
                </span>
            </div>
            <div class="p-4 space-y-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-semibold text-gray-400">Tingkat Kehadiran</span>
                        <span class="font-black text-crypto-accent">{{ $hadirPct }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-gradient-to-r from-crypto-accent to-blue-500 shadow-[0_0_8px_rgba(112,0,255,0.4)]" id="hadirBar" data-target="{{ $hadirPct }}" style="width:0%;"></div>
                    </div>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 pt-1">
                    <div class="att-badge bg-crypto-success/10 border border-crypto-success/20">
                        <div class="text-xl font-black text-crypto-success">{{ $statsAbsensi['hadir'] }}</div>
                        <div class="text-[9px] text-crypto-success/80 font-bold mt-0.5">Hadir</div>
                    </div>
                    <div class="att-badge bg-yellow-500/10 border border-yellow-500/20">
                        <div class="text-xl font-black text-yellow-400">{{ $statsAbsensi['terlambat'] }}</div>
                        <div class="text-[9px] text-yellow-400/80 font-bold mt-0.5">Terlambat</div>
                    </div>
                    <div class="att-badge bg-blue-500/10 border border-blue-500/20">
                        <div class="text-xl font-black text-blue-400">{{ $statsAbsensi['izin'] }}</div>
                        <div class="text-[9px] text-blue-400/80 font-bold mt-0.5">Izin</div>
                    </div>
                    <div class="att-badge bg-orange-500/10 border border-orange-500/20">
                        <div class="text-xl font-black text-orange-400">{{ $statsAbsensi['sakit'] }}</div>
                        <div class="text-[9px] text-orange-400/80 font-bold mt-0.5">Sakit</div>
                    </div>
                    <div class="att-badge bg-purple-500/10 border border-purple-500/20">
                        <div class="text-xl font-black text-purple-400">{{ $statsAbsensi['libur'] }}</div>
                        <div class="text-[9px] text-purple-400/80 font-bold mt-0.5">Libur</div>
                    </div>
                    <div class="att-badge bg-red-500/10 border border-red-500/20">
                        <div class="text-xl font-black text-red-400">{{ $statsAbsensi['alpha'] }}</div>
                        <div class="text-[9px] text-red-400/80 font-bold mt-0.5">Alpha</div>
                    </div>
                </div>

                <div class="pt-2 border-t border-white/10 flex justify-end">
                    <a href="{{ route('siswa.absensi.index') }}" class="text-xs text-crypto-accent font-bold hover:text-crypto-accentHover hover:underline flex items-center gap-1 transition-colors">
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
        <div class="section-card glass-panel rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] flex flex-col justify-between min-h-[360px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-crypto-accent/20 flex items-center justify-center text-xs shadow-[0_0_8px_rgba(112,0,255,0.2)]">📖</span>
                    <h3 class="font-bold text-white text-sm">Statistik Jurnal</h3>
                </div>
                <span class="text-[10px] bg-crypto-accent/20 text-crypto-accent px-2.5 py-1 rounded-full font-bold border border-crypto-accent/30 shadow-[0_0_8px_rgba(112,0,255,0.2)]">
                    {{ now()->locale('id')->isoFormat('MMMM Y') }}
                </span>
            </div>
            <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                <div class="grid grid-cols-3 gap-3">
                    <div class="stat-card text-center hover:bg-white/5 border border-transparent hover:border-crypto-success/30 rounded-xl transition-all">
                        <div class="accent-bar bg-gradient-to-r from-crypto-success to-emerald-500 shadow-[0_0_8px_rgba(14,203,129,0.5)]"></div>
                        <div class="text-3xl font-black text-crypto-success mt-1">{{ $statsJurnal['disetujui'] }}</div>
                        <div class="text-[10px] text-crypto-success/80 font-bold mt-1 uppercase tracking-wide">Disetujui</div>
                    </div>
                    <div class="stat-card text-center hover:bg-white/5 border border-transparent hover:border-yellow-500/30 rounded-xl transition-all">
                        <div class="accent-bar bg-gradient-to-r from-amber-400 to-yellow-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div>
                        <div class="text-3xl font-black text-yellow-400 mt-1">{{ $statsJurnal['menunggu'] }}</div>
                        <div class="text-[10px] text-yellow-400/80 font-bold mt-1 uppercase tracking-wide">Menunggu</div>
                    </div>
                    <div class="stat-card text-center hover:bg-white/5 border border-transparent hover:border-red-500/30 rounded-xl transition-all">
                        <div class="accent-bar bg-gradient-to-r from-red-400 to-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></div>
                        <div class="text-3xl font-black text-red-400 mt-1">{{ $statsJurnal['revisi'] }}</div>
                        <div class="text-[10px] text-red-400/80 font-bold mt-1 uppercase tracking-wide">Revisi</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-gray-400 font-semibold">{{ $statsJurnal['disetujui'] }} dari {{ $statsJurnal['total'] }} jurnal disetujui</span>
                        <span class="font-black text-crypto-accent">{{ $disetujuiPct }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-gradient-to-r from-crypto-accent to-blue-500 shadow-[0_0_8px_rgba(112,0,255,0.4)]" id="jurnalBar" data-target="{{ $disetujuiPct }}" style="width:0%;"></div>
                    </div>
                </div>
                <div class="pt-2 border-t border-white/10 flex items-center justify-between">
                    @if($statsJurnal['revisi'] > 0)
                        <span class="text-[11px] text-red-400 font-semibold">⚠️ {{ $statsJurnal['revisi'] }} jurnal perlu direvisi</span>
                    @elseif($statsJurnal['menunggu'] > 0)
                        <span class="text-[11px] text-yellow-400 font-semibold">⏳ {{ $statsJurnal['menunggu'] }} menunggu review</span>
                    @else
                        <span class="text-[11px] text-crypto-success font-semibold">✅ Semua jurnal disetujui</span>
                    @endif
                    <a href="{{ route('siswa.jurnal.index') }}" class="text-xs text-crypto-accent font-bold hover:text-crypto-accentHover hover:underline flex items-center gap-1 transition-colors">
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
        <div class="section-card glass-panel fade-in fade-in-4 rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] min-h-[320px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-500/20 flex items-center justify-center text-xs shadow-[0_0_8px_rgba(59,130,246,0.2)]">📢</span>
                    <h3 class="font-bold text-white text-sm">Pengumuman</h3>
                </div>
                @if($pengumuman->count() > 0)
                <span class="w-5 h-5 bg-crypto-accent text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-[0_0_8px_rgba(112,0,255,0.4)]">{{ $pengumuman->count() }}</span>
                @endif
            </div>

            <div class="p-4">
                <div class="divide-y divide-white/5 max-h-72 overflow-y-auto pr-2">
                    @forelse($pengumuman as $p)
                    <div class="announcement-item py-4 flex items-start gap-4">
                        <div class="w-8 h-8 rounded-xl bg-crypto-accent/20 border border-crypto-accent/30 flex items-center justify-center text-sm flex-shrink-0 mt-0.5 shadow-[0_0_8px_rgba(112,0,255,0.2)]">📣</div>
                        <div class="flex-1 min-w-0 space-y-2">
                            <div>
                                <h4 class="font-bold text-white text-sm leading-snug" style="word-break:break-word;">{{ $p->judul }}</h4>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    {{ $p->published_at?->format('d/m/Y') }} · {{ $p->admin->name }}
                                    @if($p->target !== 'semua')
                                        <span class="status-badge bg-crypto-accent/20 text-crypto-accent border border-crypto-accent/30 ml-1">{{ ucfirst($p->target) }}</span>
                                    @endif
                                </p>
                            </div>
                            <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($p->isi), 120) }}</p>
                            <button type="button"
                                    onclick="openAnnouncementModal({{ json_encode(['judul' => $p->judul, 'isi' => nl2br(e($p->isi)), 'tanggal' => $p->published_at?->format('d/m/Y H:i'), 'admin' => $p->admin->name, 'target' => ucfirst($p->target)]) }})"
                                    class="text-[11px] text-crypto-accent hover:text-crypto-accentHover font-bold flex items-center gap-1 transition-colors drop-shadow-[0_0_5px_rgba(112,0,255,0.3)]">
                                Baca selengkapnya
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                        <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-2xl mb-3">📭</div>
                        <p class="text-sm font-semibold text-gray-400">Belum ada pengumuman</p>
                        <p class="text-xs text-gray-500 mt-1">Pengumuman dari admin akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- JADWAL KUNJUNGAN --}}
        <div class="section-card glass-panel fade-in fade-in-5 rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] min-h-[320px]">
            <div class="section-header rounded-t-3xl">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-yellow-500/20 flex items-center justify-center text-xs shadow-[0_0_8px_rgba(245,158,11,0.2)]">🏫</span>
                    <h3 class="font-bold text-white text-sm">Jadwal Kunjungan</h3>
                </div>
                @if($kunjunganMendatang)
                    <span class="status-badge bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-[0_0_8px_rgba(59,130,246,0.2)] animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 inline-block drop-shadow-[0_0_5px_rgba(59,130,246,0.8)]"></span>
                        Aktif
                    </span>
                @endif
            </div>

            <div class="p-4 space-y-4">
                @if($kunjunganMendatang)
                <div class="bg-gradient-to-br from-crypto-accent/10 to-blue-500/10 rounded-3xl border border-white/10 p-4 shadow-inner relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/5 backdrop-blur-sm"></div>
                    <div class="flex items-start gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-2xl bg-crypto-accent/20 border border-crypto-accent/30 flex items-center justify-center text-base flex-shrink-0 shadow-[0_0_10px_rgba(112,0,255,0.2)]">📅</div>
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="text-[10px] text-crypto-accent font-bold uppercase tracking-wider">Kunjungan Berikutnya</p>
                            <p class="font-black text-white text-sm">{{ $kunjunganMendatang->tanggal->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-300 flex items-center gap-1">
                                <svg class="w-3 h-3 text-crypto-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $kunjunganMendatang->pembimbing?->name ?? '-' }}
                            </p>
                            @if($kunjunganMendatang->catatan_rencana)
                                <p class="text-[11px] text-gray-200 mt-2 bg-black/20 p-3 rounded-2xl border border-white/5">
                                    <strong class="text-crypto-accent">Catatan:</strong> {{ $kunjunganMendatang->catatan_rencana }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="divide-y divide-white/5 max-h-64 overflow-y-auto pr-2">
                    @forelse($semuaKunjungan as $k)
                    <div class="visit-item py-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl border flex items-center justify-center text-sm flex-shrink-0 mt-0.5 {{ $k->status === 'rencana' ? 'bg-blue-500/20 border-blue-500/30 shadow-[0_0_8px_rgba(59,130,246,0.2)]' : 'bg-crypto-success/20 border-crypto-success/30 shadow-[0_0_8px_rgba(14,203,129,0.2)]' }}">
                            {{ $k->status === 'rencana' ? '🔜' : '✅' }}
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-white text-sm">{{ $k->tanggal->format('d/m/Y') }}</h4>
                            @if($k->status === 'rencana')
                                <span class="status-badge bg-blue-500/20 text-blue-400 border border-blue-500/30">Rencana</span>
                            @else
                                <span class="status-badge bg-crypto-success/20 text-crypto-success border border-crypto-success/30">Selesai</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-400 mt-0.5 truncate">
                            {{ $k->pembimbing?->name ?? '-' }} · {{ $k->perusahaan?->nama ?? '-' }}
                        </p>
                        @if($k->status === 'rencana' && $k->catatan_rencana)
                            <p class="text-[11px] text-gray-300 mt-1 bg-white/5 p-1.5 rounded-lg border border-white/5"><strong class="text-crypto-accent">Catatan:</strong> {{ $k->catatan_rencana }}</p>
                        @elseif($k->status === 'selesai' && $k->catatan)
                            <p class="text-[11px] text-gray-300 mt-1 bg-crypto-success/10 p-1.5 rounded-lg border border-crypto-success/20"><strong class="text-crypto-success">Hasil:</strong> {{ $k->catatan }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-2xl mb-3">🗓️</div>
                    <p class="text-sm font-semibold text-gray-400">Belum ada jadwal kunjungan</p>
                    <p class="text-xs text-gray-500 mt-1">Jadwal kunjungan akan tampil di sini</p>
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
        <div class="bg-crypto-dark border border-white/10 rounded-2xl shadow-[0_0_40px_rgba(112,0,255,0.2)] w-full max-w-lg overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-br from-crypto-accent/5 to-crypto-success/5 pointer-events-none"></div>
            <div class="bg-gradient-to-r from-crypto-accent to-blue-500 px-5 py-4 flex justify-between items-start relative z-10 shadow-[0_0_15px_rgba(112,0,255,0.5)]">
                <div class="flex-1 min-w-0 pr-4">
                    <h3 class="text-base font-black text-white" id="modal-title">Pengumuman</h3>
                    <p class="text-white/80 text-xs mt-1 font-semibold" id="modal-meta"></p>
                </div>
                <button type="button" onclick="closeAnnouncementModal()"
                        class="w-8 h-8 rounded-full bg-white/15 hover:bg-white/30 flex items-center justify-center transition flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-5 py-4 max-h-[55vh] overflow-y-auto relative z-10 custom-scrollbar">
                <div class="prose prose-sm prose-invert max-w-none text-gray-300 leading-relaxed" id="modal-content" style="word-break:break-word;"></div>
            </div>
            <div class="px-5 py-3 bg-white/5 border-t border-white/10 flex justify-end relative z-10">
                <button type="button" onclick="closeAnnouncementModal()"
                        class="bg-crypto-accent hover:bg-crypto-accentHover text-white px-5 py-2 rounded-xl text-sm font-bold transition-all active:scale-95 shadow-[0_0_10px_rgba(112,0,255,0.3)]">
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
