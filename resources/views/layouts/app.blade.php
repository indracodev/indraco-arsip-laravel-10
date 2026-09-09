<!DOCTYPE html>
@php
    $configuredFontSize = config('app.font_size', 'medium');
    $fontSizeScale = match(strtolower($configuredFontSize)) {
        'small', 'sm' => '90%',
        'large', 'lg' => '110%',
        'xlarge', 'xl' => '120%',
        default => (str_contains($configuredFontSize, 'px') || str_contains($configuredFontSize, '%') || str_contains($configuredFontSize, 'rem')) ? $configuredFontSize : '100%',
    };
@endphp
<html lang="id" 
      x-data="{ theme: localStorage.getItem('theme') || 'dark' }" 
      :class="theme === 'dark' ? 'dark' : ''"
      style="font-size: {{ $fontSizeScale }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DMS PT Indraco - Sistem Manajemen Gudang Arsip')</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        indraco: {
                            navy: '#0F172A',
                            gold: '#D4AF37',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .text-gold-gradient {
            background: linear-gradient(135deg, #F5E08B 0%, #D4AF37 50%, #9A7B1C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 selection:bg-amber-500 selection:text-slate-950" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex flex-col">
        <!-- Top Navbar Header -->
        <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-950/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm transition-colors duration-200">
            <div class="px-4 sm:px-6 lg:px-8 flex h-16 items-center justify-between">
                <!-- Mobile Menu Button & Brand -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" type="button" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="p-1.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl group-hover:border-amber-500/50 transition">
                            <img src="{{ asset('images/logo-indraco-est.png') }}" alt="PT Indraco Logo" class="h-8 w-auto object-contain">
                        </div>
                        <div>
                            <span class="text-lg font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                                DMS <span class="text-amber-600 dark:text-amber-400 font-black">PT INDRACO</span>
                            </span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block -mt-1 tracking-wider uppercase font-semibold">Archive & Document Storage</span>
                        </div>
                    </a>
                </div>

                <!-- Right Controls: Theme Switcher & User Profile -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Light / Dark Mode Toggle Switcher -->
                    <button 
                        @click="theme = (theme === 'dark' ? 'light' : 'dark'); localStorage.setItem('theme', theme)" 
                        type="button" 
                        class="p-2 rounded-xl text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-amber-400 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 transition" 
                        title="Ganti Tema (Light / Dark)"
                    >
                        <template x-if="theme === 'dark'">
                            <i data-lucide="sun" class="w-5 h-5 text-amber-400"></i>
                        </template>
                        <template x-if="theme !== 'dark'">
                            <i data-lucide="moon" class="w-5 h-5 text-slate-700"></i>
                        </template>
                    </button>

                    @auth
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            @if(auth()->user()->isSuperAdmin())
                                <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wide uppercase rounded-full bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30">Super Admin</span>
                            @elseif(auth()->user()->isPicGudang())
                                <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wide uppercase rounded-full bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">PIC Gudang Arsip</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wide uppercase rounded-full bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30">
                                    PIC Dept: {{ auth()->user()->department->code ?? 'Umum' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition" title="Keluar / Logout">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex flex-1">
            <!-- Sidebar Navigation -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 transform lg:translate-x-0 lg:static lg:inset-auto transition-transform duration-200 ease-in-out flex flex-col justify-between pt-16 lg:pt-0"
            >
                <div class="p-4 space-y-6 overflow-y-auto">
                    <!-- Section: Menu Utama -->
                    <div>
                        <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2">Navigasi Utama</span>
                        <nav class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition {{ request()->routeIs('dashboard') ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="layout-dashboard" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                                Dashboard Overview
                            </a>

                            <a href="{{ route('archives.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition {{ request()->routeIs('archives.*') ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="folder-archive" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                                Katalog & Booking Arsip
                            </a>

                            <a href="{{ route('borrowings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition {{ request()->routeIs('borrowings.*') ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="file-check-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                                Peminjaman Dokumen
                            </a>

                            <a href="{{ route('destructions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition {{ request()->routeIs('destructions.*') ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="shield-alert" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                                Retention & Pemusnahan
                            </a>

                            <a href="{{ route('logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition {{ request()->routeIs('logs.*') ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="history" class="w-5 h-5 text-cyan-600 dark:text-cyan-400"></i>
                                Log & Audit Trail
                            </a>
                        </nav>
                    </div>

                    <!-- Section: Management Master Data (Admin & PIC Gudang) -->
                    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isPicGudang()))
                    <div>
                        <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2">Master & Data Setting</span>
                        <nav class="space-y-1">
                            <a href="{{ route('master.departments') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('master.departments') ? 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="building-2" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                                Departemen Perusahaan
                            </a>
                            <a href="{{ route('master.warehouses') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('master.warehouses') ? 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="warehouse" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                                Master Gudang & Rak
                            </a>
                            <a href="{{ route('master.numbering') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('master.numbering') ? 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="binary" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                Custom Engine Format Box
                            </a>
                            <a href="{{ route('master.users') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('master.users') ? 'bg-slate-200 dark:bg-slate-800 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900' }}">
                                <i data-lucide="users" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                Kelola User & Hak Akses
                            </a>
                        </nav>
                    </div>
                    @endif
                </div>

                <!-- Footer Sidebar Info -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 text-center">
                    <div class="text-[11px] text-slate-500 font-medium">
                        &copy; 2026 PT Indraco System<br>
                        <span class="text-slate-400 dark:text-slate-600">DMS Version 1.0.0</span>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8 overflow-y-auto transition-colors duration-200">
                <div class="w-full max-w-[1700px] mx-auto space-y-6">
                    
                    <!-- Flash Alert Banners -->
                    @if (session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 flex items-start gap-3 shadow-sm">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5"></i>
                        <div class="text-sm font-semibold">{{ session('success') }}</div>
                    </div>
                    @endif

                    @if (session('warning'))
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-start gap-3 shadow-sm">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"></i>
                        <div class="text-sm font-semibold">{{ session('warning') }}</div>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300 flex items-start gap-3 shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5"></i>
                        <div class="text-sm font-semibold">{{ session('error') }}</div>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
