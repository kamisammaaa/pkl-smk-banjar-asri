@extends('layouts.app')

@section('page-title', '✏️ Assign Siswa: ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white drop-shadow-md">✏️ Assign Siswa</h2>
        <p class="text-sm text-gray-400">{{ $user->name }} ({{ $user->email }})</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.siswa.update', $user) }}" method="POST" class="glass-panel p-6 rounded-lg shadow-md border border-white/5">
        @csrf
        @method('PUT')

        <div class="grid gap-6">
            
            {{-- NIS --}}
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-300 mb-1">
                    NIS <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nis" 
                    id="nis"
                    value="{{ old('nis', $profile?->nis) }}" 
                    required 
                    maxlength="20"
                    class="mt-1 block w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 p-2.5 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 @error('nis') border-red-500 @enderror"
                    placeholder="Masukkan NIS">
                @error('nis')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jurusan --}}
            <div>
                <label for="jurusan_id" class="block text-sm font-medium text-gray-300 mb-1">
                    Jurusan <span class="text-red-500">*</span>
                </label>
                <select 
                    name="jurusan_id" 
                    id="jurusan_id"
                    required 
                    class="mt-1 block w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 p-2.5 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 @error('jurusan_id') border-red-500 @enderror">
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusan as $j)
                        <option value="{{ $j->id }}" 
                                {{ old('jurusan_id', $profile?->jurusan_id) == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
                @error('jurusan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Perusahaan PKL --}}
            <div>
                <label for="perusahaan_id" class="block text-sm font-medium text-gray-300 mb-1">
                    Perusahaan PKL
                </label>
                <select 
                    name="perusahaan_id" 
                    id="perusahaan_id"
                    class="mt-1 block w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 p-2.5 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 @error('perusahaan_id') border-red-500 @enderror">
                    <option value="">-- Belum diassign --</option>
                    @foreach($perusahaan as $p)
                        <option value="{{ $p->id }}" 
                                {{ old('perusahaan_id', $profile?->perusahaan_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
                @error('perusahaan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Kosongkan jika siswa belum mendapat tempat PKL</p>
            </div>

            {{-- Pembimbing Sekolah --}}
            <div>
                <label for="pembimbing_id" class="block text-sm font-medium text-gray-300 mb-1">
                    Pembimbing Sekolah
                </label>
                <select 
                    name="pembimbing_id" 
                    id="pembimbing_id"
                    class="mt-1 block w-full border border-white/20 rounded-lg bg-crypto-dark text-white placeholder-gray-500 p-2.5 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 @error('pembimbing_id') border-red-500 @enderror">
                    <option value="">-- Belum diassign --</option>
                    @foreach($pembimbing as $pb)
                        <option value="{{ $pb->id }}" 
                                {{ old('pembimbing_id', $profile?->pembimbing_id) == $pb->id ? 'selected' : '' }}>
                            {{ $pb->name }}
                        </option>
                    @endforeach
                </select>
                @error('pembimbing_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Kosongkan jika belum ada pembimbing</p>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-white/5">
                <button type="submit" class="bg-crypto-accent hover:bg-purple-600 text-white shadow-[0_0_15px_rgba(112,0,255,0.3)] font-medium px-6 py-2.5 rounded-lg transition active:scale-95 shadow-sm">
                    💾 Simpan Assign
                </button>
                <a href="{{ route('admin.siswa.index') }}" class="bg-white/10 hover:bg-white/20 text-white font-medium px-6 py-2.5 rounded-lg transition text-center">
                    ❌ Batal
                </a>
            </div>
        </div>
    </form>

    {{-- Info Box --}}
    <div class="mt-6 glass-panel border-l-4 border-blue-500 p-4 rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.1)]">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-400">
                    <strong>💡 Tips:</strong> Setelah assign pembimbing, siswa akan muncul di dashboard pembimbing tersebut dan dapat mulai mengisi jurnal PKL.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(document.getElementById('perusahaan_id')) {
            new TomSelect("#perusahaan_id", {
                create: false,
                sortField: { field: "text", direction: "asc" }
            });
        }
        if(document.getElementById('pembimbing_id')) {
            new TomSelect("#pembimbing_id", {
                create: false,
                sortField: { field: "text", direction: "asc" }
            });
        }
    });
</script>
@endpush

@endsection