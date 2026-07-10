@extends('layouts.app')
@section('content')
<div class="min-h-screen py-6 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-xl font-black text-slate-900">🔑 E-OTAG PKL</h1>
                <p class="text-sm text-slate-500 mt-1">Buat token kedatangan dan cek riwayat token Anda dengan tampilan yang lebih bersih.</p>
            </div>
            <span class="text-xs uppercase tracking-wide text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">Riwayat Terakhir</span>
        </div>

    <h2 class="text-2xl font-bold mb-4">🏷️ E-OTAG PKL</h2>
    @if(session('success')) <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-4 rounded mb-6 text-lg font-mono">{{ session('success') }}</div> @endif

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 mb-6">
        <p class="text-gray-600 mb-4">Generate token untuk verifikasi kedatangan di tempat industri.</p>
        <form action="{{ route('siswa.eotag.checkin') }}" method="POST">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-indigo-700 transition">🔑 Buat E-OTAG Sekarang</button>
        </form>
        <p class="text-xs text-gray-400 mt-4">Token akan tercatat otomatis dengan IP & waktu server.</p>
    </div>

    <h3 class="text-lg font-semibold mb-2 text-left">Riwayat Token</h3>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-x-auto text-left">
        <table class="w-full">
            <thead class="bg-gray-50"><tr><th class="p-3">Token</th><th class="p-3">Waktu</th><th class="p-3">Status</th><th class="p-3">IP</th></tr></thead>
            <tbody>
                @forelse($tags as $t)
                <tr class="border-t"><td class="p-3 font-mono">{{ $t->token }}</td><td class="p-3">{{ \Carbon\Carbon::parse($t->check_in_at)->format('d/m/Y H:i') }}</td><td class="p-3"><span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">{{ $t->status }}</span></td><td class="p-3">{{ $t->ip_address }}</td></tr>
                @empty
                <tr><td colspan="4" class="p-3 text-center text-gray-500">Belum ada token</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $tags->links() }}</div>
    </div>
</div>
@endsection