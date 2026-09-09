@extends('layouts.app')

@section('title', 'Peminjaman Dokumen Arsip - DMS PT Indraco')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i data-lucide="file-check-2" class="w-7 h-7 text-emerald-400"></i>
                Manajemen Peminjaman Dokumen Arsip
            </h1>
            <p class="text-slate-400 text-sm">Pengajuan pinjam, persetujuan kurator gudang, pengeluaran berkas fisik, dan tracking pengembalian.</p>
        </div>

        <a href="{{ route('borrowings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 hover:to-emerald-300 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Pengajuan Pinjam Dokumen
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4">No. Box & Judul Berkas</th>
                        <th class="py-3.5 px-4">Peminjam</th>
                        <th class="py-3.5 px-4">Tgl Pinjam / Est. Kembali</th>
                        <th class="py-3.5 px-4">Tujuan Peminjaman</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi Gudang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse($borrowings as $bLog)
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4">
                            <div class="space-y-1">
                                <span class="font-mono text-xs text-amber-400 font-bold block">{{ $bLog->archive->box_number }}</span>
                                <a href="{{ route('archives.show', $bLog->archive) }}" class="font-bold text-white hover:text-amber-400 transition">
                                    {{ $bLog->archive->title }}
                                </a>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-xs">
                            <span class="font-bold text-slate-200 block">{{ $bLog->borrower->name }}</span>
                            <span class="text-slate-400">{{ $bLog->archive->department->code ?? 'Dept' }}</span>
                        </td>

                        <td class="py-4 px-4 text-xs space-y-1">
                            <div class="text-slate-300">Tgl Pinjam: {{ $bLog->borrow_date ? $bLog->borrow_date->format('d M Y') : 'Menunggu Dispatch' }}</div>
                            <div class="text-amber-400 font-semibold">Est. Kembali: {{ \Carbon\Carbon::parse($bLog->expected_return_date)->format('d M Y') }}</div>
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-300 max-w-xs truncate">
                            {{ $bLog->purpose }}
                        </td>

                        <td class="py-4 px-4">
                            @if($bLog->status === 'requested')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">Diajukan</span>
                            @elseif($bLog->status === 'approved')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">Disetujui</span>
                            @elseif($bLog->status === 'dispatched')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">Sedang Dipinjam</span>
                            @elseif($bLog->status === 'returned')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Dikembalikan</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right">
                            @if(auth()->user()->isPicGudang() || auth()->user()->isSuperAdmin())
                                @if($bLog->status === 'requested')
                                <form action="{{ route('borrowings.approve', $bLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-400 text-slate-950 text-xs font-bold rounded-lg transition">
                                        Approve
                                    </button>
                                </form>
                                @elseif($bLog->status === 'approved')
                                <form action="{{ route('borrowings.dispatch', $bLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-purple-500 hover:bg-purple-400 text-white text-xs font-bold rounded-lg transition">
                                        Serahkan Berkas (Dispatch)
                                    </button>
                                </form>
                                @elseif($bLog->status === 'dispatched')
                                <form action="{{ route('borrowings.return', $bLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Konfirmasi pengembalian berkas fisik ke gudang?')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-lg transition">
                                        Konfirmasi Kembali
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-slate-500 font-semibold">Selesai</span>
                                @endif
                            @else
                                <span class="text-xs text-slate-500 font-medium">Monitoring</span>
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
