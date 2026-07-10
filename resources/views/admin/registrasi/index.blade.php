@extends('layouts.app')
@section('page-title', 'Approval Registrasi')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">⏳ Registrasi Menunggu Approval</h2>
    
    @if(session('success')) 
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg">{{ session('success') }}</div> 
    @endif
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">NIS</th>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold">Jurusan/Kelas</th>
                        <th class="px-4 py-3 font-semibold">Tanggal Daftar</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pendingSiswa as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->siswaProfile?->nis }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            {{ $user->siswaProfile?->jurusan?->nama ?? '-' }} / {{ $user->siswaProfile?->kelas ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('admin.registrasi.approve', $user) }}" method="POST" onsubmit="return confirm('Aktifkan akun {{ $user->name }}?')">
                                    @csrf
                                    <button class="bg-green-600 text-white px-3 py-1.5 rounded text-xs hover:bg-green-700 transition">✅ Approve</button>
                                </form>
                                <form action="{{ route('admin.registrasi.reject', $user) }}" method="POST" onsubmit="return confirm('Tolak registrasi {{ $user->name }}?')">
                                    @csrf
                                    <button class="bg-red-600 text-white px-3 py-1.5 rounded text-xs hover:bg-red-700 transition">❌ Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-3xl mb-2">🎉</div>
                            <p>Tidak ada registrasi menunggu approval</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t bg-gray-50">{{ $pendingSiswa->links() }}</div>
    </div>
</div>
@endsection
