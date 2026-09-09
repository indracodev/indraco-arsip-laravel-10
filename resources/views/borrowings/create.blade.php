@extends('layouts.app')

@section('title', 'Form Pengajuan Pinjam Arsip - DMS PT Indraco')

@section('content')
<div class="w-full max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('borrowings.index') }}" class="text-xs text-amber-400 font-semibold hover:underline inline-flex items-center gap-1 mb-2">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Log Peminjaman
        </a>
        <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <i data-lucide="file-symlink" class="w-7 h-7 text-emerald-400"></i>
            Formulir Permintaan Peminjaman Dokumen Arsip
        </h1>
        <p class="text-slate-400 text-sm">Pilih berkas fisik yang tersedia di gudang dan sebutkan keperluan peminjaman.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <form action="{{ route('borrowings.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Archive Picker -->
            <div>
                <label for="archive_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Pilih Berkas Dokumen Arsip (Status: Tersimpan di Gudang) <span class="text-rose-400">*</span>
                </label>
                <select name="archive_id" id="archive_id" required class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">-- Pilih Berkas Arsip --</option>
                    @foreach($archives as $arc)
                    <option value="{{ $arc->id }}" {{ $selectedArchiveId == $arc->id ? 'selected' : '' }}>
                        [{{ $arc->box_number }}] {{ $arc->title }} ({{ $arc->department->code ?? 'Dept' }})
                    </option>
                    @endforeach
                </select>
                @error('archive_id') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Expected Return Date -->
            <div>
                <label for="expected_return_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Estimasi Tanggal Pengembalian <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="date" 
                    name="expected_return_date" 
                    id="expected_return_date" 
                    value="{{ old('expected_return_date', \Carbon\Carbon::now()->addDays(7)->format('Y-m-d')) }}" 
                    required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500 transition"
                >
            </div>

            <!-- Purpose -->
            <div>
                <label for="purpose" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Maksud / Alasan Keperluan Peminjaman <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="purpose" 
                    id="purpose" 
                    rows="4" 
                    required 
                    placeholder="Contoh: Diperlukan untuk keperluan verifikasi audit internal pajak tahunan..." 
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition"
                >{{ old('purpose') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('borrowings.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-sm transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-400 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/20 transition">
                    Kirim Permintaan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
