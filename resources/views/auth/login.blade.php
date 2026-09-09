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
    <title>Login - DMS PT Indraco</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100 flex items-center justify-center p-4 bg-slate-100 dark:bg-slate-950 transition-colors duration-200">

    <!-- Theme Switcher Top Right -->
    <div class="fixed top-4 right-4 z-50">
        <button 
            @click="theme = (theme === 'dark' ? 'light' : 'dark'); localStorage.setItem('theme', theme)" 
            type="button" 
            class="p-2.5 rounded-2xl text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md hover:bg-slate-50 dark:hover:bg-slate-800 transition flex items-center gap-2 text-xs font-bold"
        >
            <template x-if="theme === 'dark'">
                <div class="flex items-center gap-1.5"><i data-lucide="sun" class="w-4 h-4 text-amber-400"></i> Mode Terang</div>
            </template>
            <template x-if="theme !== 'dark'">
                <div class="flex items-center gap-1.5"><i data-lucide="moon" class="w-4 h-4 text-slate-700"></i> Mode Gelap</div>
            </template>
        </button>
    </div>

    <div class="w-full max-w-md space-y-8">
        <!-- Logo & Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-amber-500/30 rounded-2xl shadow-xl mb-1">
                <img src="{{ asset('images/logo-indraco-est.png') }}" alt="PT Indraco Logo" class="h-12 w-auto object-contain">
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                DOCUMENT MANAGEMENT SYSTEM
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
                Sistem Pengelolaan & Gudang Arsip Digital PT Indraco
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md relative overflow-hidden">
            
            @if (session('info'))
            <div class="mb-6 p-3 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-800 dark:text-blue-300 text-xs text-center font-semibold">
                {{ session('info') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', 'admin@indraco.com') }}"
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700/80 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="nama@indraco.com"
                        >
                    </div>
                    @error('email')
                        <span class="text-rose-600 dark:text-rose-400 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">Kata Sandi / Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            value="password"
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700/80 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer font-medium">
                        <input type="checkbox" name="remember" class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-amber-500 focus:ring-0">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-lg shadow-amber-500/20 transition transform active:scale-[0.98]">
                    Masuk Ke Sistem DMS
                </button>
            </form>

            <!-- Demo Login Shortcuts -->
            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block text-center mb-3">Pintasan Login Cepat (Demo User)</span>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="fillLogin('admin@indraco.com')" type="button" class="px-2 py-2 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 rounded-lg text-purple-700 dark:text-purple-300 text-xs font-bold text-center transition">
                        Super Admin
                    </button>
                    <button onclick="fillLogin('gudang@indraco.com')" type="button" class="px-2 py-2 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 rounded-lg text-amber-700 dark:text-amber-300 text-xs font-bold text-center transition">
                        PIC Gudang
                    </button>
                    <button onclick="fillLogin('fin@indraco.com')" type="button" class="px-2 py-2 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-700 dark:text-blue-300 text-xs font-bold text-center transition">
                        PIC Keuangan
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 font-medium">
            &copy; 2026 PT Indraco. All rights reserved.
        </p>
    </div>

    <script>
        lucide.createIcons();
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
