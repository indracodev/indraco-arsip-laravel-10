@extends('layouts.app')

@section('title', 'Master Gudang & Rak - DMS PT Indraco')

@section('content')
<div class="space-y-6" x-data="{ 
    openAddWarehouse: false, 
    openAddLocation: false,
    editWarehouseItem: null,
    searchQuery: '',
    sortByField: 'code',
    sortDirection: 'asc',
    submitting: false,
    isLoading: false,
    warehouses: {{ json_encode($warehouses) }},

    get filteredWarehouses() {
        let res = [...this.warehouses];
        if (this.searchQuery.trim() !== '') {
            const q = this.searchQuery.toLowerCase();
            res = res.filter(wh => {
                const matchWh = (wh.code && wh.code.toLowerCase().includes(q)) ||
                                (wh.name && wh.name.toLowerCase().includes(q)) ||
                                (wh.address && wh.address.toLowerCase().includes(q));
                const matchLoc = wh.locations && wh.locations.some(loc => 
                    (loc.rack_code && loc.rack_code.toLowerCase().includes(q)) ||
                    (loc.shelf_code && loc.shelf_code.toLowerCase().includes(q)) ||
                    (loc.full_location && loc.full_location.toLowerCase().includes(q))
                );
                return matchWh || matchLoc;
            });
        }
        res.sort((a, b) => {
            let valA = a[this.sortByField] ?? '';
            let valB = b[this.sortByField] ?? '';
            if (this.sortByField === 'locations_count') {
                valA = a.locations ? a.locations.length : 0;
                valB = b.locations ? b.locations.length : 0;
                return this.sortDirection === 'asc' ? valA - valB : valB - valA;
            }
            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();
            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
        return res;
    },

    toggleSort(field) {
        this.isLoading = true;
        if (this.sortByField === field) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortByField = field;
            this.sortDirection = 'asc';
        }
        setTimeout(() => { this.isLoading = false; lucide.createIcons(); }, 80);
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="warehouse" class="w-7 h-7 text-amber-600 dark:text-amber-400"></i>
                Master Gudang & Lokasi Penyimpanan Rak
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Kelola daftar gedung gudang, rak, baris, dan alokasi kapasitas box arsip PT Indraco.</p>
        </div>

        <div class="flex gap-2">
            <button @click="openAddWarehouse = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Gudang
            </button>
            <button @click="openAddLocation = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Lokasi Rak
            </button>
        </div>
    </div>

    <!-- Live Search & Sort Control Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-96">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari kode gudang, nama, lokasi rak, atau baris..." 
                class="w-full pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 transition font-medium"
            >
            <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto justify-between md:justify-end">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Urutkan:</span>
            <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-900 p-1 rounded-xl border border-slate-200 dark:border-slate-800">
                <button @click="toggleSort('code')" :class="sortByField === 'code' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-600 dark:text-slate-400 font-bold'" class="px-2.5 py-1 text-xs rounded-lg transition flex items-center gap-1">
                    Kode
                    <i data-lucide="arrow-up" class="w-3 h-3" x-show="sortByField === 'code' && sortDirection === 'asc'"></i>
                    <i data-lucide="arrow-down" class="w-3 h-3" x-show="sortByField === 'code' && sortDirection === 'desc'"></i>
                </button>
                <button @click="toggleSort('name')" :class="sortByField === 'name' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-600 dark:text-slate-400 font-bold'" class="px-2.5 py-1 text-xs rounded-lg transition flex items-center gap-1">
                    Nama
                    <i data-lucide="arrow-up" class="w-3 h-3" x-show="sortByField === 'name' && sortDirection === 'asc'"></i>
                    <i data-lucide="arrow-down" class="w-3 h-3" x-show="sortByField === 'name' && sortDirection === 'desc'"></i>
                </button>
                <button @click="toggleSort('locations_count')" :class="sortByField === 'locations_count' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-600 dark:text-slate-400 font-bold'" class="px-2.5 py-1 text-xs rounded-lg transition flex items-center gap-1">
                    Jml Rak
                    <i data-lucide="arrow-up" class="w-3 h-3" x-show="sortByField === 'locations_count' && sortDirection === 'asc'"></i>
                    <i data-lucide="arrow-down" class="w-3 h-3" x-show="sortByField === 'locations_count' && sortDirection === 'desc'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Warehouses Grid -->
    <div class="relative min-h-[200px]">
        <!-- Loading Overlay -->
        <div x-show="isLoading" x-cloak class="absolute inset-0 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xs rounded-3xl z-10 flex items-center justify-center">
            <div class="flex items-center gap-2 text-xs font-bold text-amber-600 dark:text-amber-400">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Mengurutkan gudang...
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <template x-for="wh in filteredWarehouses" :key="wh.id">
                <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2.5 py-1 rounded-md text-xs font-mono font-extrabold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30" x-text="wh.code"></span>
                                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white mt-2" x-text="wh.name"></h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1 font-medium">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i> <span x-text="wh.address || 'Alamat belum diisi'"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="editWarehouseItem = Object.assign({}, wh)" class="p-1.5 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg transition" title="Edit Data Gudang">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <form :action="'{{ url('/master/warehouses') }}/' + wh.id" method="POST" class="inline" @submit="submitting = true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus gudang ini?')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg transition" title="Hapus Gudang">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-800 pt-4 space-y-3 mt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Slot Rak & Baris:</span>
                                <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400" x-text="(wh.locations ? wh.locations.length : 0) + ' Rak Terdaftar'"></span>
                            </div>
                            
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                <template x-for="loc in (wh.locations || [])" :key="loc.id">
                                    <div class="p-3 bg-slate-50 dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-extrabold text-emerald-700 dark:text-emerald-400 block" x-text="loc.full_location || (loc.rack_code + ' - ' + loc.shelf_code)"></span>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium" x-text="'Kapasitas Maksimal: ' + loc.box_capacity + ' Box'"></span>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 rounded-full bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300 font-bold border border-slate-200 dark:border-slate-800" x-text="'Terisi ' + (loc.current_box_count || 0) + '/' + loc.box_capacity"></span>

                                            <form :action="'{{ url('/master/warehouses/locations') }}/' + loc.id" method="POST" class="inline" @submit="submitting = true">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Hapus lokasi rak ini?')" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus Rak">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!wh.locations || wh.locations.length === 0" class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-xl text-center text-xs text-slate-500 font-medium">
                                    Belum ada lokasi rak yang terdaftar di gudang ini.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="filteredWarehouses.length === 0" class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 text-center text-slate-500 text-xs font-medium">
            Tidak ada data gudang atau lokasi rak yang cocok dengan kata kunci pencarian.
        </div>
    </div>

    <!-- Modal Add Warehouse -->
    <div x-show="openAddWarehouse" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Gedung Gudang Baru</h3>
            <form action="{{ route('master.warehouses.store') }}" method="POST" class="space-y-4" @submit="submitting = true">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kode Gudang</label>
                    <input type="text" name="code" required placeholder="GUDANG-C" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-mono text-amber-600 dark:text-amber-400 uppercase font-bold focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Gudang</label>
                    <input type="text" name="name" required placeholder="Gudang Depo Gedangan Blok C" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Alamat Lokasi</label>
                    <textarea name="address" rows="2" placeholder="Kawasan Industri Indraco..." class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAddWarehouse = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-slate-950 text-xs font-black rounded-xl transition disabled:opacity-50">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                        <span x-text="submitting ? 'Memproses...' : 'Simpan Gudang'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Warehouse -->
    <template x-if="editWarehouseItem">
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Gudang</h3>
                <form :action="'{{ url('/master/warehouses') }}/' + editWarehouseItem.id" method="POST" class="space-y-4" @submit="submitting = true">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kode Gudang</label>
                        <input type="text" name="code" :value="editWarehouseItem.code" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-mono text-amber-600 dark:text-amber-400 uppercase font-bold focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Gudang</label>
                        <input type="text" name="name" :value="editWarehouseItem.name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Alamat Lokasi</label>
                        <textarea name="address" rows="2" x-text="editWarehouseItem.address" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editWarehouseItem = null" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-slate-950 text-xs font-black rounded-xl transition disabled:opacity-50">
                            <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                            <span x-text="submitting ? 'Memperbarui...' : 'Update Gudang'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Add Location -->
    <div x-show="openAddLocation" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Slot Rak / Baris Gudang</h3>
            <form action="{{ route('master.warehouses.locations.store') }}" method="POST" class="space-y-4" @submit="submitting = true">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Pilih Gudang</label>
                    <select name="warehouse_id" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium">
                        <template x-for="wh in warehouses" :key="wh.id">
                            <option :value="wh.id" x-text="wh.code + ' - ' + wh.name"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kode Rak</label>
                        <input type="text" name="rack_code" required placeholder="RAK-A3" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white uppercase focus:outline-none focus:border-amber-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kode Baris / Shelf</label>
                        <input type="text" name="shelf_code" required placeholder="BARIS-01" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white uppercase focus:outline-none focus:border-amber-500 font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kapasitas Maksimal Box</label>
                    <input type="number" name="box_capacity" value="50" min="1" max="1000" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-bold">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAddLocation = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-xs font-black rounded-xl transition disabled:opacity-50">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                        <span x-text="submitting ? 'Menambahkan...' : 'Tambah Rak'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

