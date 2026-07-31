@extends('layouts.app')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white drop-shadow-md">👥 Data Pengguna</h2>
            <p class="text-sm text-gray-400 mt-1">Kelola akun siswa & pembimbing</p>
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
        <div class="glass-panel border-l-4 border-green-500 p-4 rounded-xl shadow-[0_0_15px_rgba(34,197,94,0.1)]">
            <div class="flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <p class="font-bold text-green-400">{!! session('success') !!}</p>
            </div>
        </div> 
    @endif

    <!-- 🔍 Search & Filter Bar -->
    <div class="glass-panel p-4 rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            
            <!-- Search Input -->
            <div class="flex-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau email..." 
                    class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors placeholder-gray-500"
                >
            </div>
            
            <!-- Role Filter -->
            <div class="sm:w-48">
                <select name="role" class="w-full bg-crypto-dark border border-white/20 text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-crypto-accent focus:border-crypto-accent transition-colors">
                    <option value="">-- Semua Role --</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>🎓 Siswa</option>
                    <option value="pembimbing" {{ request('role') == 'pembimbing' ? 'selected' : '' }}>👨‍🏫 Pembimbing</option>
                </select>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-crypto-accent hover:bg-purple-600 text-white px-5 py-2.5 rounded-lg shadow-[0_0_15px_rgba(112,0,255,0.3)] transition text-sm font-bold active:scale-95">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-white/10 border border-white/20 hover:bg-white/20 text-white px-5 py-2.5 rounded-lg shadow transition text-sm font-bold text-center">
                    🔄 Reset
                </a>
            </div>
        </form>
        
        <!-- Active Filters Info -->
        @if(request('search') || request('role'))
        <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
            <span>Filter aktif:</span>
            @if(request('search'))
                <span class="bg-crypto-accent/20 text-crypto-accent border border-crypto-accent/30 shadow-inner px-2 py-0.5 rounded-full font-bold">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('role'))
                <span class="bg-blue-500/20 text-blue-400 border border-blue-500/30 shadow-inner px-2 py-0.5 rounded-full font-bold">Role: {{ ucfirst(request('role')) }}</span>
            @endif
            <a href="{{ route('admin.users.index') }}" class="text-crypto-accent hover:text-white hover:underline ml-1 font-bold">Clear all</a>
        </div>
        @endif
    </div>

    <!-- Table -->
    <div class="glass-panel rounded-xl shadow-[0_0_15px_rgba(0,0,0,0.1)] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[650px]">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-300">Nama</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-300">Role</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $u)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3 font-bold text-white">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-inner border {{ $u->role === 'siswa' ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'bg-crypto-accent/20 text-crypto-accent border-crypto-accent/30' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-inner border {{ $u->is_active ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30' }}">
                                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-400 hover:text-white text-xs font-bold px-2 py-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/30 border border-blue-500/20 transition-colors shadow-inner">✏️ Edit</a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus user {{ $u->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-white text-xs font-bold px-2 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/30 border border-red-500/20 transition-colors shadow-inner">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-4xl opacity-50 drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">🔍</div>
                            <p class="text-gray-300 font-medium">Tidak ada data pengguna yang cocok dengan filter.</p>
                            @if(request('search') || request('role'))
                                <a href="{{ route('admin.users.index') }}" class="text-crypto-accent hover:text-white font-bold hover:underline text-sm mt-1 inline-block">Reset filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination with preserved filters -->
        <div class="px-4 py-3 border-t border-white/10 bg-white/5">
            {{ $users->appends(['search' => request('search'), 'role' => request('role')])->links() }}
        </div>
    </div>
</div>
@endsection