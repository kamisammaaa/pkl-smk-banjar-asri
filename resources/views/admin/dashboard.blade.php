@extends('layouts.app')
@section('page-title', 'Dashboard Administrator')
@section('content')
<div class="space-y-6">
    <!-- Stats Grid (Responsive: 1 col mobile -> 4 col desktop) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-2xl">👥</div>
            <div><p class="text-sm text-gray-500">Total Siswa</p><p class="text-2xl font-bold text-gray-800">{{ $stats['total_siswa'] ?? 0 }}</p></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-2xl">🏢</div>
            <div><p class="text-sm text-gray-500">Industri Mitra</p><p class="text-2xl font-bold text-gray-800">{{ $stats['total_perusahaan'] ?? 0 }}</p></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-2xl">📅</div>
            <div><p class="text-sm text-gray-500">Absensi Hari Ini</p><p class="text-2xl font-bold text-gray-800">{{ $stats['absensi_hari_ini'] ?? 0 }}</p></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-2xl">👁️</div>
            <div><p class="text-sm text-gray-500">Kunjungan Bulan Ini</p><p class="text-2xl font-bold text-gray-800">{{ $stats['kunjungan_bulan_ini'] ?? 0 }}</p></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h3 class="text-lg font-semibold text-gray-800 pt-2">Aksi Cepat</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">👥</span><div><div class="font-medium text-gray-800">Kelola User</div><div class="text-xs text-gray-500">Tambah/Edit pengguna</div></div>
        </a>
        <a href="{{ route('admin.siswa.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">🎓</span><div><div class="font-medium text-gray-800">Assign Siswa</div><div class="text-xs text-gray-500">Hubungkan ke industri</div></div>
        </a>
        <a href="{{ route('admin.periode-pkl.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">📅</span><div><div class="font-medium text-gray-800">Periode PKL</div><div class="text-xs text-gray-500">Atur jadwal PKL</div></div>
        </a>
        <a href="{{ route('admin.pengumuman.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">📢</span><div><div class="font-medium text-gray-800">Pengumuman</div><div class="text-xs text-gray-500">Buat info terbaru</div></div>
        </a>
        <a href="{{ route('admin.rekap-absensi.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">📊</span><div><div class="font-medium text-gray-800">Rekap Absensi</div><div class="text-xs text-gray-500">Lihat statistik</div></div>
        </a>
        <a href="{{ route('admin.monitoring.verifikasi') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">✅</span><div><div class="font-medium text-gray-800">Verifikasi</div><div class="text-xs text-gray-500">Cek absensi diverifikasi</div></div>
        </a>
        <a href="{{ route('admin.absensi.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">📅</span><div><div class="font-medium text-gray-800">Kelola Absensi</div><div class="text-xs text-gray-500">Lihat & hapus absensi</div></div>
        </a>
        <a href="{{ route('admin.jurnal.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center gap-3 active:scale-95">
            <span class="text-2xl">📖</span><div><div class="font-medium text-gray-800">Jurnal Siswa</div><div class="text-xs text-gray-500">Lihat & hapus jurnal</div></div>
        </a>
    </div>
</div>
@endsection