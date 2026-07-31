@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">✏️ Edit Pengumuman</h2>
    <form action="{{ route('admin.pengumuman.update', $pengumuman) }}" method="POST" class="glass-panel p-6 rounded shadow">
        @csrf @method('PUT')
        <div class="grid gap-4">
            <div><label class="block text-sm font-medium">Judul</label><input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required class="mt-1 block w-full border rounded p-2"></div>
            <div><label class="block text-sm font-medium">Isi Pengumuman</label><textarea name="isi" rows="6" required class="mt-1 block w-full border rounded p-2">{{ old('isi', $pengumuman->isi) }}</textarea></div>
            <div><label class="block text-sm font-medium">Target Penerima</label>
                <select name="target" required class="mt-1 block w-full border rounded p-2">
                    <option value="semua" {{ old('target', $pengumuman->target) == 'semua' ? 'selected' : '' }}>Semua User</option>
                    <option value="siswa" {{ old('target', $pengumuman->target) == 'siswa' ? 'selected' : '' }}>Hanya Siswa</option>
                    <option value="pembimbing" {{ old('target', $pengumuman->target) == 'pembimbing' ? 'selected' : '' }}>Hanya Pembimbing</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pengumuman->is_active) ? 'checked' : '' }} class="rounded">
                <label class="text-sm">Aktifkan Pengumuman</label>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                <a href="{{ route('admin.pengumuman.index') }}" class="bg-gray-300 px-4 py-2 rounded">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection