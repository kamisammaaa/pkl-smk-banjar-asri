@extends('layouts.app')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">← Kembali</a>
        <h2 class="text-xl font-bold text-gray-800">✏️ Edit User: {{ $user->name }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" required class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="pembimbing" {{ old('role', $user->role) == 'pembimbing' ? 'selected' : '' }}>Pembimbing</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Akun Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-medium px-4 py-2.5 rounded-lg hover:bg-blue-700 transition active:scale-95">💾 Simpan Perubahan</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection