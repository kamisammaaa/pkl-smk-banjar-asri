@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">🚧 {{ $title ?? 'Halaman dalam pengembangan' }}</h1>
    <p class="text-gray-600">Fitur ini akan tersedia di update berikutnya.</p>
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'pembimbing' ? route('pembimbing.dashboard') : route('siswa.dashboard')) }}" 
       class="mt-4 inline-block text-blue-600 hover:underline">← Kembali ke Dashboard</a>
</div>
@endsection