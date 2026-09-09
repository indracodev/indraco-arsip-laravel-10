@extends('layouts.app')

@section('title', 'Custom Engine Format Penomoran Box - DMS PT Indraco')

@section('content')
<div class="w-full space-y-6" x-data="{ 
    openAdd: false,
    searchQuery: '',
    submitting: false,
    formats: {{ json_encode($formats) }},

    get filteredFormats() {
        if (this.searchQuery.trim() === '') return this.formats;
        const q = this.searchQuery.toLowerCase();
        return this.formats.filter(f => 
            (f.name && f.name.toLowerCase().includes(q)) ||
            (f.pattern && f.pattern.toLowerCase().includes(q))
        );
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="binary" class="w-7 h-7 text-emerald-600 dark:text-emerald-400"></i>
                Dynamic Custom Box Code Engine
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Pengaturan format custom penomoran otomatis box arsip tanpa perlu mengubah kode program.</p>
        </div>

        <button @click="openAdd = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs sm:text-sm shadow-md transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Format Baru
        </button>
    </div>

    <!-- Live Preview Banner -->
    <div class="bg-slate-900 dark:bg-slate-950 border border-emerald-500/40 rounded-3xl p-6 sm:p-8 shadow-xl space-y-3 relative overflow-hidden text-white">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl"></div>
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block">Live Preview Hasil Custom Box Code saat ini:</span>
        <div class="flex items-center gap-3">
            <div class="p-3 bg-slate-950 border border-slate-800 rounded-2xl">
                <i data-lucide="qr-code" class="w-8 h-8 text-amber-400"></i>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black font-mono text-amber-400 tracking-wider">
                    {{ $previewCode }}
                </span>
                <span class="text-xs text-slate-300 block mt-0.5 font-medium">Contoh untuk Dept: FIN | Tahun: {{ date('Y') }} | Counter berikutnya</span>
            </div>
        </div>
    </div>

    <!-- Available Placeholders Reference Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
            Panduan Placeholder Pattern Format:
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="font-mono text-amber-600 dark:text-amber-400 font-extrabold block">{COMPANY}</span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Kode Perusahaan (IND)</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="font-mono text-amber-600 dark:text-amber-400 font-extrabold block">{DEPT}</span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Kode Departemen (FIN, HRD, MKT)</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="font-mono text-amber-600 dark:text-amber-400 font-extrabold block">{YEAR}</span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Tahun Dokumen (e.g. 2026)</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="font-mono text-amber-600 dark:text-amber-400 font-extrabold block">{ROMAN_MONTH}</span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Bulan Romawi (I, II, III, IX, XII)</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <span class="font-mono text-amber-600 dark:text-amber-400 font-extrabold block">{COUNTER}</span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Nomor Urut Padded (0001, 0002)</span>
            </div>
        </div>
    </div>

    <!-- Active Formats List Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Aturan Format Box Code Terdaftar</h2>

            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Cari nama atau pattern..." 
                    class="w-full pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition font-medium"
                >
                <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($formats as $fmt)
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border {{ $fmt->is_active ? 'border-amber-500/50 bg-amber-500/5 dark:bg-amber-500/5' : 'border-slate-200 dark:border-slate-800' }} space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 dark:text-white text-base">{{ $fmt->name }}</span>
                            @if($fmt->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-500/40">FORMAT AKTIF SYSTEM</span>
                            @endif
                        </div>
                        <span class="font-mono text-amber-600 dark:text-amber-400 text-sm font-extrabold block mt-1">{{ $fmt->pattern }}</span>
                    </div>

                    <span class="text-xs text-slate-600 dark:text-slate-400 font-semibold">Counter: {{ $fmt->current_counter }} | Padding: {{ $fmt->padding }} digit</span>
                </div>

                <form action="{{ route('master.numbering.update', $fmt) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-200 dark:border-slate-800/80" @submit="submitting = true">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="name" value="{{ $fmt->name }}">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pattern Template</label>
                        <input type="text" name="pattern" value="{{ $fmt->pattern }}" required class="w-full p-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-lg text-xs font-mono text-amber-600 dark:text-amber-400 font-bold focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Nilai Counter</label>
                        <input type="number" name="current_counter" value="{{ $fmt->current_counter }}" min="0" required class="w-full p-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white focus:outline-none font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Padding Digit</label>
                        <input type="number" name="padding" value="{{ $fmt->padding }}" min="1" max="10" required class="w-full p-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white focus:outline-none font-bold">
                    </div>

                    <div class="flex items-end gap-2">
                        <label class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer mb-2">
                            <input type="checkbox" name="is_active" value="1" {{ $fmt->is_active ? 'checked' : '' }} class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-amber-500">
                            Aktifkan
                        </label>
                        <button type="submit" :disabled="submitting" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black rounded-lg transition ml-auto mb-1 shadow-sm inline-flex items-center gap-1 disabled:opacity-50">
                            <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                            <span x-text="submitting ? '...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Add Format -->
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Format Penomoran Baru</h3>
            <form action="{{ route('master.numbering.store') }}" method="POST" class="space-y-4" @submit="submitting = true">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Deskripsi Format</label>
                    <input type="text" name="name" required placeholder="Format Standar Indraco 2026" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Pattern Template</label>
                    <input type="text" name="pattern" required placeholder="{COMPANY}/{DEPT}/{YEAR}/{ROMAN_MONTH}/{COUNTER}" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-mono text-amber-600 dark:text-amber-400 font-bold focus:outline-none focus:border-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nilai Counter Awal</label>
                        <input type="number" name="current_counter" value="0" min="0" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Padding Digit</label>
                        <input type="number" name="padding" value="4" min="1" max="10" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 font-bold">
                    </div>
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="rounded bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-emerald-600">
                        Aktifkan format ini secara otomatis
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black rounded-xl transition disabled:opacity-50">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                        <span x-text="submitting ? 'Memproses...' : 'Simpan Format'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

