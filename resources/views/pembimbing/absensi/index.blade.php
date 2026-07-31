@extends('layouts.app')
@section('page-title', 'Approve Absensi Siswa')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white drop-shadow-md">📅 Approve Absensi Siswa</h2>
            <p class="text-sm text-gray-400 mt-1">Verifikasi kehadiran, sakit, izin, dan libur siswa binaan</p>
        </div>
        <div class="flex gap-2">
            {{-- Badge Pending --}}
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-1.5 bg-amber-500/20 text-amber-400 border border-amber-500/30 px-3 py-2 rounded-lg text-sm font-semibold shadow-inner">
                    ⏳ {{ $pendingCount }} Menunggu Persetujuan
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-crypto-success/20 text-crypto-success border border-crypto-success/30 px-3 py-2 rounded-lg text-sm font-semibold shadow-inner">
                    ✅ Semua Sudah Disetujui
                </span>
            @endif
            <a href="{{ route('pembimbing.absensi.export') }}" 
               class="bg-crypto-success hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.3)] transition-colors flex items-center gap-2 font-medium text-sm active:scale-95">
                📊 Export CSV
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success')) 
        <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.2)]">
            <p class="font-semibold">{!! session('success') !!}</p>
        </div> 
    @endif
    @if(session('error')) 
        <div class="glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-lg shadow-[0_0_15px_rgba(239,68,68,0.2)]">
            <p class="font-semibold">{!! session('error') !!}</p>
        </div> 
    @endif
    
    {{-- 🔍 Filter Bar --}}
    <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Siswa</label>
                <select name="siswa_id" class="w-full bg-crypto-dark border border-white/20 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">Semua Siswa</option>
                    @foreach($siswaBinaan as $s)
                        <option value="{{ $s->user_id }}" {{ request('siswa_id') == $s->user_id ? 'selected' : '' }}>
                            {{ $s->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full bg-crypto-dark border border-white/20 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
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
                <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       class="w-full bg-crypto-dark border border-white/20 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-crypto-accent hover:bg-crypto-accentHover text-white px-4 py-2 rounded-lg text-sm transition-colors shadow-[0_0_15px_rgba(112,0,255,0.3)] font-bold active:scale-95">
                    🔍 Filter
                </button>
                <a href="{{ route('pembimbing.absensi') }}" 
                   class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-sm transition-colors text-white font-medium text-center active:scale-95">
                    ↺ Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 📋 Table Absensi --}}
    <div class="mb-4 flex gap-2 hidden" id="bulkActions">
        <form action="{{ route('pembimbing.absensi.bulk-verify') }}" method="POST" id="formBulkVerify" class="inline">
            @csrf
            <input type="hidden" name="absensi_ids" id="verify_ids">
            <button type="submit" class="bg-crypto-success hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 shadow-[0_0_15px_rgba(14,203,129,0.3)] transition-colors active:scale-95">
                ✅ Setujui Terpilih (<span id="countVerify">0</span>)
            </button>
        </form>
        <form action="{{ route('pembimbing.absensi.bulk-reject') }}" method="POST" id="formBulkReject" class="inline">
            @csrf
            <input type="hidden" name="absensi_ids" id="reject_ids">
            <input type="hidden" name="keterangan" id="reject_keterangan">
            <button type="button" onclick="promptBulkReject()" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-colors active:scale-95">
                ❌ Tolak Terpilih
            </button>
        </form>
    </div>

    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[950px]">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300 w-10 text-center">
                            <input type="checkbox" id="selectAll" class="rounded bg-crypto-dark border-white/20 text-crypto-accent focus:ring-crypto-accent cursor-pointer">
                        </th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Detail / Bukti</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Verifikasi</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($absensis as $a)
                    <tr class="hover:bg-white/5 transition-colors {{ !$a->is_verified && $a->status !== 'alpha' ? 'bg-amber-500/10' : '' }}">
                        
                        {{-- Checkbox Bulk --}}
                        <td class="px-4 py-3 text-center">
                            @if($a->status !== 'alpha' && !$a->is_verified)
                                <input type="checkbox" class="bulk-cb rounded bg-crypto-dark border-white/20 text-crypto-accent focus:ring-crypto-accent cursor-pointer" value="{{ $a->id }}">
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-4 py-3 text-gray-200">
                            <span class="font-medium">{{ $a->tanggal->format('d/m/Y') }}</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $a->tanggal->translatedFormat('l') }}</div>
                        </td>
                        
                        {{-- Siswa --}}
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-200">{{ $a->siswa->name }}</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">
                                {{ $a->siswa->siswaProfile?->perusahaan?->nama ?? 'Belum diassign' }}
                            </div>
                        </td>
                        
                        {{-- Status Badge --}}
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($a->status) {
                                    'hadir' => 'bg-crypto-success/20 text-crypto-success border-crypto-success/30 shadow-inner',
                                    'sakit' => 'bg-orange-500/20 text-orange-400 border-orange-500/30 shadow-inner',
                                    'izin'  => 'bg-blue-500/20 text-blue-400 border-blue-500/30 shadow-inner',
                                    'libur' => 'bg-purple-500/20 text-purple-400 border-purple-500/30 shadow-inner',
                                    'alpha' => 'bg-red-500/20 text-red-400 border-red-500/30 shadow-inner',
                                    default => 'bg-gray-500/20 text-gray-400 border-gray-500/30 shadow-inner',
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
                                <div class="text-[10px] text-purple-400 mt-0.5">Tidak dihitung dalam % hadir</div>
                            @endif
                        </td>
                        
                        {{-- Detail / Bukti --}}
                        <td class="px-4 py-3 max-w-xs">
                            @if($a->status === 'hadir')
                                <div class="space-y-1.5 text-xs">
                                    <p class="text-gray-300">🕒 Masuk: <span class="font-bold text-white">{{ $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-' }}</span></p>
                                    @if($a->ip_address)
                                        <p class="text-gray-500 font-mono text-[10px]">🌐 {{ $a->ip_address }}</p>
                                    @endif
                                    @if($a->foto)
                                        <a href="{{ Storage::url($a->foto) }}" target="_blank" 
                                           class="inline-flex items-center gap-1 bg-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white px-2 py-1 rounded text-[10px] font-bold border border-blue-500/30 transition-colors mt-1">
                                            📸 Lihat Selfie
                                        </a>
                                    @endif
                                </div>
                                
                            @elseif(in_array($a->status, ['sakit', 'izin', 'libur']))
                                <div class="space-y-2 text-xs">
                                    @if($a->alasan)
                                        <div class="bg-black/20 p-2 rounded border border-white/5 shadow-inner">
                                            <p class="text-gray-300 italic">"{{ Str::limit($a->alasan, 80) }}"</p>
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
                                                         class="w-20 h-20 object-cover rounded border border-white/10 group-hover:opacity-90 transition shadow-sm">
                                                    <span class="text-[10px] text-crypto-accent font-bold mt-1 inline-block drop-shadow-sm">🔍 Klik untuk perbesar</span>
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ Storage::url($a->bukti_file) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 bg-purple-500/20 text-purple-400 hover:bg-purple-500 hover:text-white px-2 py-1 rounded text-[10px] font-bold border border-purple-500/30 transition-colors">
                                                📄 Dokumen ({{ strtoupper($ext) }})
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                
                            @else {{-- alpha --}}
                                <span class="text-gray-500 text-xs italic">Tidak mengisi absensi (otomatis)</span>
                            @endif
                        </td>
                        
                        {{-- Status Verifikasi --}}
                        <td class="px-4 py-3 text-center">
                            @if($a->status === 'alpha')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30 shadow-inner">
                                    🤖 Auto
                                </span>
                            @elseif($a->is_verified)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-crypto-success/20 text-crypto-success border border-crypto-success/30 shadow-inner">
                                    ✅ Disetujui
                                </span>
                                @if($a->verified_at)
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($a->verified_at)->format('d/m H:i') }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 shadow-inner animate-pulse">
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
                                                class="w-full justify-center bg-crypto-success/20 hover:bg-crypto-success text-crypto-success hover:text-white border border-crypto-success/30 font-bold px-3 py-1.5 rounded-lg text-xs transition-colors active:scale-95 flex items-center gap-1 shadow-inner">
                                            ✅ Setujui
                                        </button>
                                    </form>
                                    {{-- Tombol Reject --}}
                                    <form action="{{ route('pembimbing.absensi.reject', $a->id) }}" method="POST" class="w-full" onsubmit="let reason = prompt('Alasan penolakan absensi ini?'); if(reason === null || reason.trim() === '') return false; this.keterangan.value = reason;">
                                        @csrf
                                        <input type="hidden" name="keterangan" value="">
                                        <button type="submit" 
                                                class="w-full justify-center bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 font-bold px-3 py-1.5 rounded-lg text-xs transition-colors active:scale-95 flex items-center gap-1 shadow-inner">
                                            ❌ Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif($a->status !== 'alpha' && $a->is_verified)
                                <span class="text-crypto-success text-xs font-bold drop-shadow-[0_0_5px_rgba(14,203,129,0.5)]">Telah Diverifikasi</span>
                            @else
                                <span class="text-gray-500 text-xs italic">Auto</span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-3xl opacity-50 drop-shadow-[0_0_10px_rgba(255,255,255,0.2)]">📭</div>
                            <p class="text-gray-400 font-medium">Tidak ada data absensi yang cocok.</p>
                            @if(request()->hasAny(['siswa_id', 'status', 'tanggal']))
                                <a href="{{ route('pembimbing.absensi') }}" class="text-crypto-accent hover:text-white transition-colors text-sm mt-2 inline-block">Reset semua filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-white/10 bg-white/5">
            {{ $absensis->appends(request()->query())->links() }}
        </div>
    </div>
    
    {{-- Info Box --}}
    <div class="glass-panel border-l-4 border-crypto-accent bg-crypto-accent/10 p-4 rounded-r-lg text-sm text-gray-300">
        <strong class="text-white drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">💡 Panduan Verifikasi:</strong><br>
        • Status <strong class="text-white">Alpha</strong> digenerate otomatis oleh sistem — tidak perlu diverifikasi manual.<br>
        • Status <strong class="text-white">Libur</strong> 🏖️ tidak dihitung dalam persentase kehadiran siswa.<br>
        • Semua status lain (Hadir, Sakit, Izin, Libur) wajib disetujui agar dianggap sah.<br>
        • Klik <strong class="text-white">📸 Lihat Selfie</strong> atau <strong class="text-white">📄 Dokumen</strong> untuk memeriksa bukti sebelum menyetujui.
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.bulk-cb');
    const bulkActions = document.getElementById('bulkActions');
    const countVerify = document.getElementById('countVerify');
    const verifyIds = document.getElementById('verify_ids');
    const rejectIds = document.getElementById('reject_ids');
    const rejectKeterangan = document.getElementById('reject_keterangan');
    const formBulkVerify = document.getElementById('formBulkVerify');
    const formBulkReject = document.getElementById('formBulkReject');

    function updateBulkActions() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (checked.length > 0) {
            bulkActions.classList.remove('hidden');
            countVerify.innerText = checked.length;
            verifyIds.value = checked.join(',');
            rejectIds.value = checked.join(',');
        } else {
            bulkActions.classList.add('hidden');
        }
    }

    if(selectAll) {
        selectAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateBulkActions();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    if(formBulkVerify) {
        formBulkVerify.addEventListener('submit', (e) => {
            if(!confirm('Anda yakin ingin menyetujui data yang dipilih?')) {
                e.preventDefault();
            }
        });
    }

    window.promptBulkReject = function() {
        const reason = prompt('Masukkan alasan penolakan untuk data yang dipilih:');
        if(reason !== null && reason.trim() !== '') {
            rejectKeterangan.value = reason;
            formBulkReject.submit();
        }
    };
});
</script>
@endpush