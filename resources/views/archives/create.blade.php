@extends('layouts.app')

@section('title', 'Form Draft & Booking Tempat Arsip - DMS PT Indraco')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('archives.index') }}" class="text-xs text-amber-400 font-semibold hover:underline inline-flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Katalog
            </a>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i data-lucide="file-plus" class="w-7 h-7 text-amber-400"></i>
                Pengajuan Draft & Booking Gudang Arsip
            </h1>
            <p class="text-slate-400 text-sm">Isi rincian berkas arsip dan periode retention untuk diverifikasi oleh PIC Gudang.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <form action="{{ route('archives.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Selection -->
                <div>
                    <label for="department_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Departemen Pemilik Berkas <span class="text-rose-400">*</span>
                    </label>
                    @if(auth()->user()->isPicDept())
                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                        <input 
                            type="text" 
                            value="{{ auth()->user()->department->code }} - {{ auth()->user()->department->name }}" 
                            disabled 
                            class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-300 font-semibold cursor-not-allowed"
                        >
                    @else
                        <select name="department_id" id="department_id" required class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500 transition">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} - {{ $dept->name }}
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Archive Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Judul / Nama Berkas Arsip <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        value="{{ old('title') }}" 
                        required
                        placeholder="Contoh: Laporan Keuangan & Faktur Pajak Q1 2026" 
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                    >
                    @error('title') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Period Range -->
            <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400 block">Periode Berkas Dokumen</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="period_start_date" class="block text-xs font-semibold text-slate-400 mb-1">Tanggal Mulai <span class="text-rose-400">*</span></label>
                        <input 
                            type="date" 
                            name="period_start_date" 
                            id="period_start_date" 
                            value="{{ old('period_start_date', date('Y-01-01')) }}" 
                            required 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>

                    <div>
                        <label for="period_end_date" class="block text-xs font-semibold text-slate-400 mb-1">Tanggal Selesai <span class="text-rose-400">*</span></label>
                        <input 
                            type="date" 
                            name="period_end_date" 
                            id="period_end_date" 
                            value="{{ old('period_end_date', date('Y-03-31')) }}" 
                            required 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>

                    <div>
                        <label for="period_text" class="block text-xs font-semibold text-slate-400 mb-1">Label Periode Custom (Opsional)</label>
                        <input 
                            type="text" 
                            name="period_text" 
                            id="period_text" 
                            value="{{ old('period_text') }}" 
                            placeholder="e.g. Januari - Maret 2026" 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>
                </div>
            </div>

            <!-- Retention & Physical Condition -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="retention_years" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Masa Simpan Retention (Tahun) <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input 
                            type="number" 
                            name="retention_years" 
                            id="retention_years" 
                            value="{{ old('retention_years', 5) }}" 
                            min="1" 
                            max="50" 
                            required 
                            class="w-28 px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500 transition"
                        >
                        <span class="text-xs text-slate-400">Tahun sejak tanggal akhir periode berkas</span>
                    </div>
                </div>

                <div>
                    <label for="physical_condition" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        Kondisi / Wadah Fisik Berkas <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="physical_condition" 
                        id="physical_condition" 
                        value="{{ old('physical_condition', 'Baik / Map Binder Hardcover') }}" 
                        required 
                        placeholder="Contoh: Baik / Box Karton Standard / Map Plastik" 
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500 transition"
                    >
                </div>
            </div>

            <!-- Content Description -->
            <div>
                <label for="content_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Rincian Isi Berkas & Metadata <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="content_description" 
                    id="content_description" 
                    rows="4" 
                    required 
                    placeholder="Tuliskan daftar dokumen detail yang ada di dalam box arsip ini (misal: Bukti Kas Keluar No 001-150, Faktur Pajak PPN, dsb)..." 
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                >{{ old('content_description') }}</textarea>
            </div>

            <!-- Optional Attachment File Scan -->
            <div>
                <label for="file" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Upload Scan Berkas Digital (Opsional)
                </label>
                <input 
                    type="file" 
                    name="file" 
                    id="file" 
                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-300 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500/20 file:text-amber-400 hover:file:bg-amber-500/30"
                >
                <span class="text-[11px] text-slate-500 mt-1 block">Format didukung: PDF, JPG, PNG, DOC, ZIP (Maksimal 10MB)</span>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('archives.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-sm transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition">
                    Submit Booking & Pengajuan Arsip
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
