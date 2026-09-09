@extends('layouts.app')

@section('title', 'Master Departemen - DMS PT Indraco')

@section('content')
<div class="space-y-6" x-data="{
    openAdd: false, 
    editItem: null,
    searchQuery: '',
    sortColumn: 'code',
    sortDirection: 'asc',
    submitting: false,
    isLoading: false,
    items: {{ json_encode($departments) }},

    get filteredItems() {
        let res = [...this.items];
        if (this.searchQuery.trim() !== '') {
            const q = this.searchQuery.toLowerCase();
            res = res.filter(i => 
                (i.code && i.code.toLowerCase().includes(q)) ||
                (i.name && i.name.toLowerCase().includes(q)) ||
                (i.description && i.description.toLowerCase().includes(q))
            );
        }
        res.sort((a, b) => {
            let valA = a[this.sortColumn] ?? '';
            let valB = b[this.sortColumn] ?? '';
            if (typeof valA === 'number' && typeof valB === 'number') {
                return this.sortDirection === 'asc' ? valA - valB : valB - valA;
            }
            valA = valA.toString().toLowerCase();
            valB = valB.toString().toLowerCase();
            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
        return res;
    },

    sortBy(col) {
        this.isLoading = true;
        if (this.sortColumn === col) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = col;
            this.sortDirection = 'asc';
        }
        setTimeout(() => { this.isLoading = false; lucide.createIcons(); }, 80);
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="building-2" class="w-7 h-7 text-purple-600 dark:text-purple-400"></i>
                Master Departemen Perusahaan
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Kelola daftar unit/departemen PT Indraco untuk pengelompokan arsip.</p>
        </div>

        <button @click="openAdd = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-black text-xs sm:text-sm shadow-md transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Departemen
        </button>
    </div>

    <!-- Filter Search & Data Counter Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari kode, nama, atau deskripsi..." 
                class="w-full pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-500 transition font-medium"
            >
            <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Menampilkan <span class="text-purple-600 dark:text-purple-400 font-bold" x-text="filteredItems.length"></span> dari <span class="font-bold" x-text="items.length"></span> departemen
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4 relative">
        <!-- Loading Overlay -->
        <div x-show="isLoading" x-cloak class="absolute inset-0 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xs rounded-3xl z-10 flex items-center justify-center">
            <div class="flex items-center gap-2 text-xs font-bold text-purple-600 dark:text-purple-400">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Mengurutkan data...
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 select-none">
                        <th @click="sortBy('code')" class="py-3.5 px-4 cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition">
                            <div class="flex items-center gap-1.5">
                                Kode Dept
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'code'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-purple-600" x-show="sortColumn === 'code' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-purple-600" x-show="sortColumn === 'code' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th @click="sortBy('name')" class="py-3.5 px-4 cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition">
                            <div class="flex items-center gap-1.5">
                                Nama Departemen
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'name'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-purple-600" x-show="sortColumn === 'name' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-purple-600" x-show="sortColumn === 'name' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4">Deskripsi / Ruang Lingkup</th>
                        <th @click="sortBy('archives_count')" class="py-3.5 px-4 cursor-pointer hover:text-purple-600 dark:hover:text-purple-400 transition">
                            <div class="flex items-center gap-1.5">
                                Total Berkas Arsip
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'archives_count'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-purple-600" x-show="sortColumn === 'archives_count' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-purple-600" x-show="sortColumn === 'archives_count' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    <template x-for="dept in filteredItems" :key="dept.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                            <td class="py-4 px-4 font-mono text-xs text-amber-600 dark:text-amber-400 font-black" x-text="dept.code"></td>
                            <td class="py-4 px-4 font-bold text-slate-900 dark:text-white" x-text="dept.name"></td>
                            <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-300 font-medium" x-text="dept.description || '-'"></td>
                            <td class="py-4 px-4 text-xs font-bold text-purple-700 dark:text-purple-300" x-text="(dept.archives_count || 0) + ' Box/Berkas'"></td>
                            <td class="py-4 px-4 text-right flex items-center justify-end gap-2">
                                <button @click="editItem = Object.assign({}, dept)" class="p-1.5 text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg transition" title="Edit Departemen">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <form :action="'{{ url('/master/departments') }}/' + dept.id" method="POST" class="inline" @submit="submitting = true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus departemen ini?')" class="p-1.5 text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg transition" title="Hapus Departemen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredItems.length === 0">
                        <td colspan="5" class="py-8 text-center text-slate-500 text-xs font-medium">Tidak ada data departemen yang cocok dengan kata kunci pencarian.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add -->
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Departemen Baru</h3>
            <form action="{{ route('master.departments.store') }}" method="POST" class="space-y-4" @submit="submitting = true">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kode Departemen (cth: FIN, HRD)</label>
                    <input type="text" name="code" required maxlength="10" placeholder="FIN" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-mono text-amber-600 dark:text-amber-400 uppercase font-bold focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Departemen</label>
                    <input type="text" name="name" required placeholder="Keuangan & Akuntansi" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-purple-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-500 font-medium"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-xs font-black rounded-xl transition disabled:opacity-50">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <template x-if="editItem">
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Departemen</h3>
                <form :action="'{{ url('/master/departments') }}/' + editItem.id" method="POST" class="space-y-4" @submit="submitting = true">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Kode Departemen</label>
                        <input type="text" name="code" :value="editItem.code" required maxlength="10" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-mono text-amber-600 dark:text-amber-400 uppercase font-bold focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Departemen</label>
                        <input type="text" name="name" :value="editItem.name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Deskripsi</label>
                        <textarea name="description" rows="2" x-text="editItem.description" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500 font-medium"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editItem = null" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-slate-950 text-xs font-black rounded-xl transition disabled:opacity-50">
                            <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                            <span x-text="submitting ? 'Memperbarui...' : 'Update'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

