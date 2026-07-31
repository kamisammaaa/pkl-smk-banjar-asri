@extends('layouts.app')
@section('page-title', 'Import Pengguna')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">← Kembali</a>
    </div>

    <div class="glass-panel p-6 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <h3 class="text-xl font-bold text-white drop-shadow-md mb-2">📥 Import Data Pengguna (CSV)</h3>
        <p class="text-sm text-gray-400 mb-4">Upload file CSV dengan format: <code class="bg-crypto-dark px-2 py-0.5 rounded text-xs">nama, email, password, role</code></p>
        
        <div class="glass-panel border-l-4 border-blue-500 p-4 rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.15)] mb-6">
            <strong class="text-blue-400 drop-shadow-md">📌 Contoh isi file CSV:</strong><br>
            <code class="block mt-2 bg-crypto-dark/50 p-3 rounded-lg border border-white/10 text-xs text-gray-300 font-mono overflow-x-auto shadow-inner">
                nama,email,password,role<br>
                Budi Santoso,budi@smk.id,123456,siswa<br>
                Siti Aminah,siti@smk.id,123456,pembimbing
            </code>
            <p class="mt-2 text-xs text-blue-200">💡 Role hanya menerima: <code>siswa</code> atau <code>pembimbing</code>. Email duplikat akan otomatis dilewati.</p>
        </div>

        <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Pilih File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border file:border-crypto-accent/30 file:text-sm file:font-bold file:bg-crypto-accent/20 file:text-crypto-accent hover:file:bg-crypto-accent/30 transition-colors shadow-inner cursor-pointer focus:outline-none focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent">
            </div>
            <div class="flex gap-3 pt-4 border-t border-white/10 mt-2">
                <button type="submit" class="flex-1 bg-crypto-accent text-white font-bold px-6 py-2.5 rounded-lg hover:bg-purple-600 transition shadow-[0_0_15px_rgba(112,0,255,0.3)] active:scale-95">🚀 Mulai Import</button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 bg-white/10 border border-white/20 text-white rounded-lg hover:bg-white/20 transition text-sm font-bold text-center active:scale-95">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection