<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PKL SMK Banjar Asri') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('android-chrome-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-crypto-dark text-gray-200 antialiased min-h-screen flex items-center justify-center relative overflow-hidden font-sans selection:bg-crypto-success selection:text-crypto-dark">
    
    <!-- Background Animations -->
    <div class="bg-animation-container">
        <div class="bg-gradient-ambient"></div>
        <div class="bg-orb orb-1"></div>
        <div class="bg-orb orb-2"></div>
        <div class="bg-orb orb-3"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 w-full max-w-md px-6">
        <div class="glass-panel rounded-3xl p-8 sm:p-12 shadow-[0_0_40px_rgba(112,0,255,0.15)] border border-white/10 text-center animate-in fade-in slide-in-from-bottom-8 duration-700">
            
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-[0_0_20px_rgba(255,255,255,0.1)] transform hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('android-chrome-192x192.png') }}" alt="SMK Banjar Asri Logo" class="w-full h-full object-contain rounded-xl">
                </div>
            </div>

            <!-- Titles -->
            <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">
                Portal <span class="text-transparent bg-clip-text bg-gradient-to-r from-crypto-success to-blue-400">PKL</span>
            </h1>
            <p class="text-sm text-crypto-textMuted font-medium mb-8 uppercase tracking-widest">
                SMK Banjar Asri
            </p>

            <!-- Actions -->
            <div class="space-y-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="group relative flex w-full justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-crypto-accent hover:bg-crypto-accentHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crypto-accent focus:ring-offset-crypto-dark transition-all duration-300 shadow-[0_0_20px_rgba(112,0,255,0.3)] hover:shadow-[0_0_30px_rgba(112,0,255,0.5)] active:scale-95">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="group relative flex w-full justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-crypto-dark bg-crypto-success hover:bg-crypto-successHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crypto-success focus:ring-offset-crypto-dark transition-all duration-300 shadow-[0_0_20px_rgba(14,203,129,0.3)] hover:shadow-[0_0_30px_rgba(14,203,129,0.5)] active:scale-95">
                            Login
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="group relative flex w-full justify-center py-3.5 px-4 border border-white/10 text-sm font-bold rounded-xl text-gray-300 bg-white/5 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/20 focus:ring-offset-crypto-dark transition-all duration-300 active:scale-95">
                                Registrasi Siswa
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-white/5">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Sistem Informasi Praktik Kerja Lapangan.<br>
                    Dikembangkan untuk SMK Banjar Asri.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
