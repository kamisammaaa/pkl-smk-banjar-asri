@extends('layouts.app')
@section('page-title', 'Review Jurnal')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white drop-shadow-md">📖 Review Jurnal Siswa</h2>
        @php $pending = $jurnals->where('status','menunggu')->count(); @endphp
        @if($pending > 0)
        <span class="inline-flex items-center gap-1 bg-yellow-500/20 text-yellow-400 text-sm font-semibold px-3 py-1 rounded-full border border-yellow-500/30 shadow-inner">
            ⏳ {{ $pending }} menunggu review
        </span>
        @endif
    </div>

    @if(session('success'))
    <div class="flex items-start gap-3 glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-lg shadow-[0_0_15px_rgba(14,203,129,0.2)]">
        <span class="text-xl">✅</span><span>{{ session('success') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="glass-panel bg-red-500/20 border-l-4 border-red-500 text-red-400 p-4 rounded-lg shadow-[0_0_15px_rgba(239,68,68,0.2)]">
        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Filter --}}
    <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-300 mb-1">Filter Siswa</label>
                <select name="siswa_id" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent transition-colors">
                    <option value="">Semua Siswa Binaan</option>
                    @foreach($siswaBinaan as $s)
                        <option value="{{ $s->user_id }}" {{ request('siswa_id') == $s->user_id ? 'selected' : '' }}>{{ $s->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent transition-colors">
                    <option value="">Semua</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>🔄 Revisi</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-crypto-accent hover:bg-crypto-accentHover text-white px-4 py-2 rounded-lg text-sm transition-colors font-bold shadow-[0_0_15px_rgba(112,0,255,0.3)] active:scale-95">🔍 Filter</button>
                <a href="{{ route('pembimbing.jurnal') }}" class="flex-1 text-center bg-white/10 border border-white/20 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm transition-colors font-medium active:scale-95">Reset</a>
                @php $exportUrl = route('pembimbing.jurnal.export', request()->all()); @endphp
                <a href="{{ $exportUrl }}" class="flex-1 text-center bg-crypto-success hover:bg-emerald-500 shadow-[0_0_15px_rgba(14,203,129,0.3)] text-white px-4 py-2 rounded-lg text-sm transition-colors font-bold active:scale-95" title="Export CSV">📥 Export</a>
            </div>
        </form>
    </div>

    {{-- Bulk Actions --}}
    <div class="mb-4 flex gap-2 hidden items-center glass-panel p-3 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5" id="bulkActions">
        <form action="{{ route('pembimbing.jurnal.bulk-approve') }}" method="POST" id="formBulkApprove" class="flex flex-wrap gap-3 items-center w-full">
            @csrf
            <input type="hidden" name="jurnal_ids" id="approve_ids">
            <span class="text-sm font-medium text-gray-300">Setujui <span id="countApprove" class="font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">0</span> jurnal terpilih dengan nilai:</span>
            <input type="number" name="nilai" min="0" max="100" value="85" required class="bg-crypto-dark border border-white/20 text-white rounded-lg px-2 py-1.5 text-sm w-20 focus:ring-2 focus:ring-crypto-accent transition-colors">
            <button type="submit" class="bg-crypto-success hover:bg-emerald-500 text-white px-4 py-1.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-[0_0_15px_rgba(14,203,129,0.3)] transition-colors active:scale-95">
                ✅ Setujui Massal
            </button>
        </form>
    </div>

    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-sm text-left">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-3 py-3 sm:px-4 font-semibold text-gray-300 w-10 text-center">
                            <input type="checkbox" id="selectAll" class="rounded bg-crypto-dark border-white/20 text-crypto-accent focus:ring-crypto-accent cursor-pointer">
                        </th>
                        <th class="px-3 py-3 sm:px-4 font-semibold text-gray-300 w-[13%]">Tanggal</th>
                        <th class="px-3 py-3 sm:px-4 font-semibold text-gray-300 w-[20%]">Siswa</th>
                        <th class="px-3 py-3 sm:px-4 font-semibold text-gray-300 w-[42%]">Ringkasan</th>
                        <th class="px-3 py-3 sm:px-4 font-semibold text-gray-300 w-[15%]">Status</th>
                        <th class="px-3 py-3 sm:px-4 font-semibold text-gray-300 text-right w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($jurnals as $j)
                    @php
                        $statusClass = match($j->status) {
                            'disetujui' => 'bg-crypto-success/20 text-crypto-success border-crypto-success/30 shadow-inner',
                            'revisi' => 'bg-orange-500/20 text-orange-400 border-orange-500/30 shadow-inner',
                            default => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30 shadow-inner',
                        };
                        $statusLabel = match($j->status) {
                            'disetujui' => 'Disetujui',
                            'revisi' => 'Revisi',
                            default => 'Menunggu',
                        };
                        $preview = \Illuminate\Support\Str::limit(strip_tags($j->kegiatan), 90);
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors align-top">
                        <td class="px-3 py-3 sm:px-4 text-center">
                            @if($j->status !== 'disetujui')
                                <input type="checkbox" class="bulk-cb rounded bg-crypto-dark border-white/20 text-crypto-accent focus:ring-crypto-accent cursor-pointer" value="{{ $j->id }}">
                            @endif
                        </td>
                        <td class="px-3 py-3 sm:px-4 text-gray-200 whitespace-nowrap">
                            <div class="font-medium text-[13px] sm:text-sm">{{ $j->tanggal->locale('id')->isoFormat('D MMM Y') }}</div>
                            <div class="text-[10px] sm:text-[11px] text-gray-400">{{ $j->tanggal->locale('id')->isoFormat('dddd') }}</div>
                        </td>
                        <td class="px-3 py-3 sm:px-4">
                            <div class="font-semibold text-gray-200 text-[13px] sm:text-sm">{{ $j->siswa->name }}</div>
                            <div class="text-[10px] sm:text-[11px] text-gray-400 leading-4">{{ $j->siswa->siswaProfile->perusahaan->nama ?? '-' }}</div>
                        </td>
                        <td class="px-3 py-3 sm:px-4">
                            <div class="text-gray-300 text-[13px] sm:text-sm leading-5">{{ $preview }}</div>
                            @if($j->kendala)
                                <div class="text-[10px] sm:text-[11px] text-amber-500 mt-1 leading-4">⚠️ {{ \Illuminate\Support\Str::limit(strip_tags($j->kendala), 60) }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3 sm:px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-bold border {{ $statusClass }}">
                                {{ $j->status === 'disetujui' ? '✅' : ($j->status === 'revisi' ? '🔄' : '⏳') }} {{ $statusLabel }}
                            </span>
                            @if($j->nilai !== null)
                                <div class="text-[10px] sm:text-[11px] text-crypto-accent mt-1 font-bold drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">Nilai: {{ $j->nilai }}/100</div>
                            @endif
                        </td>
                        <td class="px-3 py-3 sm:px-4 text-right">
                            <button type="button" onclick="openJurnalModal({{ $j->id }})" class="w-full sm:w-auto bg-crypto-accent/20 text-crypto-accent border border-crypto-accent/30 hover:bg-crypto-accent hover:text-white px-2.5 py-2 sm:px-3 sm:py-1.5 rounded-lg text-[11px] sm:text-xs font-bold transition-colors">
                                👁️ Lihat
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-3xl opacity-50 drop-shadow-[0_0_10px_rgba(255,255,255,0.2)]">📭</div>
                            <p class="text-gray-400 font-medium">Tidak ada jurnal yang perlu ditinjau.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($jurnals as $j)
    <div id="jurnal-modal-{{ $j->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <!-- Backdrop with blur -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeJurnalModal({{ $j->id }})"></div>
        
        <!-- Modal Content Container -->
        <div class="relative w-full max-w-2xl rounded-2xl glass-panel bg-[#1a1a2e] shadow-[0_0_30px_rgba(112,0,255,0.2)] border border-white/10 flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200" style="max-height: 85vh; display: flex; flex-direction: column;">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-4 flex-shrink-0 bg-white/5">
                <div>
                    <h3 class="text-base font-bold text-white drop-shadow-md">Detail Jurnal Harian</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $j->siswa->name }} • {{ $j->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <button type="button" onclick="closeJurnalModal({{ $j->id }})" class="text-gray-400 hover:text-white text-2xl transition-colors leading-none">&times;</button>
            </div>

            <!-- Scrollable Modal Body -->
            <div class="p-6 space-y-5 flex-1" style="overflow-y: auto; flex: 1 1 0%;">
                <!-- Status Badge Banner -->
                @php
                    $bannerStyle = match($j->status) {
                        'disetujui' => 'bg-crypto-success/20 border-crypto-success/30 text-crypto-success',
                        'revisi' => 'bg-orange-500/20 border-orange-500/30 text-orange-400',
                        default => 'bg-amber-500/20 border-amber-500/30 text-amber-400',
                    };
                    $bannerLabel = match($j->status) {
                        'disetujui' => 'Jurnal ini telah disetujui',
                        'revisi' => 'Jurnal ini memerlukan revisi dari siswa',
                        default => 'Jurnal ini menunggu review dan persetujuan Anda',
                    };
                @endphp
                <div class="border rounded-xl p-3 text-xs font-bold flex items-center gap-2 shadow-inner {{ $bannerStyle }}">
                    <span>{{ $j->status === 'disetujui' ? '✅' : ($j->status === 'revisi' ? '🔄' : '⏳') }}</span>
                    <span>{{ $bannerLabel }}</span>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-white/10 p-4 bg-black/20 shadow-inner">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">Kegiatan Siswa</p>
                        <p class="mt-1.5 text-sm text-gray-300 leading-relaxed whitespace-pre-line">{{ $j->kegiatan }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 p-4 bg-black/20 shadow-inner">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">Kendala / Hambatan</p>
                        <p class="mt-1.5 text-sm text-gray-300 leading-relaxed whitespace-pre-line">{{ $j->kendala ?: 'Tidak ada kendala.' }}</p>
                    </div>
                </div>

                @if($j->foto)
                <div class="rounded-xl border border-white/10 p-4 bg-black/20 shadow-inner">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)] mb-2">Foto Dokumentasi</p>
                    <div class="flex justify-center">
                        <a href="{{ Storage::url($j->foto) }}" target="_blank" class="group relative block overflow-hidden rounded-lg border border-white/10 bg-crypto-dark shadow-[0_0_15px_rgba(0,0,0,0.5)]" style="max-width: 280px; width: 100%;">
                            <img src="{{ Storage::url($j->foto) }}" alt="Foto jurnal" class="w-full object-contain transition duration-300 group-hover:scale-105" style="max-height: 180px; height: 180px; display: block; margin: 0 auto;">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold">
                                🔍 Klik untuk memperbesar
                            </div>
                        </a>
                    </div>
                </div>
                @endif

                @if($j->catatan_revisi)
                <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 shadow-inner">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-red-400 drop-shadow-[0_0_5px_rgba(239,68,68,0.5)]">Catatan Revisi Sebelumnya</p>
                    <p class="mt-1 text-sm text-red-300 leading-relaxed">{{ $j->catatan_revisi }}</p>
                </div>
                @endif

                <!-- Form Persetujuan -->
                <form action="{{ route('pembimbing.jurnal.approve', $j->id) }}" method="POST" class="rounded-2xl border border-white/10 p-5 bg-white/5 space-y-4 shadow-inner">
                    @csrf
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Tindakan Persetujuan & Penilaian</p>
                    
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-300 mb-1">Status Persetujuan</label>
                            <select name="status" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent transition-colors" onchange="toggleCatatanRevisi({{ $j->id }}, this.value)">
                                <option value="disetujui" {{ $j->status === 'disetujui' ? 'selected' : '' }}>✅ Setujui Jurnal</option>
                                <option value="revisi" {{ $j->status === 'revisi' ? 'selected' : '' }}>🔄 Minta Revisi</option>
                                <option value="menunggu" {{ $j->status === 'menunggu' ? 'selected' : '' }}>⏳ Tetap Menunggu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-300 mb-1">Nilai Kegiatan (0-100)</label>
                            <input type="number" name="nilai" min="0" max="100" placeholder="Contoh: 85" value="{{ old('nilai', $j->nilai) }}" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-crypto-accent transition-colors">
                        </div>
                    </div>

                    <div id="catatan-{{ $j->id }}" class="{{ $j->status === 'revisi' ? '' : 'hidden' }}">
                        <label class="block text-[11px] font-semibold text-gray-300 mb-1">Instruksi Revisi untuk Siswa</label>
                        <textarea name="catatan_revisi" placeholder="Sebutkan bagian apa saja yang perlu diperbaiki oleh siswa..." class="w-full bg-crypto-dark border border-white/20 text-white placeholder-gray-500 rounded-lg px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-crypto-accent transition-colors" rows="2">{{ old('catatan_revisi', $j->catatan_revisi) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-white/10">
                        <button type="button" onclick="closeJurnalModal({{ $j->id }})" class="bg-white/10 border border-white/20 hover:bg-white/20 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors active:scale-95">Batal</button>
                        <button type="submit" class="bg-crypto-success hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-[0_0_15px_rgba(14,203,129,0.3)] active:scale-95">Simpan Hasil Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <div class="px-1">{{ $jurnals->appends(request()->query())->links() }}</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.bulk-cb');
    const bulkActions = document.getElementById('bulkActions');
    const countApprove = document.getElementById('countApprove');
    const approveIds = document.getElementById('approve_ids');
    const formBulkApprove = document.getElementById('formBulkApprove');

    function updateBulkActions() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (checked.length > 0) {
            bulkActions.classList.remove('hidden');
            countApprove.innerText = checked.length;
            approveIds.value = checked.join(',');
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

    if(formBulkApprove) {
        formBulkApprove.addEventListener('submit', (e) => {
            if(!confirm('Anda yakin ingin menyetujui jurnal yang dipilih dengan nilai tersebut?')) {
                e.preventDefault();
            }
        });
    }
});

function toggleCatatanRevisi(id, value) {
    const el = document.getElementById('catatan-' + id);
    if (value === 'revisi') {
        el.classList.remove('hidden');
        el.querySelector('textarea').setAttribute('required', 'required');
    } else {
        el.classList.add('hidden');
        el.querySelector('textarea').removeAttribute('required');
    }
}

function openJurnalModal(id) {
    const modal = document.getElementById('jurnal-modal-' + id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }
}

function closeJurnalModal(id) {
    const modal = document.getElementById('jurnal-modal-' + id);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
}
</script>
@endsection
