@extends('layouts.app')

@section('content')
{{-- ✅ GANTI: max-w-2xl mx-auto → w-full --}}
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-white drop-shadow-md">➕ Tambah User Baru</h2>
        <p class="text-sm text-gray-400 mt-1">Buat akun pengguna baru (Admin, Pembimbing, atau Siswa)</p>
    </div>
    
    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="glass-panel border-l-4 border-red-500 p-4 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.1)]">
            <p class="font-bold text-red-400">⚠️ Gagal Menyimpan:</p>
            <ul class="mt-2 list-disc list-inside text-sm text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="glass-panel p-6 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">  <!-- ✅ Grid 2 kolom di desktop -->
            {{-- Nama --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                       class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            
            {{-- Email --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            
            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" name="password" required 
                       class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
            </div>
            
            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                <select name="role" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pembimbing" {{ old('role') == 'pembimbing' ? 'selected' : '' }}>Pembimbing</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                </select>
            </div>
            
            {{-- Buttons --}}
            <div class="md:col-span-2 flex gap-3 pt-4 border-t border-white/10 mt-2">
                <button type="submit" class="bg-crypto-accent hover:bg-purple-600 text-white px-6 py-2.5 rounded-lg shadow-[0_0_15px_rgba(112,0,255,0.3)] transition font-bold active:scale-95">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="glass-panel/10 border border-white/20 hover:glass-panel/20 text-white px-6 py-2.5 rounded-lg shadow transition font-bold active:scale-95 text-center">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
