@extends('layouts.app')

@section('title', 'Master Gudang & Rak - DMS PT Indraco')

@section('content')
<div class="space-y-8" x-data="{ openAddWarehouse: false, openAddLocation: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i data-lucide="warehouse" class="w-7 h-7 text-amber-400"></i>
                Master Gudang & Lokasi Penyimpanan Rak
            </h1>
            <p class="text-slate-400 text-sm">Kelola daftar gedung gudang, rak, baris, dan alokasi kapasitas box arsip PT Indraco.</p>
        </div>

        <div class="flex gap-2">
            <button @click="openAddWarehouse = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Gudang
            </button>
            <button @click="openAddLocation = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Lokasi Rak
            </button>
        </div>
    </div>

    <!-- Warehouses Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($warehouses as $wh)
        <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ $wh->code }}
                    </span>
                    <h2 class="text-lg font-extrabold text-white mt-2">{{ $wh->name }}</h2>
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-500"></i> {{ $wh->address ?? 'Alamat belum diisi' }}
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Daftar Slot Rak & Baris:</span>
                
                <div class="space-y-2">
                    @forelse($wh->locations as $loc)
                    <div class="p-3 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-emerald-400 block">{{ $loc->full_location }}</span>
                            <span class="text-[11px] text-slate-400">Kapasitas Maksimal: {{ $loc->box_capacity }} Box</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-full bg-slate-950 text-slate-300 font-semibold border border-slate-800">
                                Terisi {{ $loc->current_box_count }}/{{ $loc->box_capacity }}
                            </span>

                            <form action="{{ route('master.warehouses.locations.destroy', $loc) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus lokasi rak ini?')" class="p-1 text-slate-500 hover:text-rose-400 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 bg-slate-900/40 rounded-xl text-center text-xs text-slate-500">
                        Belum ada lokasi rak yang terdaftar di gudang ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Add Warehouse -->
    <div x-show="openAddWarehouse" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Gedung Gudang Baru</h3>
            <form action="{{ route('master.warehouses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Kode Gudang</label>
                    <input type="text" name="code" required uppercase placeholder="GUDANG-C" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm font-mono text-amber-400 uppercase font-bold focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nama Gudang</label>
                    <input type="text" name="name" required placeholder="Gudang Depo Gedangan Blok C" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Alamat Lokasi</label>
                    <textarea name="address" rows="2" placeholder="Kawasan Industri Indraco..." class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAddWarehouse = false" class="px-4 py-2 bg-slate-900 text-slate-400 text-xs font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 text-slate-950 text-xs font-bold rounded-xl">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add Location -->
    <div x-show="openAddLocation" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Slot Rak / Baris Gudang</h3>
            <form action="{{ route('master.warehouses.locations.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Pilih Gudang</label>
                    <select name="warehouse_id" required class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->code }} - {{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Kode Rak</label>
                        <input type="text" name="rack_code" required placeholder="RAK-A3" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white uppercase focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Kode Baris / Shelf</label>
                        <input type="text" name="shelf_code" required placeholder="BARIS-01" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white uppercase focus:outline-none focus:border-amber-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Kapasitas Maksimal Box</label>
                    <input type="number" name="box_capacity" value="50" min="1" max="1000" required class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAddLocation = false" class="px-4 py-2 bg-slate-900 text-slate-400 text-xs font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-slate-950 text-xs font-bold rounded-xl">Tambah Rak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
