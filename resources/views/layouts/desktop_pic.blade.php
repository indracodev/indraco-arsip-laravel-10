<!DOCTYPE html>
@php
    $configuredFontSize = config('app.font_size', env('APP_FONT_SIZE', '19px'));
    $fontSizeScale = match(strtolower($configuredFontSize)) {
        'small', 'sm' => '90%',
        'large', 'lg' => '110%',
        'xlarge', 'xl' => '120%',
        default => (str_contains($configuredFontSize, 'px') || str_contains($configuredFontSize, '%') || str_contains($configuredFontSize, 'rem')) ? $configuredFontSize : '19px',
    };
@endphp
<html lang="id" 
      x-data="{ theme: localStorage.getItem('theme') || 'dark' }" 
      :class="theme === 'dark' ? 'dark' : ''"
      style="font-size: {{ $fontSizeScale }};"
      class="h-full select-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DMS PT Indraco - Workstation Desktop Edition')</title>
    
    <!-- PWA Manifest & Theme -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d97706">
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
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
        
        /* Enterprise Desktop Custom Component Styles (Delphi/VB DBGrid Style) */
        main table {
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }
        main table th {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            border-right: 1px solid #cbd5e1;
            border-bottom: 2px solid #94a3b8;
            color: #1e293b;
            padding-top: 7px;
            padding-bottom: 7px;
        }
        .dark main table th {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-right: 1px solid #334155;
            border-bottom: 2px solid #475569;
            color: #f8fafc;
        }
        main table td {
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding-top: 7px;
            padding-bottom: 7px;
        }
        .dark main table td {
            border-right: 1px solid #1e293b;
            border-bottom: 1px solid #1e293b;
        }
        .desktop-window {
            border: 2px solid #334155;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 flex flex-col overflow-hidden font-sans text-xs">

    <!-- IMPERSONATION BANNER (If Active) -->
    @if(session()->has('impersonator_id'))
    @php
        $impersonator = \App\Models\User::find(session('impersonator_id'));
    @endphp
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 text-slate-950 px-4 py-2 shadow-md flex items-center justify-between z-50 text-xs font-bold border-b border-amber-600 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-1 bg-slate-950 text-amber-400 rounded shadow">
                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
            </span>
            <div>
                <span>Mode Impersonasi Aktif: Anda sedang login sebagai <strong class="underline font-black text-slate-950">{{ auth()->user()->name }}</strong> ({{ auth()->user()->role_label }} {{ auth()->user()->department ? '- ' . auth()->user()->department->code : '' }})</span>
                @if($impersonator)
                    <span class="opacity-80 block sm:inline text-[11px] sm:ml-2">| Akun Asli: <strong>{{ $impersonator->name }}</strong> (Super Admin)</span>
                @endif
            </div>
        </div>

        <form action="{{ route('impersonate.leave') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-3 py-1 bg-slate-950 hover:bg-slate-900 text-white rounded-lg text-xs font-black shadow transition flex items-center gap-1 shrink-0">
                <i data-lucide="log-out" class="w-3 h-3 text-amber-400"></i>
                Kembali ke SuperAdmin
            </button>
        </form>
    </div>
    @endif

    <!-- 1. TOP WINDOW TITLE BAR & DELPHI MAIN MENU -->
    <header class="bg-slate-950 text-white flex items-center justify-between px-3 py-1.5 border-b border-slate-800 shrink-0 shadow-sm">
        <div class="flex items-center gap-4">
            <!-- Brand & Desktop Logo -->
            <a href="{{ route('archives.index') }}" class="flex items-center gap-2 font-black tracking-tight text-white group">
                <div class="p-1 bg-amber-500 text-slate-950 rounded-lg font-extrabold text-xs shadow">
                    <i data-lucide="monitor" class="w-4 h-4"></i>
                </div>
                <span class="text-sm font-extrabold">
                    INDRACO DMS <span class="text-amber-400 text-xs font-mono font-bold">[Desktop Workstation]</span>
                </span>
            </a>

            <!-- Delphi Style Top Menu Dropdowns -->
            <nav class="hidden md:flex items-center gap-3 text-slate-300 text-xs font-medium border-l border-slate-800 pl-4">
                <a href="{{ route('archives.index') }}" class="hover:text-amber-400 transition {{ request()->routeIs('archives.index') ? 'text-amber-400 font-bold' : '' }}">Catalog</a>
                <a href="{{ route('borrowings.index') }}" class="hover:text-amber-400 transition {{ request()->routeIs('borrowings.*') ? 'text-amber-400 font-bold' : '' }}">Borrowings</a>
                <a href="{{ route('destructions.index') }}" class="hover:text-amber-400 transition {{ request()->routeIs('destructions.*') ? 'text-amber-400 font-bold' : '' }}">Retention</a>
                <a href="{{ route('logs.index') }}" class="hover:text-amber-400 transition {{ request()->routeIs('logs.*') ? 'text-amber-400 font-bold' : '' }}">Audit Logs</a>
            </nav>
        </div>

        <!-- Right User Info & Controls -->
        <div class="flex items-center gap-3">
            <!-- Light/Dark Mode Switcher -->
            <button 
                @click="theme = (theme === 'dark' ? 'light' : 'dark'); localStorage.setItem('theme', theme)" 
                type="button" 
                class="p-1.5 rounded-lg bg-slate-900 text-slate-400 hover:text-amber-400 border border-slate-800 transition"
                title="Ganti Tema"
            >
                <template x-if="theme === 'dark'">
                    <i data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                </template>
                <template x-if="theme !== 'dark'">
                    <i data-lucide="moon" class="w-4 h-4 text-slate-300"></i>
                </template>
            </button>

            @auth
            <div class="flex items-center gap-2 border-l border-slate-800 pl-3">
                <div class="text-right">
                    <span class="text-xs font-bold text-white block">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] text-amber-400 font-mono block">PIC DEPT: {{ auth()->user()->department->code ?? 'UMUM' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-900 rounded-lg transition" title="Logout">
                        <i data-lucide="power" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <!-- 2. DELPHI ACTION RIBBON TOOLBAR -->
    <div class="bg-white dark:bg-slate-950 border-b border-slate-300 dark:border-slate-800 px-3 py-1.5 flex flex-wrap items-center justify-between gap-2 shrink-0 shadow-xs">
        <div class="flex flex-wrap items-center gap-1.5">
            <!-- F2: Draft Baru -->
            <a href="{{ route('archives.create') }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                <span>Baru (F2)</span>
            </a>

            <!-- F8: Pinjam Dokumen -->
            <a href="{{ route('borrowings.create') }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="file-symlink" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400"></i>
                <span>Pinjam (F8)</span>
            </a>

            <!-- F9: Cetak Custom Label -->
            <a href="{{ route('archives.print_labels') }}" target="_blank" class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 rounded-lg text-amber-700 dark:text-amber-300 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                <span>Cetak Label (F9)</span>
            </a>

            <!-- F5: Refresh Data -->
            <button onclick="window.location.reload()" type="button" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400"></i>
                <span>Refresh (F5)</span>
            </button>
        </div>

        <div class="flex items-center gap-3 text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400">
            <span>DEPARTEMEN: <strong class="text-amber-600 dark:text-amber-400">{{ auth()->user()->department->name ?? 'Global' }}</strong></span>
            <span>WORKSTATION: WS-DESKTOP-01</span>
        </div>
    </div>

    <!-- 3. MDI TAB SHEET NAVIGATION MANAGER -->
    <div class="bg-slate-200 dark:bg-slate-900/90 px-2 pt-1.5 border-b border-slate-300 dark:border-slate-800 flex items-center gap-1 shrink-0 overflow-x-auto">
        <!-- Tab 1: Katalog & Booking Arsip -->
        <a href="{{ route('archives.index') }}" class="px-3.5 py-1.5 rounded-t-xl border-t border-x border-slate-300 dark:border-slate-700 font-bold text-xs transition flex items-center gap-1.5 shrink-0 {{ request()->routeIs('archives.index') ? 'bg-white dark:bg-slate-950 text-amber-600 dark:text-amber-400 border-b-white dark:border-b-slate-950 -mb-px shadow-2xs' : 'bg-slate-300 dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="folder-archive" class="w-3.5 h-3.5"></i>
            <span>[Form 1] Katalog Arsip {{ auth()->user()->department->code ?? '' }}</span>
        </a>

        <!-- Tab 2: Draft Pengajuan Storage Baru (If active or link) -->
        @if(request()->routeIs('archives.create'))
        <a href="{{ route('archives.create') }}" class="px-3.5 py-1.5 rounded-t-xl border-t border-x border-slate-300 dark:border-slate-700 font-bold text-xs bg-white dark:bg-slate-950 text-emerald-600 dark:text-emerald-400 border-b-white dark:border-b-slate-950 -mb-px shadow-2xs transition flex items-center gap-1.5 shrink-0">
            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
            <span>[Form 2] Draft Storage Baru</span>
        </a>
        @endif

        <!-- Tab 3: Peminjaman Dokumen -->
        <a href="{{ route('borrowings.index') }}" class="px-3.5 py-1.5 rounded-t-xl border-t border-x border-slate-300 dark:border-slate-700 font-bold text-xs transition flex items-center gap-1.5 shrink-0 {{ request()->routeIs('borrowings.*') ? 'bg-white dark:bg-slate-950 text-purple-600 dark:text-purple-400 border-b-white dark:border-b-slate-950 -mb-px shadow-2xs' : 'bg-slate-300 dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i>
            <span>[Form {{ request()->routeIs('archives.create') ? '3' : '2' }}] Peminjaman Berkas</span>
        </a>

        <!-- Tab 4: Expiry Retention -->
        <a href="{{ route('destructions.index') }}" class="px-3.5 py-1.5 rounded-t-xl border-t border-x border-slate-300 dark:border-slate-700 font-bold text-xs transition flex items-center gap-1.5 shrink-0 {{ request()->routeIs('destructions.*') ? 'bg-white dark:bg-slate-950 text-rose-600 dark:text-rose-400 border-b-white dark:border-b-slate-950 -mb-px shadow-2xs' : 'bg-slate-300 dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
            <span>[Form Status] Expiry Retention</span>
        </a>

        <!-- Tab 5: Audit Log -->
        <a href="{{ route('logs.index') }}" class="px-3.5 py-1.5 rounded-t-xl border-t border-x border-slate-300 dark:border-slate-700 font-bold text-xs transition flex items-center gap-1.5 shrink-0 {{ request()->routeIs('logs.*') ? 'bg-white dark:bg-slate-950 text-cyan-600 dark:text-cyan-400 border-b-white dark:border-b-slate-950 -mb-px shadow-2xs' : 'bg-slate-300 dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="history" class="w-3.5 h-3.5"></i>
            <span>Audit Trail</span>
        </a>
    </div>

    <!-- 4. MAIN VIEWPORT (MAIN CONTENT CONTAINER) -->
    <main class="flex-1 bg-slate-50 dark:bg-slate-900 p-3 sm:p-4 overflow-y-auto relative min-w-0">
        <!-- Flash Banners -->
        @if (session('success'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 flex items-start gap-2.5 text-xs shadow-sm">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"></i>
            <div class="font-bold">{{ session('success') }}</div>
        </div>
        @endif

        @if (session('warning'))
        <div class="mb-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-start gap-2.5 text-xs shadow-sm">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
            <div class="font-bold">{{ session('warning') }}</div>
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300 flex items-start gap-2.5 text-xs shadow-sm">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
            <div class="font-bold">{{ session('error') }}</div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- 5. WINDOWS BOTTOM STATUS BAR PANEL -->
    <footer class="bg-slate-900 text-slate-300 text-[11px] px-3 py-1 flex items-center justify-between border-t border-slate-800 shrink-0 font-mono">
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1.5 text-emerald-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> SYSTEM READY
            </span>
            <span class="text-slate-400">|</span>
            <span>USER: <strong class="text-white">{{ auth()->user()->name }}</strong> ({{ auth()->user()->department->code ?? 'DEPT' }})</span>
        </div>
        <div class="hidden sm:flex items-center gap-4 text-slate-400">
            <span>HOTKEYS: F2:Baru | F5:Refresh | F8:Pinjam | F9:Cetak Label | Ctrl+F:Cari</span>
            <span class="text-slate-400">|</span>
            <span>develope by Web Dev Indraco</span>
        </div>
    </footer>

    <!-- Lucide Icons & Desktop Hotkeys Engine Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();

            // Keyboard Shortcuts Handler
            document.addEventListener('keydown', function(e) {
                // F2: Buka Form Draft Baru
                if (e.key === 'F2') {
                    e.preventDefault();
                    window.location.href = "{{ route('archives.create') }}";
                }
                // F5: Refresh Halaman
                else if (e.key === 'F5') {
                    e.preventDefault();
                    window.location.reload();
                }
                // F8: Form Peminjaman Dokumen
                else if (e.key === 'F8') {
                    e.preventDefault();
                    window.location.href = "{{ route('borrowings.create') }}";
                }
                // F9: Cetak Custom Label
                else if (e.key === 'F9') {
                    e.preventDefault();
                    window.open("{{ route('archives.print_labels') }}", '_blank');
                }
                // Ctrl + F: Focus Search Input
                else if (e.ctrlKey && e.key.toLowerCase() === 'f') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[name="search"], input[type="text"][placeholder*="Cari"]');
                    if (searchInput) searchInput.focus();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
