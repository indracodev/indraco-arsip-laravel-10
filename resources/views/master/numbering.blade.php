@extends('layouts.app')

@section('title', 'Custom Engine Format Penomoran Box - DMS PT Indraco')

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{ openAdd: false }">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <i data-lucide="binary" class="w-7 h-7 text-emerald-400"></i>
            Dynamic Custom Box Code Engine
        </h1>
        <p class="text-slate-400 text-sm">Pengaturan format custom penomoran otomatis box arsip tanpa perlu mengubah kode program.</p>
    </div>

    <!-- Live Preview Banner -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-emerald-500/40 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-3 relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl"></div>
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block">Live Preview Hasil Custom Box Code saat ini:</span>
        <div class="flex items-center gap-3">
            <div class="p-3 bg-slate-900 border border-slate-800 rounded-2xl">
                <i data-lucide="qr-code" class="w-8 h-8 text-amber-400"></i>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-extrabold font-mono text-amber-400 tracking-wider">
                    {{ $previewCode }}
                </span>
                <span class="text-xs text-slate-400 block mt-0.5">Contoh untuk Dept: FIN | Tahun: {{ date('Y') }} | Counter berikutnya</span>
            </div>
        </div>
    </div>

    <!-- Available Placeholders Reference Card -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-amber-400"></i>
            Panduan Placeholder Pattern Format:
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="font-mono text-amber-400 font-bold block">{COMPANY}</span>
                <span class="text-slate-400">Kode Perusahaan (IND)</span>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="font-mono text-amber-400 font-bold block">{DEPT}</span>
                <span class="text-slate-400">Kode Departemen (FIN, HRD, MKT)</span>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="font-mono text-amber-400 font-bold block">{YEAR}</span>
                <span class="text-slate-400">Tahun Dokumen (e.g. 2026)</span>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="font-mono text-amber-400 font-bold block">{ROMAN_MONTH}</span>
                <span class="text-slate-400">Bulan Romawi (I, II, III, IX, XII)</span>
            </div>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                <span class="font-mono text-amber-400 font-bold block">{COUNTER}</span>
                <span class="text-slate-400">Nomor Urut Padded (0001, 0002)</span>
            </div>
        </div>
    </div>

    <!-- Active Formats List -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Aturan Format Box Code Terdaftar</h2>
            <button @click="openAdd = true" type="button" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold rounded-xl transition">
                + Format Baru
            </button>
        </div>

        <div class="space-y-4">
            @foreach($formats as $fmt)
            <div class="p-5 rounded-2xl bg-slate-900/80 border {{ $fmt->is_active ? 'border-amber-500/50 bg-amber-500/5' : 'border-slate-800' }} space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white text-base">{{ $fmt->name }}</span>
                            @if($fmt->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">FORMAT AKTIF SYSTEM</span>
                            @endif
                        </div>
                        <span class="font-mono text-amber-400 text-sm font-bold block mt-1">{{ $fmt->pattern }}</span>
                    </div>

                    <span class="text-xs text-slate-400 font-semibold">Counter: {{ $fmt->current_counter }} | Padding: {{ $fmt->padding }} digit</span>
                </div>

                <form action="{{ route('master.numbering.update', $fmt) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-800/80">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="name" value="{{ $fmt->name }}">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pattern Template</label>
                        <input type="text" name="pattern" value="{{ $fmt->pattern }}" required class="w-full p-2 bg-slate-950 border border-slate-800 rounded-lg text-xs font-mono text-amber-400 font-bold focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nilai Counter</label>
                        <input type="number" name="current_counter" value="{{ $fmt->current_counter }}" min="0" required class="w-full p-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Padding Digit</label>
                        <input type="number" name="padding" value="{{ $fmt->padding }}" min="1" max="10" required class="w-full p-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none">
                    </div>

                    <div class="flex items-end gap-2">
                        <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer mb-2">
                            <input type="checkbox" name="is_active" value="1" {{ $fmt->is_active ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-amber-500">
                            Aktifkan
                        </label>
                        <button type="submit" class="px-4 py-2 bg-amber-500 text-slate-950 text-xs font-bold rounded-lg hover:bg-amber-400 transition ml-auto mb-1">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
