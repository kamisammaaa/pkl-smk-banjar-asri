@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-4">📊 Penilaian: {{ $siswa->name }}</h2>
    @if(session('success')) <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div> @endif

    <form action="{{ route('pembimbing.penilaian.store') }}" method="POST" class="bg-white p-5 rounded shadow mb-6">
        @csrf
        <input type="hidden" name="siswa_user_id" value="{{ $siswa->id }}">
        <div class="grid gap-3">
            <select name="kategori" required class="border p-2 rounded">
                <option value="">Pilih Kategori</option>
                <option value="Sikap Kerja">Sikap Kerja</option>
                <option value="Kinerja Teknik">Kinerja Teknik</option>
                <option value="Kedisiplinan">Kedisiplinan</option>
                <option value="Kerjasama Tim">Kerjasama Tim</option>
            </select>
            <input type="number" name="nilai" min="0" max="100" placeholder="Nilai (0-100)" required class="border p-2 rounded">
            <textarea name="keterangan" placeholder="Keterangan (opsional)" class="border p-2 rounded"></textarea>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Tambah Penilaian</button>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50"><tr><th class="p-3">Kategori</th><th class="p-3">Nilai</th><th class="p-3">Keterangan</th><th class="p-3">Tanggal</th></tr></thead>
            <tbody>
                @forelse($penilaian as $p)
                <tr class="border-t"><td class="p-3">{{ $p->kategori }}</td><td class="p-3 font-bold text-blue-600">{{ $p->nilai }}</td><td class="p-3">{{ $p->keterangan }}</td><td class="p-3 text-sm">{{ $p->created_at->format('d/m/Y') }}</td></tr>
                @empty
                <tr><td colspan="4" class="p-3 text-center text-gray-500">Belum ada penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection