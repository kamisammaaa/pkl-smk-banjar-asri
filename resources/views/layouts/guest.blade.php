<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon Pack -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('android-chrome-192x192.png') }}?v={{ filemtime(public_path('android-chrome-192x192.png')) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('android-chrome-192x192.png') }}?v={{ filemtime(public_path('android-chrome-192x192.png')) }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('android-chrome-192x192.png') }}?v={{ filemtime(public_path('android-chrome-192x192.png')) }}">
        <link rel="shortcut icon" href="{{ asset('android-chrome-192x192.png') }}?v={{ filemtime(public_path('android-chrome-192x192.png')) }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#4f46e5">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-crypto-dark text-gray-200 min-h-screen relative selection:bg-crypto-success selection:text-crypto-dark">
        <!-- Background Animations -->
        <div class="bg-animation-container">
            <div class="bg-gradient-ambient"></div>
            <div class="bg-orb orb-1"></div>
            <div class="bg-orb orb-2"></div>
            <div class="bg-orb orb-3"></div>
        </div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="hover-lift relative z-10">
                <a href="/" class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-white shadow-[0_0_20px_rgba(255,255,255,0.1)] border border-white/10 p-2">
                    <img src="{{ asset('android-chrome-192x192.png') }}?v={{ filemtime(public_path('android-chrome-192x192.png')) }}" alt="Logo" class="w-full h-full object-contain">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 glass-panel shadow-[0_0_40px_rgba(112,0,255,0.15)] border border-white/10 overflow-hidden sm:rounded-3xl relative z-10 backdrop-blur-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
