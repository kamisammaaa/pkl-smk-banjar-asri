<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Auth') - {{ config('app.name', 'PKL SMK Banjar Asri') }}</title>
    
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
        body { 
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(124, 58, 237, 0.12) 0px, transparent 50%);
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md my-8">
        <!-- Logo / Brand -->
        <div class="text-center mb-8 hover-lift">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md mb-3.5 shadow-lg border border-white/10 p-2">
                <img src="/logo.png" alt="Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-xl font-extrabold text-white tracking-tight">PKL SMK Banjar Asri</h1>
            <p class="text-slate-400 text-xs mt-1.5 font-medium tracking-wide">Sistem Informasi Praktek Kerja Lapangan</p>
        </div>

        <!-- Content Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl border border-white/20 overflow-hidden hover-lift">
            <div class="p-6 sm:p-8">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-slate-500 text-xs font-semibold">
            <p>&copy; {{ date('Y') }} SMK Banjar Asri. All rights reserved.</p>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>