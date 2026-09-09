@extends('layouts.app')

@section('title', 'Peminjaman Dokumen Arsip - DMS PT Indraco')

@section('content')
<div class="space-y-6" x-data="{ submitting: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="file-check-2" class="w-7 h-7 text-emerald-600 dark:text-emerald-400"></i>
                Manajemen Peminjaman Dokumen Arsip
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Pengajuan pinjam, persetujuan kurator gudang, pengeluaran berkas fisik, dan tracking pengembalian.</p>
        </div>

        <a href="{{ route('borrowings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Pengajuan Pinjam Dokumen
        </a>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
        <form action="{{ route('borrowings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4" @submit="submitting = true">
            <input type="hidden" name="sort" value="{{ request('sort', 'borrowed_at') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">

            <!-- Search Keyword -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 block">Cari Keyword</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="No. Box, Judul, Peminjam, Tujuan..." 
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition font-medium"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5"></i>
                </div>
            </div>

            <!-- Filter Status -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 block">Filter Status</label>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition font-medium">
                    <option value="">-- Semua Status --</option>
                    <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Diajukan</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>

            <!-- Submit Filter Button -->
            <div class="space-y-1 flex items-end">
                <button type="submit" :disabled="submitting" class="w-full py-2 px-4 bg-slate-800 dark:bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 disabled:opacity-50 h-9 shadow-sm">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="submitting"></i>
                    <i data-lucide="filter" class="w-4 h-4" x-show="!submitting"></i>
                    <span x-text="submitting ? 'Memuat...' : 'Cari Data'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 select-none">
                        @php
                            $curSort = request('sort', 'borrowed_at');
                            $curDir = request('direction', 'desc');
                            $nextDir = $curDir === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <th class="py-3.5 px-4">No. Box & Judul Berkas</th>
                        <th class="py-3.5 px-4">Peminjam</th>
                        <th class="py-3.5 px-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'borrowed_at', 'direction' => $curSort === 'borrowed_at' ? $nextDir : 'asc']) }}" class="flex items-center gap-1.5 hover:text-emerald-500 transition">
                                Tgl Pinjam / Est. Kembali
                                @if($curSort === 'borrowed_at')
                                    <i data-lucide="{{ $curDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5 text-emerald-500"></i>
                                @else
                                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">Tujuan Peminjaman</th>
                        <th class="py-3.5 px-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $curSort === 'status' ? $nextDir : 'asc']) }}" class="flex items-center gap-1.5 hover:text-emerald-500 transition">
                                Status
                                @if($curSort === 'status')
                                    <i data-lucide="{{ $curDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5 text-emerald-500"></i>
                                @else
                                    <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4 text-right">Aksi Gudang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    @forelse($borrowings as $bLog)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4">
                            <div class="space-y-1">
                                <span class="font-mono text-xs text-amber-600 dark:text-amber-400 font-extrabold block">{{ $bLog->archive->box_number }}</span>
                                <a href="{{ route('archives.show', $bLog->archive) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition">
                                    {{ $bLog->archive->title }}
                                </a>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-xs font-medium">
                            <span class="font-bold text-slate-900 dark:text-slate-200 block">{{ $bLog->borrower->name }}</span>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold">{{ $bLog->archive->department->code ?? 'Dept' }}</span>
                        </td>

                        <td class="py-4 px-4 text-xs space-y-1 font-medium">
                            <div class="text-slate-700 dark:text-slate-300">Tgl Pinjam: {{ $bLog->borrow_date ? $bLog->borrow_date->format('d M Y') : 'Menunggu Dispatch' }}</div>
                            <div class="text-amber-600 dark:text-amber-400 font-extrabold">Est. Kembali: {{ \Carbon\Carbon::parse($bLog->expected_return_date)->format('d M Y') }}</div>
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-300 max-w-xs truncate font-medium">
                            {{ $bLog->purpose }}
                        </td>

                        <td class="py-4 px-4">
                            @if($bLog->status === 'requested')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">Diajukan</span>
                            @elseif($bLog->status === 'approved')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30">Disetujui</span>
                            @elseif($bLog->status === 'dispatched')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30">Sedang Dipinjam</span>
                            @elseif($bLog->status === 'returned')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/30">Dikembalikan</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right">
                            @if(auth()->user()->isPicGudang() || auth()->user()->isSuperAdmin())
                                @if($bLog->status === 'requested')
                                <form action="{{ route('borrowings.approve', $bLog) }}" method="POST" class="inline" @submit="submitting = true">
                                    @csrf
                                    <button type="submit" :disabled="submitting" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-lg transition shadow-sm inline-flex items-center gap-1 disabled:opacity-50">
                                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                                        <span>Approve</span>
                                    </button>
                                </form>
                                @elseif($bLog->status === 'approved')
                                <form action="{{ route('borrowings.dispatch', $bLog) }}" method="POST" class="inline" @submit="submitting = true">
                                    @csrf
                                    <button type="submit" :disabled="submitting" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white text-xs font-black rounded-lg transition shadow-sm inline-flex items-center gap-1 disabled:opacity-50">
                                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                                        <span>Serahkan Berkas (Dispatch)</span>
                                    </button>
                                </form>
                                @elseif($bLog->status === 'dispatched')
                                <form action="{{ route('borrowings.return', $bLog) }}" method="POST" class="inline" @submit="submitting = true">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Konfirmasi pengembalian berkas fisik ke gudang?')" :disabled="submitting" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black rounded-lg transition shadow-sm inline-flex items-center gap-1 disabled:opacity-50">
                                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                                        <span>Konfirmasi Kembali</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-slate-500 font-bold">Selesai</span>
                                @endif
                            @else
                                <span class="text-xs text-slate-500 font-semibold">Monitoring</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">Belum ada riwayat pengajuan peminjaman dokumen.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $borrowings->links() }}
        </div>
    </div>
</div>
@endsection

