@extends('layouts.app')

@section('title', 'Retention & Pemusnahan Arsip - DMS PT Indraco')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <i data-lucide="shield-alert" class="w-7 h-7 text-rose-600 dark:text-rose-400"></i>
            Manajemen Retention & Pemusnahan Dokumen (BAP)
        </h1>
        <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Pengawasan masa simpan dokumen kadaluarsa, usulan pemusnahan, dan pencetakan Berita Acara Pemusnahan (BAP).</p>
    </div>

    <!-- Expired or Expiring Archives Panel -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="hourglass" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    Daftar Berkas Mendekati / Lewat Masa Simpan (Retention Expiry)
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Arsip yang perlu diproses Berita Acara Pemusnahan (BAP)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-4">No. Box Arsip</th>
                        <th class="py-3.5 px-4">Judul Berkas & Dept</th>
                        <th class="py-3.5 px-4">Lokasi Fisik</th>
                        <th class="py-3.5 px-4">Tgl Expiry Retention</th>
                        <th class="py-3.5 px-4">Status Simpan</th>
                        <th class="py-3.5 px-4 text-right">Eksekusi BAP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    @forelse($expiredArchives as $arc)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4 font-mono text-xs text-amber-600 dark:text-amber-400 font-extrabold">
                            {{ $arc->box_number }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $arc->title }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $arc->department->code ?? 'Dept' }} | {{ $arc->period_text }}</span>
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ $arc->location->full_location ?? 'Gudang' }}
                        </td>

                        <td class="py-4 px-4 text-xs">
                            <span class="font-extrabold text-rose-600 dark:text-rose-400">
                                {{ \Carbon\Carbon::parse($arc->retention_expiry_date)->format('d M Y') }}
                            </span>
                            <span class="text-[10px] text-slate-500 block">({{ $arc->retention_years }} Thn Retention)</span>
                        </td>

                        <td class="py-4 px-4">
                            @if($arc->status === 'destroyed')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-500/30">Dimusnahkan</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">Jatuh Tempo</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right">
                            @if($arc->status !== 'destroyed' && (auth()->user()->isPicGudang() || auth()->user()->isSuperAdmin()))
                            <a href="{{ route('destructions.propose', $arc) }}" class="px-3.5 py-1.5 rounded-xl bg-rose-600 text-white hover:bg-rose-500 font-black text-xs shadow-md transition inline-flex items-center gap-1">
                                <i data-lucide="file-x" class="w-4 h-4"></i> Proses BAP
                            </a>
                            @else
                            <span class="text-xs text-slate-500 font-bold">BAP Prosedur</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">Tidak ada berkas yang jatuh tempo pemusnahan saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Destruction History Panel -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div>
            <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="check-check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                Riwayat Berita Acara Pemusnahan (BAP) Disahkan
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Arsip yang telah resmi dimusnahkan beserta dokumen bukti fisik BAP</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-4">Nomor BAP</th>
                        <th class="py-3.5 px-4">Judul Berkas Arsip</th>
                        <th class="py-3.5 px-4">Tanggal Eksekusi</th>
                        <th class="py-3.5 px-4">Metode Pemusnahan</th>
                        <th class="py-3.5 px-4">Eksekutor Gudang</th>
                        <th class="py-3.5 px-4 text-right">Cetak BAP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    @forelse($destructionLogs as $dLog)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4 font-mono text-xs text-amber-600 dark:text-amber-400 font-extrabold">
                            {{ $dLog->bap_number }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $dLog->archive->title }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Box Code: {{ $dLog->archive->box_number }}</span>
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ \Carbon\Carbon::parse($dLog->destruction_date)->format('d M Y') }}
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ $dLog->method }}
                        </td>

                        <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-medium">
                            {{ $dLog->proposedBy->name ?? 'Gudang Specialist' }}
                        </td>

                        <td class="py-4 px-4 text-right">
                            <a href="{{ route('destructions.bap', $dLog) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-amber-600 dark:text-amber-400 font-extrabold text-xs transition inline-flex items-center gap-1">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak BAP
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada riwayat pemusnahan dokumen yang dicatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
