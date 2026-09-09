@extends('layouts.app')

@section('title', 'Peminjaman Dokumen Arsip - DMS PT Indraco')

@section('content')
<div class="space-y-6">
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

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-4">No. Box & Judul Berkas</th>
                        <th class="py-3.5 px-4">Peminjam</th>
                        <th class="py-3.5 px-4">Tgl Pinjam / Est. Kembali</th>
                        <th class="py-3.5 px-4">Tujuan Peminjaman</th>
                        <th class="py-3.5 px-4">Status</th>
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
                                <form action="{{ route('borrowings.approve', $bLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white hover:bg-blue-500 text-xs font-black rounded-lg transition shadow-sm">
                                        Approve
                                    </button>
                                </form>
                                @elseif($bLog->status === 'approved')
                                <form action="{{ route('borrowings.dispatch', $bLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white text-xs font-black rounded-lg transition shadow-sm">
                                        Serahkan Berkas (Dispatch)
                                    </button>
                                </form>
                                @elseif($bLog->status === 'dispatched')
                                <form action="{{ route('borrowings.return', $bLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Konfirmasi pengembalian berkas fisik ke gudang?')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black rounded-lg transition shadow-sm">
                                        Konfirmasi Kembali
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
