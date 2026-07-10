@extends('layouts.app')

@section('content')
{{-- ✅ GANTI: max-w-2xl mx-auto → w-full --}}
<div class="w-full px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">➕ Tambah User Baru</h2>
    
    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            <p class="font-semibold">⚠️ Gagal Menyimpan:</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">  <!-- ✅ Grid 2 kolom di desktop -->
            {{-- Nama --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                       class="mt-1 block w-full border rounded p-2">
            </div>
            
            {{-- Email --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       class="mt-1 block w-full border rounded p-2">
            </div>
            
            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required 
                       class="mt-1 block w-full border rounded p-2">
            </div>
            
            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium">Role</label>
                <select name="role" required class="mt-1 block w-full border rounded p-2">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pembimbing" {{ old('role') == 'pembimbing' ? 'selected' : '' }}>Pembimbing</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                </select>
            </div>
            
            {{-- Buttons --}}
            <div class="md:col-span-2 flex gap-2 pt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
