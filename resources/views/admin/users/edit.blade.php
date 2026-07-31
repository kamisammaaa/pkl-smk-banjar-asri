@extends('layouts.app')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">← Kembali</a>
        <h2 class="text-2xl font-bold text-white drop-shadow-md">✏️ Edit User: {{ $user->name }}</h2>
    </div>

    <div class="glass-panel p-6 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                    <select name="role" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                        <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="pembimbing" {{ old('role', $user->role) == 'pembimbing' ? 'selected' : '' }}>Pembimbing</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer pb-2.5">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="w-4 h-4 text-crypto-accent border border-white/20 bg-crypto-dark rounded focus:ring-crypto-accent focus:ring-2 focus:ring-offset-crypto-dark focus:ring-offset-2">
                        <span class="text-sm font-medium text-gray-300">Akun Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-white/10 mt-2">
                <button type="submit" class="flex-1 bg-crypto-accent text-white font-bold px-6 py-2.5 rounded-lg hover:bg-purple-600 transition shadow-[0_0_15px_rgba(112,0,255,0.3)] active:scale-95">💾 Simpan Perubahan</button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 glass-panel/10 border border-white/20 text-white rounded-lg hover:glass-panel/20 transition text-sm font-bold text-center active:scale-95">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection