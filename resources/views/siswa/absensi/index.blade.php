@extends('layouts.app')
@section('page-title', 'Absensi Harian')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-6 px-2 md:px-4">
    
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN: INFO & LEGEND -->
        <div class="lg:col-span-4 space-y-6">
            {{-- Info Periode PKL --}}
            @if($periode)
            @php
                $today      = \Carbon\Carbon::today();
                $startDate  = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
                $endDate    = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();
                $periodeAktif = $periode->is_active && $today->between($startDate, $endDate);
                $belumMulai = $today->lt($startDate);
                $sudahSelesai = $today->gt($endDate);
            @endphp
            <div class="rounded-xl border p-4 text-sm flex items-start gap-3 shadow-sm
                {{ $periodeAktif ? 'bg-green-50 border-green-200 text-green-800' : ($belumMulai ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-gray-50 border-gray-200 text-gray-600') }}">
                <span class="text-xl shrink-0">{{ $periodeAktif ? '🟢' : ($belumMulai ? '🕐' : '🔴') }}</span>
                <div>
                    <p class="font-bold">
                        @if($periodeAktif) Periode PKL Berjalan
                        @elseif($belumMulai) Periode PKL Belum Dimulai
                        @else Periode PKL Selesai
                        @endif
                    </p>
                    <p class="text-xs mt-1 opacity-95 leading-relaxed">
                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} 
                        – 
                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                        @if($belumMulai)
                            <br>Mulai <strong>{{ $startDate->diffForHumans() }}</strong>
                        @endif
                    </p>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800 flex items-start gap-3 shadow-sm">
                <span class="text-xl">⚠️</span>
                <p>Belum ada periode PKL aktif untuk perusahaan Anda. Hubungi Admin.</p>
            </div>
            @endif

            {{-- Legenda Status --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Keterangan Status</p>
                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-200 font-bold">✅ Hadir</span>
                        <span class="text-[10px] text-gray-400">Masuk tepat waktu</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 border border-orange-200 font-bold">🤒 Sakit</span>
                        <span class="text-[10px] text-gray-400">Dengan bukti surat</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200 font-bold">📝 Izin</span>
                        <span class="text-[10px] text-gray-400">Dengan surat izin</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200 font-bold">🏖️ Libur</span>
                        <span class="text-[10px] text-gray-400">Hari libur nasional</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-200 font-bold">❌ Alpha</span>
                        <span class="text-[10px] text-gray-400">Alasan tidak jelas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: TABLE -->
        <div class="lg:col-span-8 space-y-6">
            {{-- Riwayat Absensi Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-5 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-base">📜 Riwayat Absensi</h3>
                    {{-- Hanya tampilkan tombol jika periode aktif --}}
                    @if(isset($periodeAktif) && $periodeAktif)
                        <a href="{{ route('siswa.absensi.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3.5 py-2 rounded-lg transition shadow-sm flex items-center gap-1.5 font-bold active:scale-95">
                            ➕ Tambah Absensi
                        </a>
                    @else
                        <span class="text-xs text-gray-400 italic">Absensi tidak tersedia</span>
                    @endif
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 font-bold text-gray-700 text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 font-bold text-gray-700 text-xs uppercase tracking-wider">Jam Masuk</th>
                                <th class="px-4 py-3 font-bold text-gray-700 text-xs uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 font-bold text-gray-700 text-xs uppercase tracking-wider">Verifikasi</th>
                                <th class="px-4 py-3 font-bold text-gray-700 text-xs uppercase tracking-wider">Keterangan / Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensis as $a)
                            <tr class="hover:bg-gray-50/50 transition
                                {{ $a->status === 'alpha' ? 'bg-red-50/30' : '' }}
                                {{ $a->status === 'libur' ? 'bg-purple-50/20' : '' }}">
                                
                                {{-- Tanggal --}}
                                <td class="px-4 py-3.5 font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}
                                    <div class="text-xs text-gray-400 font-normal mt-0.5">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('l') }}</div>
                                </td>

                                {{-- Jam Masuk --}}
                                <td class="px-4 py-3.5 text-gray-600">
                                    @if($a->status === 'hadir')
                                        <span class="font-bold">{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}</span>
                                        @if($a->is_late)
                                            <div class="block mt-1">
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    ⏱ Terlambat
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs italic">—</span>
                                    @endif
                                </td>
                                
                                {{-- Status Badge --}}
                                <td class="px-4 py-3.5">
                                    @php
                                        $badge = match($a->status) {
                                            'hadir'  => 'bg-green-100 text-green-800 border-green-200',
                                            'sakit'  => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'izin'   => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'libur'  => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'alpha'  => 'bg-red-100 text-red-800 border-red-200',
                                            default  => 'bg-gray-100 text-gray-800 border-gray-200',
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border {{ $badge }}">
                                        {{ $icon }} {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                                
                                {{-- Status Verifikasi --}}
                                <td class="px-4 py-3.5">
                                    @if($a->status === 'alpha')
                                        <span class="text-xs text-red-600 font-bold">❌ Alpha Otomatis</span>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Oleh sistem</div>
                                    @elseif($a->is_verified)
                                        <span class="text-green-600 text-xs font-bold flex items-center gap-1">
                                            ✅ Disetujui
                                        </span>
                                        @if($a->verified_at)
                                            <div class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($a->verified_at)->format('d/m/Y H:i') }}</div>
                                        @endif
                                    @else
                                        <span class="text-yellow-600 text-xs font-bold flex items-center gap-1">
                                            ⏳ Menunggu Review
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Belum disetujui</div>
                                    @endif
                                </td>
                                
                                {{-- Keterangan & Link Bukti --}}
                                <td class="px-4 py-3.5">
                                    @if($a->alasan)
                                        <p class="text-xs text-gray-600 italic mb-1.5 bg-gray-50 p-2 rounded border border-gray-100 line-clamp-2">
                                            "{{ Str::limit($a->alasan, 60) }}"
                                        </p>
                                    @endif

                                    <div class="flex gap-1.5 flex-wrap">
                                        @if($a->status === 'hadir' && $a->foto)
                                            <a href="{{ Storage::url($a->foto) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 hover:bg-blue-100 px-2.5 py-1 rounded-md text-xs font-bold transition border border-blue-200 shadow-sm">
                                                📸 Selfie
                                            </a>
                                        @endif
                                        
                                        @if(in_array($a->status, ['sakit', 'izin']) && $a->bukti_file)
                                            <a href="{{ Storage::url($a->bukti_file) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 hover:bg-purple-100 px-2.5 py-1 rounded-md text-xs font-bold transition border border-purple-200 shadow-sm">
                                                📄 Surat Bukti
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center bg-gray-50">
                                    <div class="text-4xl mb-3 opacity-50">📭</div>
                                    <p class="text-gray-600 font-medium">Belum ada riwayat absensi.</p>
                                    @if(isset($periodeAktif) && $periodeAktif)
                                        <a href="{{ route('siswa.absensi.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm font-bold underline">
                                            Input absensi hari ini
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($absensis->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $absensis->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsectionomatis)</span>
            </div>
        </div>
    </div>
</div>
@endsection