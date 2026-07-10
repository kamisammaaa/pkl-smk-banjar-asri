<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') - {{ config('app.name', 'PKL SMK Banjar Asri') }}</title>
    
    <!-- Favicon Pack -->
    <link rel="apple-touch-icon" sizes="180x180" href="/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/logo.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#4f46e5">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Prevent body scroll when sidebar open on mobile */
        body.sidebar-open { overflow: hidden; }
        
        /* Print styles */
        @media print {
            .sidebar, header, .mobile-toggle, button, .no-print, .mobile-nav-blur { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            body { background: white; }
            .main-content { padding: 1rem !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }" :class="{ 'sidebar-open': sidebarOpen }">
    <div class="flex min-h-screen">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

        <!-- Sidebar (Fixed width desktop, slide mobile) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-xl lg:shadow-none border-r border-slate-800">
            
            <!-- Logo Area -->
            <div class="h-16 flex items-center justify-start px-6 border-b border-slate-800 flex-shrink-0 gap-3">
                <img src="/logo.png" alt="Logo" class="w-8 h-8 rounded-lg shadow-md bg-white p-0.5">
                <div class="truncate">
                    <h1 class="text-sm font-bold tracking-wide text-white leading-tight">
                        @if(auth()->check())
                            @if(auth()->user()->role === 'admin') Admin Portal
                            @elseif(auth()->user()->role === 'pembimbing') Pembimbing
                            @else Siswa PKL @endif
                        @else SMK Banjar Asri @endif
                    </h1>
                    <span class="text-[10px] text-slate-400 font-medium tracking-wider">SMK BANJAR ASRI</span>
                </div>
            </div>

            <!-- Menu Scrollable -->
            <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-3">
                @auth
                @if(auth()->user()->role === 'admin')
                    <!-- ADMIN MENU -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    
                    <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Master Data</div>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Pengguna</span>
                    </a>
                    <a href="{{ route('admin.jurusan.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.jurusan.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                        <span>Jurusan</span>
                    </a>
                    <a href="{{ route('admin.perusahaan.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.perusahaan.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Industri</span>
                    </a>
                    <a href="{{ route('admin.siswa.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.siswa.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Assign Siswa</span>
                    </a>
                    
                    <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Operasional</div>
                    <a href="{{ route('admin.periode-pkl.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.periode-pkl.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Periode PKL</span>
                    </a>
                    <a href="{{ route('admin.pengumuman.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.pengumuman.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span>Pengumuman</span>
                    </a>
                    <a href="{{ route('admin.rekap-absensi.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.rekap-absensi.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Rekap Absensi</span>
                    </a>
                    <a href="{{ route('admin.registrasi') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.registrasi') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Approval Registrasi</span>
                    </a>
                    
                    <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Monitoring</div>
                    <a href="{{ route('admin.monitoring.kunjungan') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.monitoring.kunjungan') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Kunjungan</span>
                    </a>
                    <a href="{{ route('admin.monitoring.verifikasi') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.monitoring.verifikasi') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Verifikasi</span>
                    </a>
                    <a href="{{ route('admin.perusahaan-data') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.perusahaan-data') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Data Perusahaan</span>
                    </a>

                @elseif(auth()->user()->role === 'pembimbing')
                    <!-- PEMBIMBING MENU -->
                    <a href="{{ route('pembimbing.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('pembimbing.siswa-binaan') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.siswa-binaan') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Siswa Binaan</span>
                    </a>
                    <a href="{{ route('pembimbing.absensi') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.absensi') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Absensi Siswa</span>
                    </a>
                    <a href="{{ route('pembimbing.jurnal') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.jurnal') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Review Jurnal</span>
                    </a>
                    <a href="{{ route('pembimbing.kunjungan') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.kunjungan.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Input Kunjungan</span>
                    </a>
                    <a href="{{ route('pembimbing.nilai.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.nilai.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.969 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.18 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.908a1 1 0 00.95-.69l1.519-4.674z"></path></svg>
                        <span>Nilai Siswa</span>
                    </a>
                    <a href="{{ route('pembimbing.laporan') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('pembimbing.laporan') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Laporan</span>
                    </a>
                    
                @else
                    <!-- SISWA MENU -->
                    <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('siswa.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('siswa.absensi.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('siswa.absensi.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Absensi</span>
                    </a>
                    <a href="{{ route('siswa.jurnal.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('siswa.jurnal.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Jurnal Harian</span>
                    </a>
                    <a href="{{ route('siswa.perusahaan') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition {{ request()->routeIs('siswa.perusahaan') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Data Perusahaan</span>
                    </a>
                @endif
                @endauth
            </nav>

            <!-- Logout Button -->
            @auth
            <div class="p-4 border-t border-slate-800 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium text-slate-400 hover:text-white hover:bg-red-600/20 hover:text-red-400 rounded-xl transition interactive-btn">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Header (STRICT h-16 / 64px) -->
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 z-30 shadow-sm sticky top-0 flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-slate-600 hover:bg-slate-100 rounded-xl active:scale-95 transition" aria-label="Toggle sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h2 class="text-sm md:text-base font-semibold text-slate-700 truncate">@yield('page-title', 'Dashboard')</h2>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- User Dropdown (dengan null check) -->
                    <div class="relative" x-data="{ open: false }">
                        @auth
                            <button @click="open = !open" class="flex items-center gap-2.5 p-1.5 pr-3 rounded-full hover:bg-slate-100 transition active:scale-95 border border-slate-100" aria-label="User menu">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm ring-2 ring-indigo-100">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="hidden md:block text-xs font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-150 py-1.5 z-50 origin-top-right" 
                                 style="display: none;">
                                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="px-4 py-2.5 text-[10px] text-slate-500 font-medium">Role: <span class="font-bold text-indigo-600 uppercase">{{ Auth::user()->role }}</span></div>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="button" onclick="this.closest('form').submit()" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 font-bold flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Guest View -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition">Login</a>
                                <a href="{{ route('register') }}" class="text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 px-3.5 py-1.5 rounded-lg shadow-sm transition active:scale-95">Daftar</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Area -->
            <!-- pb-24 / padding-bottom 96px on mobile prevents content hiding behind floating bottom navigation bar -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 pb-24 lg:pb-6">
                @if(auth()->check())
                    @yield('content')
                @else
                    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
                        @yield('content')
                    </div>
                @endif
            </main>
        </div>
    </div>
    
    <!-- Floating Bottom Navigation Bar for Mobile View (<1024px) -->
    @auth
    <div class="lg:hidden fixed bottom-4 left-4 right-4 z-40">
        <nav class="mobile-nav-blur rounded-2xl flex items-center justify-around py-2.5 px-3 border border-white/30">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>User</span>
                </a>
                <a href="{{ route('admin.rekap-absensi.index') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('admin.rekap-absensi.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Rekap</span>
                </a>
            @elseif(auth()->user()->role === 'pembimbing')
                <a href="{{ route('pembimbing.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('pembimbing.dashboard') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('pembimbing.siswa-binaan') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('pembimbing.siswa-binaan') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('pembimbing.absensi') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('pembimbing.absensi') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Absen</span>
                </a>
                <a href="{{ route('pembimbing.jurnal') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('pembimbing.jurnal') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Jurnal</span>
                </a>
            @else
                <a href="{{ route('siswa.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('siswa.dashboard') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('siswa.absensi.index') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('siswa.absensi.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Absen</span>
                </a>
                <a href="{{ route('siswa.jurnal.index') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('siswa.jurnal.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Jurnal</span>
                </a>
                <a href="{{ route('siswa.perusahaan') }}" class="flex flex-col items-center gap-0.5 text-[9px] font-bold transition {{ request()->routeIs('siswa.perusahaan') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Mitra</span>
                </a>
            @endif
            
            <!-- Hamburger Menu for Mobile Drawer -->
            <button @click="sidebarOpen = !sidebarOpen" class="flex flex-col items-center gap-0.5 text-[9px] font-bold text-slate-400 hover:text-slate-700 focus:outline-none transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <span>Menu</span>
            </button>
        </nav>
    </div>
    @endauth

    <!-- Scripts -->
    <script>
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('aside');
            const toggle = document.querySelector('[aria-label="Toggle sidebar"]');
            const body = document.body;
            if (window.innerWidth < 1024 && body.classList.contains('sidebar-open') && sidebar && !sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
                body.classList.remove('sidebar-open');
            }
        });
    </script>
    
    <!-- Stack for page-specific scripts -->
    @stack('scripts')
</body>
</html>