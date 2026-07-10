@extends('layouts.app')
@section('page-title', 'Approve Absensi Siswa')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📅 Approve Absensi Siswa</h2>
            <p class="text-sm text-gray-500 mt-1">Verifikasi kehadiran, sakit, izin, dan libur siswa binaan</p>
        </div>
        <div class="flex gap-2">
            {{-- Badge Pending --}}
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 border border-amber-200 px-3 py-2 rounded-lg text-sm font-semibold">
                    ⏳ {{ $pendingCount }} Menunggu Persetujuan
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-800 border border-green-200 px-3 py-2 rounded-lg text-sm font-semibold">
                    ✅ Semua Sudah Disetujui
                </span>
            @endif
            <a href="{{ route('pembimbing.absensi.export') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                📊 Export CSV
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success')) 
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            <p class="font-semibold">{!! session('success') !!}</p>
        </div> 
    @endif
    @if(session('error')) 
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
            <p class="font-semibold">{!! session('error') !!}</p>
        </div> 
    @endif
    
    {{-- 🔍 Filter Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                <select name="siswa_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua Siswa</option>
                    @foreach($siswaBinaan as $s)
                        <option value="{{ $s->user_id }}" {{ request('siswa_id') == $s->user_id ? 'selected' : '' }}>
                            {{ $s->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Semua</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>⏳ Belum Diverifikasi</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>✅ Sudah Diverifikasi</option>
                    <option value="hadir"    {{ request('status') === 'hadir'    ? 'selected' : '' }}>✅ Hadir</option>
                    <option value="sakit"    {{ request('status') === 'sakit'    ? 'selected' : '' }}>🤒 Sakit</option>
                    <option value="izin"     {{ request('status') === 'izin'     ? 'selected' : '' }}>📝 Izin</option>
                    <option value="libur"    {{ request('status') === 'libur'    ? 'selected' : '' }}>🏖️ Libur</option>
                    <option value="alpha"    {{ request('status') === 'alpha'    ? 'selected' : '' }}>❌ Alpha</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition font-medium">
                    🔍 Filter
                </button>
                <a href="{{ route('pembimbing.absensi') }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition text-gray-700 font-medium text-center">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 📋 Table Absensi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[950px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Detail / Bukti</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">Verifikasi</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($absensis as $a)
                    <tr class="hover:bg-gray-50 transition {{ !$a->is_verified && $a->status !== 'alpha' ? 'bg-amber-50/30' : '' }}">
                        
                        {{-- Tanggal --}}
                        <td class="px-4 py-3 text-gray-800">
                            <span class="font-medium">{{ $a->tanggal->format('d/m/Y') }}</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $a->tanggal->translatedFormat('l') }}</div>
                        </td>
                        
                        {{-- Siswa --}}
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-800">{{ $a->siswa->name }}</span>
                            <div class="text-[10px] text-gray-500 mt-0.5">
                                {{ $a->siswa->siswaProfile?->perusahaan?->nama ?? 'Belum diassign' }}
                            </div>
                        </td>
                        
                        {{-- Status Badge --}}
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($a->status) {
                                    'hadir' => 'bg-green-100 text-green-800 border-green-200',
                                    'sakit' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'izin'  => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'libur' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'alpha' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200',
                                };
                                $icon = match($a->status) {
                                    'hadir' => '✅',
                                    'sakit' => '🤒',
                                    'izin'  => '📝',
                                    'libur' => '🏖️',
                                    'alpha' => '❌',
                                    default => '❓',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ $icon }} {{ ucfirst($a->status) }}
                            </span>
                            @if($a->status === 'libur')
                                <div class="text-[10px] text-purple-500 mt-0.5">Tidak dihitung dalam % hadir</div>
                            @endif
                        </td>
                        
                        {{-- Detail / Bukti --}}
                        <td class="px-4 py-3 max-w-xs">
                            @if($a->status === 'hadir')
                                <div class="space-y-1.5 text-xs">
                                    <p class="text-gray-600">🕒 Masuk: <span class="font-semibold">{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}</span></p>
                                    @if($a->ip_address)
                                        <p class="text-gray-400 font-mono text-[10px]">🌐 {{ $a->ip_address }}</p>
                                    @endif
                                    @if($a->foto)
                                        <a href="{{ Storage::url($a->foto) }}" target="_blank" 
                                           class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 hover:bg-blue-100 px-2 py-1 rounded text-[10px] font-bold border border-blue-200 transition mt-1">
                                            📸 Lihat Selfie
                                        </a>
                                    @endif
                                </div>
                                
                            @elseif(in_array($a->status, ['sakit', 'izin', 'libur']))
                                <div class="space-y-2 text-xs">
                                    @if($a->alasan)
                                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                                            <p class="text-gray-700 italic">"{{ Str::limit($a->alasan, 80) }}"</p>
                                        </div>
                                    @endif
                                    @if($a->bukti_file)
                                        @php
                                            $ext     = pathinfo($a->bukti_file, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <div class="mt-1">
                                                <a href="{{ Storage::url($a->bukti_file) }}" target="_blank" class="block group">
                                                    <img src="{{ Storage::url($a->bukti_file) }}" alt="Bukti" 
                                                         class="w-20 h-20 object-cover rounded border border-gray-200 group-hover:opacity-90 transition">
                                                    <span class="text-[10px] text-purple-600 font-medium mt-1 inline-block">🔍 Klik untuk perbesar</span>
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ Storage::url($a->bukti_file) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 hover:bg-purple-100 px-2 py-1 rounded text-[10px] font-bold border border-purple-200 transition">
                                                📄 Dokumen ({{ strtoupper($ext) }})
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                
                            @else {{-- alpha --}}
                                <span class="text-gray-400 text-xs italic">Tidak mengisi absensi (otomatis)</span>
                            @endif
                        </td>
                        
                        {{-- Status Verifikasi --}}
                        <td class="px-4 py-3 text-center">
                            @if($a->status === 'alpha')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                    🤖 Auto
                                </span>
                            @elseif($a->is_verified)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                    ✅ Disetujui
                                </span>
                                @if($a->verified_at)
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($a->verified_at)->format('d/m H:i') }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                    ⏳ Pending
                                </span>
                            @endif
                        </td>
                        
                        {{-- Aksi --}}
                        <td class="px-4 py-3 text-right">
                            @if($a->status !== 'alpha' && !$a->is_verified)
                                <div class="flex flex-col gap-1 items-end">
                                    {{-- Tombol Approve --}}
                                    <form action="{{ route('pembimbing.absensi.verify', $a->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Setujui absensi {{ addslashes($a->siswa->name) }} ({{ $a->tanggal->format('d/m/Y') }}, {{ ucfirst($a->status) }})?')"
                                                class="w-full justify-center bg-green-600 hover:bg-green-700 text-white font-semibold px-3 py-1.5 rounded-lg text-xs transition shadow-sm active:scale-95 flex items-center gap-1">
                                            ✅ Setujui
                                        </button>
                                    </form>
                                    {{-- Tombol Reject --}}
                                    <form action="{{ route('pembimbing.absensi.reject', $a->id) }}" method="POST" class="w-full" onsubmit="let reason = prompt('Alasan penolakan absensi ini?'); if(reason === null || reason.trim() === '') return false; this.keterangan.value = reason;">
                                        @csrf
                                        <input type="hidden" name="keterangan" value="">
                                        <button type="submit" 
                                                class="w-full justify-center bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1.5 rounded-lg text-xs transition shadow-sm active:scale-95 flex items-center gap-1">
                                            ❌ Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif($a->status !== 'alpha' && $a->is_verified)
                                <span class="text-green-600 text-xs font-semibold">Telah Diverifikasi</span>
                            @else
                                <span class="text-gray-400 text-xs italic">Auto</span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-3xl">📭</div>
                            <p class="text-gray-500 font-medium">Tidak ada data absensi yang cocok.</p>
                            @if(request()->hasAny(['siswa_id', 'status', 'tanggal']))
                                <a href="{{ route('pembimbing.absensi') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Reset semua filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $absensis->appends(request()->query())->links() }}
        </div>
    </div>
    
    {{-- Info Box --}}
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg text-sm text-blue-700">
        <strong>💡 Panduan Verifikasi:</strong><br>
        • Status <strong>Alpha</strong> digenerate otomatis oleh sistem — tidak perlu diverifikasi manual.<br>
        • Status <strong>Libur</strong> 🏖️ tidak dihitung dalam persentase kehadiran siswa.<br>
        • Semua status lain (Hadir, Sakit, Izin, Libur) wajib disetujui agar dianggap sah.<br>
        • Klik <strong>📸 Lihat Selfie</strong> atau <strong>📄 Dokumen</strong> untuk memeriksa bukti sebelum menyetujui.
    </div>
</div>
@endsection