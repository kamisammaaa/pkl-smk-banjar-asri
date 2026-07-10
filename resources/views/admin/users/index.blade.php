@extends('layouts.app')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">👥 Data Pengguna</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola akun siswa & pembimbing</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.import') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                📥 Import CSV
            </a>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                + Tambah Manual
            </a>
        </div>
    </div>

    @if(session('success')) 
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            {!! session('success') !!}
        </div> 
    @endif

    <!-- 🔍 Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            
            <!-- Search Input -->
            <div class="flex-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau email..." 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
            </div>
            
            <!-- Role Filter -->
            <div class="sm:w-48">
                <select name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">-- Semua Role --</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>🎓 Siswa</option>
                    <option value="pembimbing" {{ request('role') == 'pembimbing' ? 'selected' : '' }}>👨‍🏫 Pembimbing</option>
                </select>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition text-sm font-medium active:scale-95">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow transition text-sm font-medium text-center">
                    🔄 Reset
                </a>
            </div>
        </form>
        
        <!-- Active Filters Info -->
        @if(request('search') || request('role'))
        <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
            <span>Filter aktif:</span>
            @if(request('search'))
                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('role'))
                <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Role: {{ ucfirst(request('role')) }}</span>
            @endif
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline ml-1">Clear all</a>
        </div>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[650px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Nama</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Role</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->role === 'siswa' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1.5 rounded hover:bg-blue-50 transition">✏️ Edit</a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus user {{ $u->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1.5 rounded hover:bg-red-50 transition">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2">🔍</div>
                            <p class="text-gray-500">Tidak ada data pengguna yang cocok dengan filter.</p>
                            @if(request('search') || request('role'))
                                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline text-sm mt-1 inline-block">Reset filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination with preserved filters -->
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $users->appends(['search' => request('search'), 'role' => request('role')])->links() }}
        </div>
    </div>
</div>
@endsection