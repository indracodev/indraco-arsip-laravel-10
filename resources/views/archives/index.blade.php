@extends('layouts.app')

@section('title', 'Katalog & Booking Arsip - DMS PT Indraco')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="folder-archive" class="w-7 h-7 text-amber-600 dark:text-amber-400"></i>
                Katalog Arsip & Pengajuan Storage
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Cari, ajukan booking gudang, dan kelola masa simpan dokumen fisik & digital PT Indraco.</p>
        </div>

        <a href="{{ route('archives.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-500/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Buat Draft Pengajuan Arsip
        </a>
    </div>

    <!-- Filter & Search Bar Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm" x-data="{ submitting: false }">
        <form action="{{ route('archives.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4" @submit="submitting = true">
            <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">

            <!-- Search Keyword -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 block">Cari Keyword</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Judul, No. Box, Isi Berkas..." 
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-amber-500 transition font-medium"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5"></i>
                </div>
            </div>

            <!-- Department Filter -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 block">Filter Departemen</label>
                <select name="department_id" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium">
                    <option value="">-- Semua Departemen --</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->code }} - {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 block">Filter Status</label>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium">
                    <option value="">-- Semua Status Workflow --</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft (Revisi)</option>
                    <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Antrean Verifikasi</option>
                    <option value="approved_booked" {{ request('status') == 'approved_booked' ? 'selected' : '' }}>Approved / Booked</option>
                    <option value="in_warehouse" {{ request('status') == 'in_warehouse' ? 'selected' : '' }}>Di Gudang</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="destroyed" {{ request('status') == 'destroyed' ? 'selected' : '' }}>Dimusnahkan</option>
                </select>
            </div>

            <!-- Expiry Alert Filter -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 block">Filter Expiry Masa Simpan</label>
                <select name="expiry_filter" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium">
                    <option value="">-- Semua Expiry --</option>
                    <option value="expiring_soon" {{ request('expiry_filter') == 'expiring_soon' ? 'selected' : '' }}>Mendekati Expiry (&le; 90 Hari)</option>
                    <option value="expired" {{ request('expiry_filter') == 'expired' ? 'selected' : '' }}>Sudah Kadaluarsa</option>
                </select>
            </div>

            <!-- Submit Filter Button -->
            <div class="space-y-1 flex items-end">
                <button type="submit" :disabled="submitting" class="w-full py-2 px-4 bg-slate-800 dark:bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 disabled:opacity-50 h-9 shadow-sm">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="submitting"></i>
                    <i data-lucide="filter" class="w-4 h-4" x-show="!submitting"></i>
                    <span x-text="submitting ? 'Memuat...' : 'Terapkan Filter'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Archives Catalog Grid -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 select-none">
                        @php
                            $curSort = request('sort', 'created_at');
                            $curDir = request('direction', 'desc');
                            $nextDir = $curDir === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <th class="py-3.5 px-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'box_number', 'direction' => $curSort === 'box_number' ? $nextDir : 'asc']) }}" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                                No. Box Arsip
                                @if($curSort === 'box_number')
                                    <i data-lucide="{{ $curDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5 text-amber-500"></i>
                                @else
                                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => $curSort === 'title' ? $nextDir : 'asc']) }}" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                                Judul Berkas & Dept
                                @if($curSort === 'title')
                                    <i data-lucide="{{ $curDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5 text-amber-500"></i>
                                @else
                                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">Periode Berkas</th>
                        <th class="py-3.5 px-4">Kondisi Fisik</th>
                        <th class="py-3.5 px-4">Lokasi Rak Gudang</th>
                        <th class="py-3.5 px-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'retention_expiry_date', 'direction' => $curSort === 'retention_expiry_date' ? $nextDir : 'asc']) }}" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                                Masa Simpan (Expiry)
                                @if($curSort === 'retention_expiry_date')
                                    <i data-lucide="{{ $curDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5 text-amber-500"></i>
                                @else
                                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $curSort === 'status' ? $nextDir : 'asc']) }}" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                                Status
                                @if($curSort === 'status')
                                    <i data-lucide="{{ $curDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5 text-amber-500"></i>
                                @else
                                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    @forelse($archives as $archive)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4 font-mono text-xs text-amber-600 dark:text-amber-400 font-extrabold">
                            @if($archive->box_number)
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="qr-code" class="w-4 h-4 text-amber-500"></i>
                                    {{ $archive->box_number }}
                                </div>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 italic">Belum Ada Box Code</span>
                            @endif
                        </td>

                        <td class="py-4 px-4">
                            <div class="space-y-1">
                                <a href="{{ route('archives.show', $archive) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition block">
                                    {{ $archive->title }}
                                </a>
                                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                        {{ $archive->department->code ?? 'GEN' }}
                                    </span>
                                    <span>by {{ $archive->creator->name ?? 'User' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ $archive->period_text ?? $archive->period_start_date->format('M Y') }}
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ $archive->physical_condition }}
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            @if($archive->location)
                                <div class="flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                                    <span class="font-bold">{{ $archive->location->full_location }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 italic">Belum Check-in</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-xs">
                            @if($archive->retention_expiry_date)
                                @php
                                    $isExpired = \Carbon\Carbon::parse($archive->retention_expiry_date)->isPast();
                                    $isNear = \Carbon\Carbon::parse($archive->retention_expiry_date)->diffInDays(now()) <= 90;
                                @endphp
                                <span class="{{ $isExpired ? 'text-rose-600 dark:text-rose-400 font-extrabold' : ($isNear ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-600 dark:text-slate-400 font-medium') }}">
                                    {{ \Carbon\Carbon::parse($archive->retention_expiry_date)->format('d M Y') }}
                                    ({{ $archive->retention_years }} Thn)
                                </span>
                            @else
                                <span class="text-slate-400 dark:text-slate-500">-</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($archive->status === 'draft')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700">Draft</span>
                            @elseif($archive->status === 'pending_verification')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">Antrean Verifikasi</span>
                            @elseif($archive->status === 'approved_booked')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30">Approved / Booked</span>
                            @elseif($archive->status === 'in_warehouse')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/30">Di Gudang</span>
                            @elseif($archive->status === 'borrowed')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30">Dipinjam</span>
                            @elseif($archive->status === 'destroyed')
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-500/30">Dimusnahkan</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right">
                            <a href="{{ route('archives.show', $archive) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-amber-600 dark:text-amber-400 hover:text-amber-700 text-xs font-bold transition inline-flex items-center gap-1">
                                Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500 dark:text-slate-500 space-y-2">
                            <i data-lucide="folder-search" class="w-12 h-12 mx-auto text-slate-400 dark:text-slate-600"></i>
                            <p class="text-sm font-semibold">Tidak ada berkas arsip yang ditemukan berdasarkan pencarian ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $archives->links() }}
        </div>
    </div>
</div>
@endsection
