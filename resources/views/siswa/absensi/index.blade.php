@extends('layouts.app')
@section('page-title', 'Absensi Harian')

@section('content')
<div class="min-h-screen py-6 bg-slate-50">
    <div class="w-full max-w-6xl mx-auto space-y-6 px-2 md:px-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
           

        </div>

    {{-- Alert Messages --}}
    @if(session('success')) 
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl shadow-sm flex items-start gap-3">
            <div class="mt-0.5 shrink-0">✅</div>
            <p>{!! session('success') !!}</p>
        </div> 
    @endif
    
    @if(session('error')) 
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm flex items-start gap-3">
            <div class="mt-0.5 shrink-0">⚠️</div>
            <p>{!! session('error') !!}</p>
        </div> 
    @endif

    @if($errors->any()) 
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm">
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
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-xl font-black text-slate-900">📅 Absensi Harian</h1>
                    <p class="text-sm text-slate-500 mt-1">Lihat riwayat absensi, status verifikasi, dan detail kehadiran PKL Anda.</p>
                </div>
            </div>

            <div class="mt-6">
                @if($periode)
                    <div class="rounded-3xl border p-4 shadow-sm
                        {{ $periodeAktif ? 'bg-green-50 border-green-200 text-green-800' : ($belumMulai ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-gray-50 border-gray-200 text-gray-600') }}">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $periodeAktif ? '🟢' : ($belumMulai ? '🕐' : '🔴') }}</span>
                                <div>
                                    <p class="font-bold text-sm">
                                        @if($periodeAktif) Periode PKL Berjalan
                                        @elseif($belumMulai) Periode PKL Belum Dimulai
                                        @else Periode PKL Selesai
                                        @endif
                                    </p>
                                    <p class="text-xs mt-1 leading-relaxed">
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} – {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            @if($belumMulai)
                                <span class="text-xs font-semibold text-slate-600">Mulai <strong>{{ $startDate->diffForHumans() }}</strong></span>
                            @elseif($sudahSelesai)
                                <span class="text-xs font-semibold text-slate-600">Periode selesai</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-3xl p-4 text-sm text-yellow-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">⚠️</span>
                            <p>Belum ada periode PKL aktif untuk perusahaan Anda. Hubungi Admin.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Keterangan Status</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                    <p class="font-bold text-slate-700">✅ Hadir</p>
                    <p class="mt-1 text-[11px] text-slate-500">Masuk tepat waktu.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                    <p class="font-bold text-slate-700">🤒 Sakit</p>
                    <p class="mt-1 text-[11px] text-slate-500">Dengan bukti surat.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                    <p class="font-bold text-slate-700">📝 Izin</p>
                    <p class="mt-1 text-[11px] text-slate-500">Dengan surat izin.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                    <p class="font-bold text-slate-700">❌ Alpha</p>
                    <p class="mt-1 text-[11px] text-slate-500">Alasan tidak jelas.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b bg-slate-50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">📜 Riwayat Absensi</h3>
                    <p class="text-xs text-slate-500 mt-1">Menampilkan 10 data terakhir per halaman.</p>
                </div>
                @if($periodeAktif)
                    <a href="{{ route('siswa.absensi.create') }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-2xl transition shadow-sm flex items-center gap-2 font-semibold active:scale-95">
                        ➕ Tambah Absensi
                    </a>
                @else
                    <span class="text-xs text-slate-500 italic">Absensi hanya bisa dilakukan saat periode PKL berjalan.</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[600px]">
                    <thead class="bg-slate-100 border-b">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-slate-700 text-[11px] uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 font-semibold text-slate-700 text-[11px] uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-3 font-semibold text-slate-700 text-[11px] uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 font-semibold text-slate-700 text-[11px] uppercase tracking-wider">Verifikasi</th>
                            <th class="px-4 py-3 font-semibold text-slate-700 text-[11px] uppercase tracking-wider">Keterangan / Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($absensis as $a)
                        <tr class="hover:bg-slate-50 transition {{ $a->status === 'alpha' ? 'bg-red-50/30' : '' }} {{ $a->status === 'libur' ? 'bg-purple-50/20' : '' }}">
                            <td class="px-4 py-4 font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}
                                <div class="text-[11px] text-slate-400 font-normal mt-1">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-4 py-4 text-slate-600">
                                @if($a->status === 'hadir')
                                    <span class="font-semibold">{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}</span>
                                    @if($a->is_late)
                                        <div class="mt-1 inline-flex items-center gap-1 rounded-full bg-yellow-100 text-yellow-800 text-[10px] px-2 py-1 border border-yellow-200">
                                            ⏱ Terlambat
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs italic">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $badge = match($a->status) {
                                        'hadir'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'sakit'  => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'izin'   => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'libur'  => 'bg-violet-100 text-violet-800 border-violet-200',
                                        'alpha'  => 'bg-red-100 text-red-800 border-red-200',
                                        default  => 'bg-slate-100 text-slate-800 border-slate-200',
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
                                    <span class="text-xs font-bold text-red-600">❌ Alpha Otomatis</span>
                                    <div class="text-[10px] text-slate-400 mt-1">Oleh sistem</div>
                                @elseif($a->is_verified)
                                    <span class="text-xs font-bold text-emerald-700 flex items-center gap-1">✅ Disetujui</span>
                                    @if($a->verified_at)
                                        <div class="text-[10px] text-slate-400 mt-1">{{ \Carbon\Carbon::parse($a->verified_at)->format('d/m/Y H:i') }}</div>
                                    @endif
                                @else
                                    <span class="text-xs font-bold text-amber-700 flex items-center gap-1">⏳ Menunggu Review</span>
                                    <div class="text-[10px] text-slate-400 mt-1">Belum disetujui</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($a->alasan)
                                    <p class="text-xs text-slate-600 italic mb-2 bg-slate-50 p-2 rounded-xl border border-slate-100 line-clamp-2">
                                        "{{ Str::limit($a->alasan, 60) }}"
                                    </p>
                                @endif
                                <div class="flex flex-wrap gap-2">
                                    @if($a->status === 'hadir' && $a->foto)
                                        <a href="{{ Storage::url($a->foto) }}" target="_blank"
                                           class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 hover:bg-sky-100 px-2.5 py-1 rounded-xl text-xs font-bold border border-sky-200 shadow-sm">
                                            📸 Selfie
                                        </a>
                                    @endif
                                    @if(in_array($a->status, ['sakit', 'izin']) && $a->bukti_file)
                                        <a href="{{ Storage::url($a->bukti_file) }}" target="_blank"
                                           class="inline-flex items-center gap-1 bg-violet-50 text-violet-700 hover:bg-violet-100 px-2.5 py-1 rounded-xl text-xs font-bold border border-violet-200 shadow-sm">
                                            📄 Surat Bukti
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center bg-slate-50">
                                <div class="text-4xl mb-3 opacity-50">📭</div>
                                <p class="text-slate-600 font-medium">Belum ada riwayat absensi.</p>
                                @if($periodeAktif)
                                    <a href="{{ route('siswa.absensi.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-semibold underline">
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
            <div class="px-4 py-3 border-t bg-slate-50">
                {{ $absensis->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection