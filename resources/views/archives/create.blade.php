@extends('layouts.app')

@section('title', 'Form Draft & Booking Tempat Arsip - DMS PT Indraco')

@section('content')
<div class="w-full space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('archives.index') }}" class="text-xs text-amber-600 dark:text-amber-400 font-bold hover:underline inline-flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Katalog
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="file-plus" class="w-7 h-7 text-amber-600 dark:text-amber-400"></i>
                Pengajuan Draft & Booking Gudang Arsip
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Isi rincian berkas arsip dan periode retention untuk diverifikasi oleh PIC Gudang.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm relative overflow-hidden">
        <form action="{{ route('archives.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                        Perusahaan / Entitas Indraco <span class="text-rose-500">*</span>
                    </label>
                    <select name="company_name" id="company_name" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium">
                        <option value="PT Indraco Global" {{ old('company_name') == 'PT Indraco Global' ? 'selected' : '' }}>PT Indraco Global</option>
                        <option value="PT Indraco Trading" {{ old('company_name') == 'PT Indraco Trading' ? 'selected' : '' }}>PT Indraco Trading</option>
                        <option value="PT Indraco Enterprise" {{ old('company_name') == 'PT Indraco Enterprise' ? 'selected' : '' }}>PT Indraco Enterprise</option>
                        <option value="PT Indraco International" {{ old('company_name') == 'PT Indraco International' ? 'selected' : '' }}>PT Indraco International</option>
                    </select>
                </div>

                <!-- Department Selection -->
                <div>
                    <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                        Departemen Pemilik Berkas <span class="text-rose-500">*</span>
                    </label>
                    @if(auth()->user()->isPicDept())
                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                        <input 
                            type="text" 
                            value="{{ auth()->user()->department->code }} - {{ auth()->user()->department->name }}" 
                            disabled 
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-800 dark:text-slate-300 font-bold cursor-not-allowed"
                        >
                    @else
                        <select name="department_id" id="department_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} - {{ $dept->name }}
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Document Type -->
                <div>
                    <label for="document_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                        Jenis Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <select name="document_type" id="document_type" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium">
                        <option value="PR" {{ old('document_type') == 'PR' ? 'selected' : '' }}>PR (Purchase Requisition)</option>
                        <option value="ABSENSI" {{ old('document_type') == 'ABSENSI' ? 'selected' : '' }}>ABSENSI</option>
                        <option value="UTILITY" {{ old('document_type') == 'UTILITY' ? 'selected' : '' }}>UTILITY</option>
                        <option value="DATA SAMPLE" {{ old('document_type') == 'DATA SAMPLE' ? 'selected' : '' }}>DATA SAMPLE</option>
                        <option value="FAKTUR" {{ old('document_type') == 'FAKTUR' ? 'selected' : '' }}>FAKTUR / INVOICE</option>
                        <option value="KONTRAK" {{ old('document_type') == 'KONTRAK' ? 'selected' : '' }}>KONTRAK / PERJANJIAN</option>
                        <option value="LAINNYA" {{ old('document_type') == 'LAINNYA' ? 'selected' : '' }}>LAINNYA</option>
                    </select>
                </div>
            </div>

            <!-- Archive Title -->
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                    Judul / Nama Berkas Arsip <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}" 
                    required
                    placeholder="Contoh: Laporan Keuangan & Faktur Pajak Q1 2026" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-amber-500 transition font-medium"
                >
                @error('title') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Period Range -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 space-y-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Periode Berkas Dokumen</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="period_start_date" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input 
                            type="date" 
                            name="period_start_date" 
                            id="period_start_date" 
                            value="{{ old('period_start_date', date('Y-01-01')) }}" 
                            required 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium"
                        >
                    </div>

                    <div>
                        <label for="period_end_date" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input 
                            type="date" 
                            name="period_end_date" 
                            id="period_end_date" 
                            value="{{ old('period_end_date', date('Y-03-31')) }}" 
                            required 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium"
                        >
                    </div>

                    <div>
                        <label for="period_yy_mm" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Format YY-MM</label>
                        <input 
                            type="text" 
                            name="period_yy_mm" 
                            id="period_yy_mm" 
                            value="{{ old('period_yy_mm', date('y-m')) }}" 
                            placeholder="e.g. 26-03" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-amber-500 transition font-medium"
                        >
                    </div>

                    <div>
                        <label for="period_text" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Label Periode Custom</label>
                        <input 
                            type="text" 
                            name="period_text" 
                            id="period_text" 
                            value="{{ old('period_text') }}" 
                            placeholder="e.g. Januari - Maret 2026" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-amber-500 transition font-medium"
                        >
                    </div>
                </div>
            </div>

            <!-- Retention & Physical Condition -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="retention_years" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                        Masa Simpan Retention (Tahun) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input 
                            type="number" 
                            name="retention_years" 
                            id="retention_years" 
                            value="{{ old('retention_years', 5) }}" 
                            min="1" 
                            max="5" 
                            required 
                            class="w-28 px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-bold"
                        >
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-bold">Maksimal 5 Tahun per ketentuan revisi gudang</span>
                    </div>
                </div>

                <div>
                    <label for="physical_condition" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                        Kondisi / Wadah Fisik Berkas <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="physical_condition" 
                        id="physical_condition" 
                        value="{{ old('physical_condition', 'Baik / Map Binder Hardcover') }}" 
                        required 
                        placeholder="Contoh: Baik / Box Karton Standard / Map Plastik" 
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium"
                    >
                </div>
            </div>

            <!-- Content Description -->
            <div>
                <label for="content_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1.5">
                    Rincian Isi Berkas & Metadata <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="content_description" 
                    id="content_description" 
                    rows="4" 
                    required 
                    placeholder="Tuliskan daftar dokumen detail yang ada di dalam box arsip ini (misal: Bukti Kas Keluar No 001-150, Faktur Pajak PPN, dsb)..." 
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-amber-500 transition font-medium"
                >{{ old('content_description') }}</textarea>
            </div>

            <!-- Attachment Scans section -->
            <div class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 space-y-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-700 dark:text-amber-400 block flex items-center gap-1.5">
                    <i data-lucide="file-check" class="w-4 h-4"></i> Upload Berkas Digital & Scan Formulir
                </span>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="scan_input_form" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">
                            Scan Formulir Input Arsip (Image/PDF)
                        </label>
                        <input 
                            type="file" 
                            name="scan_input_form" 
                            id="scan_input_form" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500/20 file:text-amber-700 dark:file:text-amber-400"
                        >
                    </div>

                    <div>
                        <label for="scan_approval_input" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">
                            Scan Bukti Approval Input (Image/PDF)
                        </label>
                        <input 
                            type="file" 
                            name="scan_approval_input" 
                            id="scan_approval_input" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500/20 file:text-amber-700 dark:file:text-amber-400"
                        >
                    </div>

                    <div>
                        <label for="file" class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">
                            Lampiran Lampiran Digital Lainnya
                        </label>
                        <input 
                            type="file" 
                            name="file" 
                            id="file" 
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-500/20 file:text-slate-700 dark:file:text-slate-400"
                        >
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('archives.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs sm:text-sm transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-500/20 transition">
                    Submit Booking & Pengajuan Arsip
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
