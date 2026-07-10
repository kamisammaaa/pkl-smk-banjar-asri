@extends('layouts.app')
@section('page-title', 'Import Pengguna')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">← Kembali</a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-2">📥 Import Data Pengguna (CSV)</h3>
        <p class="text-sm text-gray-600 mb-4">Upload file CSV dengan format: <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">nama, email, password, role</code></p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800 mb-6">
            <strong>📌 Contoh isi file CSV:</strong><br>
            <code class="block mt-2 bg-white p-3 rounded border text-xs overflow-x-auto">
                nama,email,password,role<br>
                Budi Santoso,budi@smk.id,123456,siswa<br>
                Siti Aminah,siti@smk.id,123456,pembimbing
            </code>
            <p class="mt-2 text-xs">💡 Role hanya menerima: <code>siswa</code> atau <code>pembimbing</code>. Email duplikat akan otomatis dilewati.</p>
        </div>

        <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full border rounded-lg px-3 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white font-medium px-4 py-2.5 rounded-lg hover:bg-indigo-700 transition active:scale-95">🚀 Mulai Import</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection