<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PKL SMK Banjar Asri') }} - Admin</title>
    
    <!-- Favicon Pack -->
    <link rel="apple-touch-icon" sizes="180x180" href="/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/logo.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#4f46e5">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Admin CSS -->
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; }
        .sidebar { 
            width: 260px; 
            min-height: 100vh; 
            background: #1e293b; 
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 40;
            transition: transform 0.3s ease;
        }
        .sidebar-header { 
            padding: 1.5rem; 
            border-bottom: 1px solid #334155; 
        }
        .sidebar-menu { 
            padding: 1rem 0; 
        }
        .sidebar-menu a { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            padding: 0.75rem 1.5rem; 
            color: #cbd5e1; 
            text-decoration: none; 
            transition: 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active { 
            background: #334155; 
            color: white;
            border-left-color: #3b82f6;
        }
        .sidebar-menu .category-title {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 0.5rem;
        }
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header { 
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 1rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 30;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .main-content { 
            flex: 1; 
            background: #f8fafc; 
            padding: 2rem;
        }
        .mobile-toggle { 
            display: none; 
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #475569;
        }
        
        @media (max-width: 1024px) {
            .sidebar { 
                transform: translateX(-100%);
            }
            .sidebar.open { 
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .mobile-toggle { 
                display: block; 
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex">
        
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1 class="text-xl font-bold text-white">🎓 PKL Admin</h1>
                <p class="text-xs text-gray-400 mt-1">SMK Banjar Asri</p>
            </div>
            
            <nav class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span>📊</span> Dashboard
                </a>
                
                <div class="category-title">Master Data</div>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span>👥</span> Pengguna
                </a>
                <a href="{{ route('admin.jurusan.index') }}" class="{{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
                    <span>🎓</span> Jurusan
                </a>
                <a href="{{ route('admin.perusahaan.index') }}" class="{{ request()->routeIs('admin.perusahaan.*') ? 'active' : '' }}">
                    <span>🏢</span> Industri
                </a>
                <a href="{{ route('admin.siswa.index') }}" class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                    <span>🎒</span> Assign Siswa
                </a>
                
                <div class="category-title">Operasional</div>
                <a href="{{ route('admin.periode-pkl.index') }}" class="{{ request()->routeIs('admin.periode-pkl.*') ? 'active' : '' }}">
                    <span>📅</span> Periode PKL
                </a>
                <a href="{{ route('admin.pengumuman.index') }}" class="{{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
                    <span>📢</span> Pengumuman
                </a>
                <a href="{{ route('admin.rekap-absensi.index') }}" class="{{ request()->routeIs('admin.rekap-absensi.*') ? 'active' : '' }}">
                    <span>📈</span> Rekap Absensi
                </a>
                
                <div class="category-title">Monitoring</div>
                <a href="{{ route('admin.monitoring.kunjungan') }}" class="{{ request()->routeIs('admin.monitoring.kunjungan') ? 'active' : '' }}">
                    <span>👁️</span> Kunjungan
                </a>
                <a href="{{ route('admin.monitoring.verifikasi') }}" class="{{ request()->routeIs('admin.monitoring.verifikasi') ? 'active' : '' }}">
                    <span>✅</span> Verifikasi
                </a>
            </nav>
        </aside>
        
        <!-- MAIN WRAPPER -->
        <div class="main-wrapper">
            
            <!-- HEADER -->
            <header class="header">
                <div class="flex items-center gap-4">
                    <button class="mobile-toggle" onclick="toggleSidebar()">☰</button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Notif Bell -->
                    <button class="relative p-2 rounded-full hover:bg-gray-100 transition">
                        <span class="text-xl">🔔</span>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-full hover:bg-gray-100 transition">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50" style="display: none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="button" onclick="this.closest('form').submit()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <span>🚪</span> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- PAGE CONTENT -->
            <main class="main-content">
                @yield('content')
            </main>
            
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth < 1024 && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html>