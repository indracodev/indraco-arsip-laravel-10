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
                        <td class="py-3.5 px-4">
                            @if($archive->status === 'draft')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700">Draft</span>
                            @elseif($archive->status === 'pending_verification')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">Antrean Verifikasi</span>
                            @elseif($archive->status === 'approved_booked')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30">Approved / Booking</span>
                            @elseif($archive->status === 'in_warehouse')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/30">Di Gudang</span>
                            @elseif($archive->status === 'borrowed')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30">Dipinjam</span>
                            @elseif($archive->status === 'destroyed')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-500/30">Dimusnahkan</span>
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
