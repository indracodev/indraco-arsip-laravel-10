<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DMS PT Indraco</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full font-sans antialiased text-slate-100 flex items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black">

    <div class="w-full max-w-md space-y-8">
        <!-- Logo & Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 bg-slate-900/80 border border-amber-500/30 rounded-2xl shadow-2xl backdrop-blur-xl mb-1">
                <img src="{{ asset('images/logo-indraco-est.png') }}" alt="PT Indraco Logo" class="h-12 w-auto object-contain">
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">
                DOCUMENT MANAGEMENT SYSTEM
            </h1>
            <p class="text-sm text-slate-400">
                Sistem Pengelolaan & Gudang Arsip Digital PT Indraco
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

            @if (session('info'))
            <div class="mb-6 p-3 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-300 text-xs text-center font-medium">
                {{ session('info') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', 'admin@indraco.com') }}"
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="nama@indraco.com"
                        >
                    </div>
                    @error('email')
                        <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Kata Sandi / Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            value="password"
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-0">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-400 text-slate-950 font-bold text-sm rounded-xl shadow-lg shadow-amber-500/20 transition transform active:scale-[0.98]">
                    Masuk Ke Sistem DMS
                </button>
            </form>

            <!-- Demo Login Shortcuts -->
            <div class="mt-8 pt-6 border-t border-slate-800/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block text-center mb-3">Pintasan Login Cepat (Demo User)</span>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="fillLogin('admin@indraco.com')" type="button" class="px-2 py-2 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 rounded-lg text-purple-300 text-xs font-medium text-center transition">
                        Super Admin
                    </button>
                    <button onclick="fillLogin('gudang@indraco.com')" type="button" class="px-2 py-2 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 rounded-lg text-amber-300 text-xs font-medium text-center transition">
                        PIC Gudang
                    </button>
                    <button onclick="fillLogin('fin@indraco.com')" type="button" class="px-2 py-2 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-300 text-xs font-medium text-center transition">
                        PIC Keuangan
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500">
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
