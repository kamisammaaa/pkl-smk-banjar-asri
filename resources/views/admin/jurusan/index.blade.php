@extends('layouts.app')
@section('page-title', 'Kelola Jurusan')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">🎓 Kelola Jurusan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data kompetensi keahlian / jurusan sekolah</p>
        </div>
    </div>

    @if(session('success')) 
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            {!! session('success') !!}
        </div> 
    @endif

    <!-- Form Tambah Jurusan -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">➕ Tambah Jurusan Baru</h3>
        <form action="{{ route('admin.jurusan.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="flex-1">
                <input 
                    type="text" 
                    name="nama" 
                    placeholder="Nama Jurusan (contoh: Teknik Komputer dan Jaringan)" 
                    required 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg shadow font-medium text-sm transition active:scale-95 whitespace-nowrap">
                💾 Simpan Jurusan
            </button>
        </form>
    </div>

    <!-- Table List Jurusan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Nama Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jurusan as $j)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $j->nama }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.jurusan.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus jurusan {{ $j->nama }}?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium px-3 py-1.5 rounded hover:bg-red-50 transition">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-12 text-center text-gray-500">
                            <div class="text-3xl mb-2">📭</div>
                            <p>Belum ada data jurusan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection