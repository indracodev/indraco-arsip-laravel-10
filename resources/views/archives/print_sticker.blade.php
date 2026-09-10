<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stiker Label Box Container - {{ $archive->box_number ?? 'DRAFT' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .sticker-card {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-6 font-sans flex flex-col items-center justify-center">

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Stiker Label Box (Ctrl+P)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-sm rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- Printable Sticker Label Card (Standard Box Label 100mm x 75mm aspect) -->
    <div class="sticker-card bg-white border-4 border-slate-900 rounded-2xl w-[450px] p-6 shadow-2xl space-y-4 relative overflow-hidden">
        
        <!-- Header: Logo & Company -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
            <div>
                <span class="text-xs font-black tracking-widest text-amber-600 uppercase block">LABEL CONTAINER ARSIP</span>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $archive->company_name ?? 'PT INDRACO' }}</h1>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-500 block uppercase">DEPARTEMEN</span>
                <span class="text-sm font-black text-slate-900 uppercase">{{ $archive->department->code }}</span>
            </div>
        </div>

        <!-- Box Code & QR / Barcode Display -->
        <div class="bg-slate-50 p-4 rounded-xl border-2 border-slate-900 text-center space-y-2">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">NOMOR KODE CONTAINER / BOX</span>
            <div class="text-2xl font-black font-mono tracking-wider text-slate-950">
                {{ $archive->box_number ?? 'BOX-PENDING-VERIFY' }}
            </div>
            <!-- Simulated QR/Barcode visual -->
            <div class="flex justify-center items-center pt-1">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($archive->box_number ?? 'DRAFT') }}" alt="QR Code" class="w-20 h-20 border border-slate-300 rounded p-1 bg-white">
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="border border-slate-300 p-2.5 rounded-lg bg-slate-50">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">LOKASI RAK GUDANG</span>
                <span class="font-extrabold text-slate-900 block text-sm">{{ $archive->location->full_location ?? 'BELUM CHECK-IN' }}</span>
            </div>

            <div class="border border-slate-300 p-2.5 rounded-lg bg-slate-50">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">JENIS DOKUMEN</span>
                <span class="font-extrabold text-slate-900 block text-sm">{{ $archive->document_type ?? 'UMUM' }}</span>
            </div>

            <div class="border border-slate-300 p-2.5 rounded-lg bg-slate-50">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">PERIODE (YY-MM)</span>
                <span class="font-extrabold text-amber-700 block text-sm">
                    {{ $archive->period_yy_mm ?? $archive->period_start_date->format('y-m') }}
                    <span class="text-[10px] text-slate-600 block font-normal">({{ $archive->period_text ?? $archive->period_start_date->format('M Y') }})</span>
                </span>
            </div>

            <div class="border border-slate-300 p-2.5 rounded-lg bg-slate-50">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">RETENTION & EXPIRY</span>
                <span class="font-extrabold text-rose-700 block text-sm">
                    {{ $archive->retention_years }} Thn Expiry: {{ $archive->retention_expiry_date ? \Carbon\Carbon::parse($archive->retention_expiry_date)->format('M Y') : '-' }}
                </span>
            </div>
        </div>

        <!-- Archive Title / Description -->
        <div class="border-t-2 border-slate-900 pt-3 text-xs">
            <span class="text-[10px] font-bold text-slate-500 uppercase block">DESKRIPSI RINGKAS BERKAS:</span>
            <p class="font-bold text-slate-900 line-clamp-2 mt-0.5">{{ $archive->title }}</p>
        </div>

        <!-- Footer Info -->
        <div class="flex justify-between items-center text-[9px] text-slate-400 font-mono border-t border-slate-200 pt-2">
            <span>TGL INPUT: {{ $archive->created_at ? $archive->created_at->format('d/m/Y H:i') : date('d/m/Y') }}</span>
            <span>SYSTEM INDRACO D-ARSIP</span>
        </div>
    </div>

</body>
</html>
