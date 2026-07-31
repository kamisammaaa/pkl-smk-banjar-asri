@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">📢 Buat Pengumuman Baru</h2>
    <form action="{{ route('admin.pengumuman.store') }}" method="POST" class="glass-panel p-6 rounded shadow">
        @csrf
        <div class="grid gap-4">
            <div><label class="block text-sm font-medium">Judul</label><input type="text" name="judul" required class="mt-1 block w-full border rounded p-2"></div>
            <div><label class="block text-sm font-medium">Isi Pengumuman</label><textarea name="isi" rows="6" required class="mt-1 block w-full border rounded p-2"></textarea></div>
            <div><label class="block text-sm font-medium">Target Penerima</label>
                <select name="target" required class="mt-1 block w-full border rounded p-2">
                    <option value="semua">Semua User</option>
                    <option value="siswa">Hanya Siswa</option>
                    <option value="pembimbing">Hanya Pembimbing</option>
                </select>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Terbitkan</button>
                <a href="{{ route('admin.pengumuman.index') }}" class="bg-gray-300 px-4 py-2 rounded">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection