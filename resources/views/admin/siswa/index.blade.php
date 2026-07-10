@extends('layouts.app')
@section('page-title', 'Assign Siswa PKL')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">🎓 Assign Siswa PKL</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola penempatan siswa: jurusan, perusahaan, pembimbing</p>
        </div>
        <div class="flex gap-2">
            {{-- 🔴 Tombol Import dihapus sementara karena route belum tersedia --}}
            {{-- <a href="{{ route('admin.siswa.import') }}" ...>📥 Import CSV</a> --}}
        </div>
    </div>

    @if(session('success')) 
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm">
            {!! session('success') !!}
        </div> 
    @endif

    <!-- 🔍 Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.siswa.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            
            <!-- Search Input -->
            <div class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-3 xl:col-span-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari nama, email, atau NIS..." 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                >
            </div>
            
            <!-- Filter: Jurusan -->
            <div>
                <select name="jurusan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">-- Semua Jurusan --</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter: Pembimbing Status -->
            <div>
                <select name="pembimbing_status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">-- Pembimbing: Semua --</option>
                    <option value="assigned" {{ request('pembimbing_status') == 'assigned' ? 'selected' : '' }}>✅ Sudah di-assign</option>
                    <option value="unassigned" {{ request('pembimbing_status') == 'unassigned' ? 'selected' : '' }}>❌ Belum di-assign</option>
                </select>
            </div>
            
            <!-- Filter: Perusahaan Status -->
            <div>
                <select name="perusahaan_status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">-- Perusahaan: Semua --</option>
                    <option value="assigned" {{ request('perusahaan_status') == 'assigned' ? 'selected' : '' }}>✅ Sudah di-assign</option>
                    <option value="unassigned" {{ request('perusahaan_status') == 'unassigned' ? 'selected' : '' }}>❌ Belum di-assign</option>
                </select>
            </div>
            
            <!-- Filter: Nama Perusahaan -->
            <div>
                <select name="perusahaan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    <option value="">-- Nama Perusahaan --</option>
                    @foreach($perusahaanList as $p)
                        <option value="{{ $p->id }}" {{ request('perusahaan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-2 w-full">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition text-sm font-medium active:scale-95 whitespace-nowrap text-center justify-center flex items-center">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.siswa.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg shadow transition text-sm font-medium text-center whitespace-nowrap justify-center flex items-center">
                    🔄 Reset
                </a>
            </div>
        </form>
        
        <!-- Active Filters Info -->
        @if(request('search') || request('jurusan_id') || request('pembimbing_status') || request('perusahaan_status') || request('perusahaan_id'))
        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
            <span>Filter aktif:</span>
            @if(request('search'))
                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('jurusan_id'))
                @php $jurusanName = $jurusanList->firstWhere('id', request('jurusan_id'))?->nama @endphp
                <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded">Jurusan: {{ $jurusanName }}</span>
            @endif
            @if(request('pembimbing_status'))
                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded">Pembimbing: {{ request('pembimbing_status') == 'assigned' ? 'Sudah' : 'Belum' }}</span>
            @endif
            @if(request('perusahaan_status'))
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Perusahaan: {{ request('perusahaan_status') == 'assigned' ? 'Sudah' : 'Belum' }}</span>
            @endif
            @if(request('perusahaan_id'))
                @php $perusahaanName = $perusahaanList->firstWhere('id', request('perusahaan_id'))?->nama @endphp
                <span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">Perusahaan: {{ $perusahaanName }}</span>
            @endif
            <a href="{{ route('admin.siswa.index') }}" class="text-blue-600 hover:underline ml-1">Clear all</a>
        </div>
        @endif
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[900px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Siswa</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">NIS</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Jurusan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Perusahaan</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Pembimbing</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($siswa as $s)
                    @php $profile = $s->siswaProfile; @endphp
                    <tr class="hover:bg-gray-50">
                        <!-- Nama & Email -->
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $s->name }}</div>
                            <div class="text-xs text-gray-500">{{ $s->email }}</div>
                        </td>
                        
                        <!-- NIS -->
                        <td class="px-4 py-3 text-gray-600">
                            {{ $profile?->nis ?? '-' }}
                        </td>
                        
                        <!-- Jurusan -->
                        <td class="px-4 py-3">
                            @if($profile?->jurusan)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $profile->jurusan->nama }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        
                        <!-- Perusahaan -->
                        <td class="px-4 py-3">
                            @if($profile?->perusahaan)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800" title="{{ $profile->perusahaan->nama }}">
                                    {{ Str::limit($profile->perusahaan->nama, 20) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Belum di-assign</span>
                            @endif
                        </td>
                        
                        <!-- Pembimbing -->
                        <td class="px-4 py-3">
                            @if($profile?->pembimbing)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800" title="{{ $profile->pembimbing->name }}">
                                    {{ Str::limit($profile->pembimbing->name, 15) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Belum di-assign</span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.siswa.edit', $s) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-1.5 rounded hover:bg-blue-50 transition">
                                ✏️ Assign
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="text-gray-400 mb-2 text-3xl">🔍</div>
                            <p class="text-gray-500 font-medium">Tidak ada data siswa yang cocok dengan filter.</p>
                            @if(request('search') || request('jurusan_id') || request('pembimbing_status') || request('perusahaan_status') || request('perusahaan_id'))
                                <a href="{{ route('admin.siswa.index') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Reset semua filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($siswa as $s)
        @php $profile = $s->siswaProfile; @endphp
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between space-y-4 hover:shadow transition-shadow">
            <!-- Student Header Info -->
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-base font-bold shadow-sm">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">{{ $s->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $s->email }}</p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">
                    NIS: {{ $profile?->nis ?? '-' }}
                </span>
            </div>
            
            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-3 text-xs border-t border-gray-100 pt-3">
                <div>
                    <span class="text-gray-400 block mb-1">Jurusan</span>
                    @if($profile?->jurusan)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $profile->jurusan->nama }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400 block mb-1">Perusahaan</span>
                    @if($profile?->perusahaan)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800" title="{{ $profile->perusahaan->nama }}">
                            {{ Str::limit($profile->perusahaan->nama, 20) }}
                        </span>
                    @else
                        <span class="text-gray-400 font-medium">Belum di-assign</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <span class="text-gray-400 block mb-1">Pembimbing</span>
                    @if($profile?->pembimbing)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800" title="{{ $profile->pembimbing->name }}">
                            {{ $profile->pembimbing->name }}
                        </span>
                    @else
                        <span class="text-gray-400 font-medium">Belum di-assign</span>
                    @endif
                </div>
            </div>
            
            <!-- Action Button -->
            <div class="border-t border-gray-100 pt-3 flex justify-end">
                <a href="{{ route('admin.siswa.edit', $s) }}" class="w-full text-center bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-800 text-xs font-semibold py-2 rounded-lg transition active:scale-[0.98]">
                    ✏️ Assign
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center">
            <div class="text-gray-400 mb-2 text-3xl">🔍</div>
            <p class="text-gray-500 font-medium text-sm">Tidak ada data siswa yang cocok dengan filter.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination with preserved filters -->
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
        {{ $siswa->appends([
            'search' => request('search'), 
            'jurusan_id' => request('jurusan_id'),
            'pembimbing_status' => request('pembimbing_status'),
            'perusahaan_status' => request('perusahaan_status'),
            'perusahaan_id' => request('perusahaan_id')
        ])->links() }}
    </div>
    
    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>💡 Tips:</strong> Gunakan filter "Belum di-assign" untuk cepat menemukan siswa yang perlu ditempatkan. Klik tombol <strong>Assign</strong> untuk mengatur jurusan, perusahaan, dan pembimbing.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection