@extends('layouts.app')
@section('page-title', 'Absensi Harian')

@section('content')
<div class="min-h-screen py-6">
    <div class="w-full max-w-6xl mx-auto space-y-6 px-2 md:px-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
           

        </div>

    {{-- Alert Messages --}}
    @if(session('success')) 
        <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-xl shadow-[0_0_15px_rgba(14,203,129,0.2)] flex items-start gap-3">
            <div class="mt-0.5 shrink-0">✅</div>
            <p>{!! session('success') !!}</p>
        </div> 
    @endif
    
    @if(session('error')) 
        <div class="glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.2)] flex items-start gap-3">
            <div class="mt-0.5 shrink-0">⚠️</div>
            <p>{!! session('error') !!}</p>
        </div> 
    @endif

    @if($errors->any()) 
        <div class="glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.2)]">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div> 
    @endif

    @php
        $today = \Carbon\Carbon::today();
        $periodeAktif = false;
        $belumMulai = false;
        $sudahSelesai = false;
        if(isset($periode) && $periode) {
            $startDate = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
            $endDate = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();
            $periodeAktif = $periode->is_active && $today->between($startDate, $endDate);
            $belumMulai = $today->lt($startDate);
            $sudahSelesai = $today->gt($endDate);
        }
    @endphp

    <div class="space-y-6">
        <div class="glass-panel rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-xl font-black text-white">📅 Absensi Harian</h1>
                    <p class="text-sm text-gray-400 mt-1">Lihat riwayat absensi, status verifikasi, dan detail kehadiran PKL Anda.</p>
                </div>
            </div>

            <div class="mt-6">
                @if($periode)
                    <div class="rounded-3xl border p-4 shadow-sm backdrop-blur-md
                        {{ $periodeAktif ? 'bg-crypto-success/20 border-crypto-success/30 text-crypto-success shadow-[0_0_15px_rgba(14,203,129,0.2)]' : ($belumMulai ? 'bg-yellow-500/20 border-yellow-500/30 text-yellow-400 shadow-[0_0_15px_rgba(234,179,8,0.2)]' : 'bg-gray-500/20 border-gray-500/30 text-gray-400') }}">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $periodeAktif ? '🟢' : ($belumMulai ? '🕐' : '🔴') }}</span>
                                <div>
                                    <p class="font-bold text-sm text-white">
                                        @if($periodeAktif) Periode PKL Berjalan
                                        @elseif($belumMulai) Periode PKL Belum Dimulai
                                        @else Periode PKL Selesai
                                        @endif
                                    </p>
                                    <p class="text-xs mt-1 leading-relaxed opacity-90">
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} – {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            @if($belumMulai)
                                <span class="text-xs font-semibold opacity-90">Mulai <strong>{{ $startDate->diffForHumans() }}</strong></span>
                            @elseif($sudahSelesai)
                                <span class="text-xs font-semibold opacity-90">Periode selesai</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="glass-panel bg-yellow-500/20 border border-yellow-500/30 rounded-3xl p-4 text-sm text-yellow-400 shadow-[0_0_15px_rgba(234,179,8,0.2)]">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl drop-shadow-[0_0_8px_rgba(234,179,8,0.5)]">⚠️</span>
                            <p>Belum ada periode PKL aktif untuk perusahaan Anda. Hubungi Admin.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="glass-panel rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Keterangan Status</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-300">
                <div class="rounded-2xl border border-crypto-success/20 bg-crypto-success/10 p-3 shadow-inner">
                    <p class="font-bold text-crypto-success">✅ Hadir</p>
                    <p class="mt-1 text-[11px] text-gray-400">Masuk tepat waktu.</p>
                </div>
                <div class="rounded-2xl border border-orange-500/20 bg-orange-500/10 p-3 shadow-inner">
                    <p class="font-bold text-orange-400">🤒 Sakit</p>
                    <p class="mt-1 text-[11px] text-gray-400">Dengan bukti surat.</p>
                </div>
                <div class="rounded-2xl border border-blue-500/20 bg-blue-500/10 p-3 shadow-inner">
                    <p class="font-bold text-blue-400">📝 Izin</p>
                    <p class="mt-1 text-[11px] text-gray-400">Dengan surat izin.</p>
                </div>
                <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-3 shadow-inner">
                    <p class="font-bold text-red-400">❌ Alpha</p>
                    <p class="mt-1 text-[11px] text-gray-400">Alasan tidak jelas.</p>
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-3xl border border-white/5 shadow-[0_0_15px_rgba(0,0,0,0.1)] overflow-hidden">
            <div class="p-5 border-b border-white/10 bg-white/5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between relative">
                <div>
                    <h3 class="font-bold text-white text-base">📜 Riwayat Absensi</h3>
                    <p class="text-xs text-gray-400 mt-1">Menampilkan 10 data terakhir per halaman.</p>
                </div>
                @if($periodeAktif)
                    <a href="{{ route('siswa.absensi.create') }}"
                       class="bg-crypto-accent hover:bg-crypto-accentHover text-white text-sm px-4 py-2 rounded-2xl transition-all shadow-[0_0_10px_rgba(112,0,255,0.3)] flex items-center gap-2 font-semibold active:scale-95">
                        ➕ Tambah Absensi
                    </a>
                @else
                    <span class="text-xs text-gray-400 italic">Absensi hanya bisa dilakukan saat periode PKL berjalan.</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[600px]">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-300 text-[11px] uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 font-semibold text-gray-300 text-[11px] uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-3 font-semibold text-gray-300 text-[11px] uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-300 text-[11px] uppercase tracking-wider">Verifikasi</th>
                            <th class="px-4 py-3 font-semibold text-gray-300 text-[11px] uppercase tracking-wider">Keterangan / Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($absensis as $a)
                        <tr class="hover:bg-white/5 transition-colors {{ $a->status === 'alpha' ? 'bg-red-500/10' : '' }} {{ $a->status === 'libur' ? 'bg-purple-500/10' : '' }}">
                            <td class="px-4 py-4 font-semibold text-white">
                                {{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}
                                <div class="text-[11px] text-gray-400 font-normal mt-1">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-4 py-4 text-gray-300">
                                @if($a->status === 'hadir')
                                    <span class="font-semibold text-white">{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}</span>
                                    @if($a->is_late)
                                        <div class="mt-1 inline-flex items-center gap-1 rounded-full bg-yellow-500/20 text-yellow-400 text-[10px] px-2 py-1 border border-yellow-500/30">
                                            ⏱ Terlambat
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-500 text-xs italic">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $badge = match($a->status) {
                                        'hadir'  => 'bg-crypto-success/20 text-crypto-success border-crypto-success/30 shadow-[0_0_8px_rgba(14,203,129,0.2)]',
                                        'sakit'  => 'bg-orange-500/20 text-orange-400 border-orange-500/30 shadow-[0_0_8px_rgba(249,115,22,0.2)]',
                                        'izin'   => 'bg-blue-500/20 text-blue-400 border-blue-500/30 shadow-[0_0_8px_rgba(59,130,246,0.2)]',
                                        'libur'  => 'bg-purple-500/20 text-purple-400 border-purple-500/30 shadow-[0_0_8px_rgba(168,85,247,0.2)]',
                                        'alpha'  => 'bg-red-500/20 text-red-400 border-red-500/30 shadow-[0_0_8px_rgba(239,68,68,0.2)]',
                                        default  => 'bg-white/10 text-gray-300 border-white/20',
                                    };
                                    $icon = match($a->status) {
                                        'hadir'  => '✅',
                                        'sakit'  => '🤒',
                                        'izin'   => '📝',
                                        'libur'  => '🏖️',
                                        'alpha'  => '❌',
                                        default  => '❓',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge }}">
                                    {{ $icon }} {{ ucfirst($a->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($a->status === 'alpha')
                                    <span class="text-xs font-bold text-red-400 drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">❌ Alpha Otomatis</span>
                                    <div class="text-[10px] text-gray-500 mt-1">Oleh sistem</div>
                                @elseif($a->is_verified)
                                    <span class="text-xs font-bold text-crypto-success flex items-center gap-1 drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">✅ Disetujui</span>
                                    @if($a->verified_at)
                                        <div class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($a->verified_at)->format('d/m/Y H:i') }}</div>
                                    @endif
                                @else
                                    <span class="text-xs font-bold text-yellow-400 flex items-center gap-1 drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]">⏳ Menunggu Review</span>
                                    <div class="text-[10px] text-gray-500 mt-1">Belum disetujui</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($a->alasan)
                                    <p class="text-xs text-gray-300 italic mb-2 bg-white/5 p-2 rounded-xl border border-white/10 line-clamp-2">
                                        "{{ Str::limit($a->alasan, 60) }}"
                                    </p>
                                @endif
                                <div class="flex flex-wrap gap-2">
                                    @if($a->status === 'hadir' && $a->foto)
                                        <a href="{{ Storage::url($a->foto) }}" target="_blank"
                                           class="inline-flex items-center gap-1 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 px-2.5 py-1 rounded-xl text-xs font-bold border border-blue-500/30 shadow-[0_0_8px_rgba(59,130,246,0.2)] transition-colors">
                                            📸 Selfie
                                        </a>
                                    @endif
                                    @if(in_array($a->status, ['sakit', 'izin']) && $a->bukti_file)
                                        <a href="{{ Storage::url($a->bukti_file) }}" target="_blank"
                                           class="inline-flex items-center gap-1 bg-crypto-accent/20 text-crypto-accent hover:bg-crypto-accent/30 px-2.5 py-1 rounded-xl text-xs font-bold border border-crypto-accent/30 shadow-[0_0_8px_rgba(112,0,255,0.2)] transition-colors">
                                            📄 Surat Bukti
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center bg-white/5">
                                <div class="text-4xl mb-3 opacity-50 drop-shadow-[0_0_10px_rgba(255,255,255,0.2)]">📭</div>
                                <p class="text-gray-400 font-medium">Belum ada riwayat absensi.</p>
                                @if($periodeAktif)
                                    <a href="{{ route('siswa.absensi.create') }}" class="mt-2 inline-block text-crypto-accent hover:text-crypto-accentHover text-sm font-semibold underline transition-colors">
                                        Input absensi hari ini
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($absensis->hasPages())
            <div class="px-4 py-3 border-t border-white/10 bg-white/5">
                {{ $absensis->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection