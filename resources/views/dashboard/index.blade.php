@extends('layouts.app')

@section('title', 'Dashboard Overview - DMS PT Indraco')

@section('content')
<div class="space-y-8">
    <!-- Top Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 border border-slate-700 dark:border-slate-800 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-3">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    Sistem Manajemen & Gudang Arsip Digital
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Selamat datang kembali, <span class="text-amber-400">{{ $user->name }}</span>
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl font-medium">
                    @if($user->isSuperAdmin())
                        Anda memiliki hak akses penuh untuk mengelola master gudang, format penomoran, dan seluruh dokumen departemen PT Indraco.
                    @elseif($user->isPicGudang())
                        Dashboard Kurator Gudang: Pantau antrean pengajuan arsip masuk, verifikasi berkas, dan aktivitas peminjaman.
                    @else
                        Dashboard Departemen {{ $user->department->name ?? '' }}: Kelola draft berkas arsip, pengajuan booking slot gudang, dan peminjaman.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('archives.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-500/20 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Buat Draft Arsip Baru
                </a>
            </div>
        </div>

        <!-- Quick Document Search Bar (Interactive Live Auto-complete & Suggestions) -->
        <div class="mt-6 pt-6 border-t border-slate-700/80 dark:border-slate-800"
             x-data="{
                searchQuery: '',
                results: [],
                keywords: [],
                recentDocs: [],
                isSuggestion: true,
                loading: false,
                showDropdown: false,
                debounceTimer: null,
                fetchResults() {
                    this.loading = true;
                    this.showDropdown = true;
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        fetch(`/api/search-archives?q=${encodeURIComponent(this.searchQuery)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.type === 'suggestions') {
                                    this.isSuggestion = true;
                                    this.keywords = data.keywords || [];
                                    this.recentDocs = data.recent || [];
                                    this.results = [];
                                } else {
                                    this.isSuggestion = false;
                                    this.results = data.items || [];
                                }
                                this.loading = false;
                                this.$nextTick(() => lucide.createIcons());
                            })
                            .catch(() => {
                                this.results = [];
                                this.loading = false;
                            });
                    }, 150);
                },
                selectKeyword(kw) {
                    this.searchQuery = kw;
                    this.fetchResults();
                }
             }" 
             @click.outside="showDropdown = false"
             class="relative z-30">

            <div class="flex items-center justify-between mb-2 text-xs font-bold text-slate-300">
                <span class="flex items-center gap-1.5 text-amber-400 uppercase tracking-wider">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                    Pencarian Cepat Dokumen & Arsip
                </span>
                <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Klik kolom cari untuk melihat saran dokumen & kata kunci populer</span>
            </div>

            <form action="{{ route('archives.index') }}" method="GET" class="relative">
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        x-model="searchQuery" 
                        @input="fetchResults()"
                        @focus="fetchResults(); showDropdown = true"
                        placeholder="Ketik kata kunci dokumen (contoh: BOX-FIN-2024, Pajak, Laporan, HRD)..." 
                        class="w-full pl-12 pr-32 py-3 bg-slate-950/70 dark:bg-slate-900/90 border border-slate-700 dark:border-slate-700/80 rounded-2xl text-sm font-semibold text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-inner transition"
                    >
                    <div class="absolute inset-y-0 right-2 flex items-center gap-1.5">
                        <template x-if="searchQuery">
                            <button type="button" @click="searchQuery = ''; fetchResults()" class="p-1.5 text-slate-400 hover:text-white rounded-lg transition" title="Clear">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </template>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            <span>Cari</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Filter Badges -->
            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-slate-400 text-[11px] font-semibold">Shortcut Status:</span>
                <a href="{{ route('archives.index', ['status' => 'in_warehouse']) }}" class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 border border-emerald-500/30 transition text-[11px] font-bold inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Di Gudang
                </a>
                <a href="{{ route('archives.index', ['status' => 'pending_verification']) }}" class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30 transition text-[11px] font-bold inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Antrean Verifikasi
                </a>
                <a href="{{ route('archives.index', ['status' => 'borrowed']) }}" class="px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 border border-purple-500/30 transition text-[11px] font-bold inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span> Sedang Dipinjam
                </a>
                <a href="{{ route('archives.index', ['expiry_filter' => 'expiring_soon']) }}" class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/30 transition text-[11px] font-bold inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Expiring Soon
                </a>
            </div>

            <!-- Live Results & Suggestions Dropdown Card -->
            <div x-show="showDropdown" x-cloak x-transition.opacity.duration.200ms class="absolute left-0 right-0 mt-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden z-50 text-slate-900 dark:text-slate-100">
                
                <!-- Loading State -->
                <div x-show="loading" class="p-5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat saran & hasil pencarian...
                </div>

                <!-- Mode 1: Initial Suggestions (When Search Query is Empty) -->
                <div x-show="!loading && isSuggestion" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    <!-- Suggested Keyword Chips Section -->
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-900/60">
                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2.5">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            Saran Kata Kunci Pencarian Dokumen
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="kw in keywords" :key="kw">
                                <button type="button" @click="selectKeyword(kw)" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-amber-500 dark:hover:border-amber-400 text-xs font-bold text-slate-800 dark:text-slate-200 hover:text-amber-600 dark:hover:text-amber-400 transition shadow-xs flex items-center gap-1.5 group">
                                    <i data-lucide="search" class="w-3 h-3 text-slate-400 group-hover:text-amber-500"></i>
                                    <span x-text="kw"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Recommended / Recent Documents List -->
                    <div class="max-h-80 overflow-y-auto">
                        <div class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                Rekomendasi Dokumen Terbaru
                            </span>
                            <span>Akses Cepat</span>
                        </div>
                        <template x-for="item in recentDocs" :key="item.id">
                            <a :href="item.url" class="p-3.5 flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-900/80 transition group">
                                <div class="space-y-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700" x-text="item.dept_code"></span>
                                        <span class="font-mono text-xs font-bold text-amber-600 dark:text-amber-400" x-text="item.box_number"></span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-amber-500 transition" x-text="item.title"></h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i>
                                        <span x-text="item.location"></span>
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-3">
                                    <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold" 
                                          :class="{
                                              'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700': item.status === 'draft',
                                              'bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30': item.status === 'pending_verification',
                                              'bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30': item.status === 'approved_booked',
                                              'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/30': item.status === 'in_warehouse',
                                              'bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30': item.status === 'borrowed',
                                              'bg-rose-500/10 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-500/30': item.status === 'destroyed'
                                          }" 
                                          x-text="item.status_label">
                                    </span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-amber-500 transition"></i>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <!-- Mode 2: Live Query Search Results -->
                <div x-show="!loading && !isSuggestion">
                    <div x-show="results.length === 0" class="p-6 text-center text-xs text-slate-500 dark:text-slate-400 space-y-1">
                        <i data-lucide="file-search" class="w-8 h-8 mx-auto text-slate-400 dark:text-slate-600 mb-2"></i>
                        <p class="font-bold text-slate-700 dark:text-slate-300">Tidak ada dokumen ditemukan untuk kata kunci ini.</p>
                        <p>Tekan tombol Enter atau tombol "Cari" untuk mencari lebih detail di Katalog Utama.</p>
                    </div>

                    <div x-show="results.length > 0" class="divide-y divide-slate-100 dark:divide-slate-800/80 max-h-96 overflow-y-auto">
                        <div class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Hasil Pencarian Cepat</span>
                            <span x-text="results.length + ' Dokumen Ditemukan'"></span>
                        </div>
                        <template x-for="item in results" :key="item.id">
                            <a :href="item.url" class="p-3.5 flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-900/80 transition group">
                                <div class="space-y-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700" x-text="item.dept_code"></span>
                                        <span class="font-mono text-xs font-bold text-amber-600 dark:text-amber-400" x-text="item.box_number"></span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-amber-500 transition" x-text="item.title"></h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i>
                                        <span x-text="item.location"></span>
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center gap-3">
                                    <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold" 
                                          :class="{
                                              'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700': item.status === 'draft',
                                              'bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30': item.status === 'pending_verification',
                                              'bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30': item.status === 'approved_booked',
                                              'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/30': item.status === 'in_warehouse',
                                              'bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30': item.status === 'borrowed',
                                              'bg-rose-500/10 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-500/30': item.status === 'destroyed'
                                          }" 
                                          x-text="item.status_label">
                                    </span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-amber-500 transition"></i>
                                </div>
                            </a>
                        </template>

                        <a :href="'{{ route('archives.index') }}?search=' + encodeURIComponent(searchQuery)" class="block p-3 text-center bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-amber-600 dark:text-amber-400 transition">
                            Buka Hasil Selengkapnya di Katalog Utama &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Active Archives -->
        <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Arsip Active</span>
                <div class="p-2 bg-blue-500/10 rounded-xl text-blue-600 dark:text-blue-400">
                    <i data-lucide="archive" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalArchives }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Box/Berkas</span>
            </div>
        </div>

        <!-- In Warehouse -->
        <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tersimpan Gudang</span>
                <div class="p-2 bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $inWarehouseCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Slot Rak</span>
            </div>
        </div>

        <!-- Pending Verification Queue -->
        <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Antrean Booking</span>
                <div class="p-2 bg-amber-500/10 rounded-xl text-amber-600 dark:text-amber-400">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $pendingVerificationCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengajuan</span>
            </div>
        </div>

        <!-- Borrowed -->
        <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sedang Dipinjam</span>
                <div class="p-2 bg-purple-500/10 rounded-xl text-purple-600 dark:text-purple-400">
                    <i data-lucide="file-symlink" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-purple-600 dark:text-purple-400">{{ $borrowedCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Berkas Out</span>
            </div>
        </div>

        <!-- Retention Expiry Alert -->
        <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alert Pemusnahan</span>
                <div class="p-2 bg-rose-500/10 rounded-xl text-rose-600 dark:text-rose-400">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-rose-600 dark:text-rose-400">{{ $expiringCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Dokumen</span>
            </div>
        </div>
    </div>

    <!-- Middle Grid: Warehouse Capacity & Retention Expiry Warning List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Warehouse Capacity Meter -->
        <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="boxes" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                        Kapasitas Gudang Arsip
                    </h2>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Real-time Meter</span>
                </div>

                <div class="space-y-4">
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Total Box Terisi / Kapasitas Slot</span>
                            <span class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $usedCapacity }} / {{ $totalCapacity }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium"> Box</span>
                        </div>
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $capacityPercent }}%</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-3 bg-slate-200 dark:bg-slate-900 rounded-full overflow-hidden p-0.5 border border-slate-300 dark:border-slate-800">
                        <div class="h-full bg-gradient-to-r from-emerald-500 via-amber-400 to-rose-500 rounded-full transition-all duration-500" style="width: {{ min($capacityPercent, 100) }}%"></div>
                    </div>
                </div>

                <!-- Locations Breakdown List -->
                <div class="mt-6 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">Daftar Lokasi Rak Available</span>
                    @foreach($warehouseLocations->take(4) as $loc)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 text-xs">
                        <div class="flex items-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $loc->full_location }}</span>
                        </div>
                        <span class="text-slate-600 dark:text-slate-400 font-bold">{{ $loc->current_box_count }}/{{ $loc->box_capacity }} Box</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 text-right">
                <a href="{{ route('master.warehouses') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1">
                    Kelola Lokasi Gudang <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <!-- Expiry Warning Alert List -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="hourglass" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                        Pemberitahuan Retention Expiry (Masa Simpan)
                    </h2>
                    <a href="{{ route('destructions.index') }}" class="text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline">Lihat Semua</a>
                </div>

                @if($expiringArchives->isEmpty())
                <div class="text-center py-10 text-slate-500 dark:text-slate-500 space-y-2">
                    <i data-lucide="shield-check" class="w-10 h-10 mx-auto text-slate-400 dark:text-slate-600"></i>
                    <p class="text-sm font-semibold">Tidak ada berkas yang mendekati masa pemusnahan dalam 90 hari ke depan.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($expiringArchives->take(4) as $exp)
                    <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-300">{{ $exp->department->code ?? 'GEN' }}</span>
                                <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $exp->title }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400">No. Box: <span class="font-mono text-amber-600 dark:text-amber-400 font-bold">{{ $exp->box_number ?? 'Belum ada' }}</span> | Tgl Expiry: <span class="text-rose-700 dark:text-rose-300 font-extrabold">{{ \Carbon\Carbon::parse($exp->retention_expiry_date)->format('d M Y') }}</span></p>
                        </div>

                        <a href="{{ route('archives.show', $exp) }}" class="px-3 py-1.5 rounded-xl bg-rose-600 text-white dark:bg-rose-500/20 dark:hover:bg-rose-500/30 dark:text-rose-300 text-xs font-bold transition border border-rose-500/30">
                            Proses Pemusnahan
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 dark:text-amber-400 inline mr-1"></i>
                Dokumen dengan status "Mendekati Pemusnahan" dapat diajukan Berita Acara Pemusnahan (BAP) oleh Kurator Gudang.
            </div>
        </div>

    </div>

    <!-- Bottom Table: Recent Archives Catalog -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="folder-git-2" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    Berkas Arsip Terbaru
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Daftar arsip yang baru dimasukkan atau diproses dalam sistem</p>
            </div>
            <a href="{{ route('archives.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-300 transition">
                Buka Katalog Lengkap
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3 px-4">No. Box Arsip</th>
                        <th class="py-3 px-4">Judul Berkas</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Lokasi Fisik</th>
                        <th class="py-3 px-4">Status Workflow</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    @forelse($recentArchives as $archive)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-3.5 px-4 font-mono text-xs text-amber-600 dark:text-amber-400 font-bold">
                            {{ $archive->box_number ?? 'Penomoran Pending' }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                            {{ Str::limit($archive->title, 40) }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-1 rounded-md text-xs font-bold bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $archive->department->code ?? 'GEN' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-400 font-medium">
                            {{ $archive->period_text ?? $archive->period_start_date->format('M Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ $archive->location->full_location ?? 'Belum Ditentukan' }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($archive->status === 'draft')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700">Draft</span>
                            @elseif($archive->status === 'pending_verification')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">Antrean Verifikasi</span>
                            @elseif($archive->status === 'approved_booked')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30">Approved / Booking</span>
                            @elseif($archive->status === 'in_warehouse')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/30">Di Gudang</span>
                            @elseif($archive->status === 'borrowed')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30">Dipinjam</span>
                            @elseif($archive->status === 'destroyed')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-500/30">Dimusnahkan</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('archives.show', $archive) }}" class="p-2 text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-xl transition inline-block">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">Belum ada data arsip tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
