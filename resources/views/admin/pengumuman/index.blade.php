@extends('layouts.app')
@section('page-title', 'Manajemen Pengumuman')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">📢 Manajemen Pengumuman</h2>
            <p class="text-sm text-gray-500 mt-1">Buat, edit, dan bagikan pengumuman untuk siswa atau pembimbing</p>
        </div>
        <div>
            <a href="{{ route('admin.pengumuman.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2 font-medium text-sm active:scale-95">
                + Buat Pengumuman
            </a>
        </div>
    </div>
    
    @if(session('success')) 
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded mb-4 shadow-sm">
            {{ session('success') }}
        </div> 
    @endif
    
    <div class="space-y-4">
        @foreach($pengumuman as $p)
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 border-l-4 {{ $p->is_active ? 'border-l-green-500' : 'border-l-gray-300' }}">
            <div class="flex justify-between items-start gap-4 mb-3">
                <h3 class="font-bold text-lg text-gray-800 flex-1">{{ $p->judul }}</h3>
                <span class="text-xs px-3 py-1 rounded-full font-medium whitespace-nowrap {{ $p->target == 'semua' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ ucfirst($p->target) }}
                </span>
            </div>
            
            {{-- Isi Pengumuman dengan Word Break --}}
            <div class="text-gray-600 text-sm leading-relaxed mb-3 break-words overflow-wrap-anywhere" 
                 style="word-break: break-word; overflow-wrap: break-word;">
                {!! nl2br(e($p->isi)) !!}
            </div>
            
            {{-- Meta Info --}}
            <div class="mt-4 pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-600">Oleh: {{ $p->admin->name }}</span>
                    <span>•</span>
                    <span>{{ $p->published_at?->format('d/m/Y H:i') ?? '-' }}</span>
                    @if(!$p->is_active)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-medium">Nonaktif</span>
                    @endif
                </div>
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.pengumuman.edit', $p) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium hover:underline transition">
                        ✏️ Edit
                    </a>
                    <form action="{{ route('admin.pengumuman.destroy', $p) }}" method="POST" class="inline">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-800 font-medium hover:underline transition"
                                onclick="return confirm('Hapus pengumuman ini?')">
                            🗑️ Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        
        @if(count($pengumuman) === 0)
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center">
                <div class="text-5xl mb-3">📭</div>
                <p class="text-gray-500 font-medium">Belum ada pengumuman</p>
                <a href="{{ route('admin.pengumuman.create') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                    Buat pengumuman pertama
                </a>
            </div>
        @endif
    </div>
</div>

{{-- CSS untuk Handle Text Overflow --}}
<style>
.break-words {
    word-break: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

.overflow-wrap-anywhere {
    overflow-wrap: anywhere;
}

/* Khusus untuk URL yang sangat panjang */
.break-words a,
.break-words {
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}
</style>
@endsection