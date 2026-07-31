@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 space-y-6">
    <h2 class="text-2xl font-bold mb-4 text-white drop-shadow-md">📊 Penilaian: {{ $siswa->name }}</h2>
    @if(session('success')) <div class="glass-panel bg-crypto-success/20 border-l-4 border-crypto-success text-crypto-success p-4 rounded-lg mb-4 font-bold shadow-[0_0_15px_rgba(14,203,129,0.2)]">{{ session('success') }}</div> @endif

    <form action="{{ route('pembimbing.penilaian.store') }}" method="POST" class="glass-panel p-6 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 mb-6">
        @csrf
        <input type="hidden" name="siswa_user_id" value="{{ $siswa->id }}">
        <div class="grid gap-4">
            <select name="kategori" required class="bg-crypto-dark border border-white/20 text-white p-2.5 rounded-lg focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                <option value="">Pilih Kategori</option>
                <option value="Sikap Kerja">Sikap Kerja</option>
                <option value="Kinerja Teknik">Kinerja Teknik</option>
                <option value="Kedisiplinan">Kedisiplinan</option>
                <option value="Kerjasama Tim">Kerjasama Tim</option>
            </select>
            <input type="number" name="nilai" min="0" max="100" placeholder="Nilai (0-100)" required class="bg-crypto-dark border border-white/20 text-white placeholder-gray-500 p-2.5 rounded-lg focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            <textarea name="keterangan" placeholder="Keterangan (opsional)" class="bg-crypto-dark border border-white/20 text-white placeholder-gray-500 p-2.5 rounded-lg focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors"></textarea>
            <button type="submit" class="bg-crypto-success text-white px-6 py-2.5 rounded-lg hover:bg-emerald-500 shadow-[0_0_15px_rgba(14,203,129,0.3)] font-bold active:scale-95 transition-colors">Tambah Penilaian</button>
        </div>
    </form>

    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-white/5 border-b border-white/10"><tr><th class="p-4 font-semibold text-gray-300">Kategori</th><th class="p-4 font-semibold text-gray-300">Nilai</th><th class="p-4 font-semibold text-gray-300">Keterangan</th><th class="p-4 font-semibold text-gray-300">Tanggal</th></tr></thead>
            <tbody>
                @forelse($penilaian as $p)
                <tr class="border-t border-white/5 hover:bg-white/5 transition-colors text-gray-200"><td class="p-4 font-medium">{{ $p->kategori }}</td><td class="p-4 font-bold text-crypto-accent drop-shadow-[0_0_5px_rgba(112,0,255,0.5)]">{{ $p->nilai }}</td><td class="p-4 text-gray-300">{{ $p->keterangan }}</td><td class="p-4 text-sm text-gray-400">{{ $p->created_at->format('d/m/Y') }}</td></tr>
                @empty
                <tr><td colspan="4" class="p-8 text-center text-gray-400 font-medium">Belum ada penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection