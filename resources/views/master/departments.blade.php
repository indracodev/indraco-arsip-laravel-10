@extends('layouts.app')

@section('title', 'Master Departemen - DMS PT Indraco')

@section('content')
<div class="space-y-6" x-data="{ openAdd: false, editItem: null }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i data-lucide="building-2" class="w-7 h-7 text-purple-400"></i>
                Master Departemen Perusahaan
            </h1>
            <p class="text-slate-400 text-sm">Kelola daftar unit/departemen PT Indraco untuk pengelompokan arsip.</p>
        </div>

        <button @click="openAdd = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-bold text-sm shadow-lg transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Departemen
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4">Kode Dept</th>
                        <th class="py-3.5 px-4">Nama Departemen</th>
                        <th class="py-3.5 px-4">Deskripsi / Ruang Lingkup</th>
                        <th class="py-3.5 px-4">Total Berkas Arsip</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse($departments as $dept)
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4 font-mono text-xs text-amber-400 font-extrabold">
                            {{ $dept->code }}
                        </td>
                        <td class="py-4 px-4 font-bold text-white">
                            {{ $dept->name }}
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-300">
                            {{ $dept->description ?? '-' }}
                        </td>
                        <td class="py-4 px-4 text-xs font-semibold text-purple-300">
                            {{ $dept->archives_count }} Box/Berkas
                        </td>
                        <td class="py-4 px-4 text-right flex items-center justify-end gap-2">
                            <button @click="editItem = {{ json_encode($dept) }}" class="p-1.5 text-slate-400 hover:text-amber-400 hover:bg-slate-900 rounded-lg transition">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('master.departments.destroy', $dept) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus departemen ini?')" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-900 rounded-lg transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">Belum ada data departemen.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add -->
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Departemen Baru</h3>
            <form action="{{ route('master.departments.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Kode Departemen (cth: FIN, HRD)</label>
                    <input type="text" name="code" required uppercase maxlength="10" placeholder="FIN" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm font-mono text-amber-400 uppercase font-bold focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nama Departemen</label>
                    <input type="text" name="name" required placeholder="Keuangan & Akuntansi" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 bg-slate-900 text-slate-400 text-xs font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-purple-500 text-slate-950 text-xs font-bold rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <template x-if="editItem">
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-950 border border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4">
                <h3 class="text-lg font-bold text-white">Edit Departemen</h3>
                <form :action="'{{ url('/master/departments') }}/' + editItem.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Kode Departemen</label>
                        <input type="text" name="code" :value="editItem.code" required maxlength="10" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm font-mono text-amber-400 uppercase font-bold focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nama Departemen</label>
                        <input type="text" name="name" :value="editItem.name" required class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Deskripsi</label>
                        <textarea name="description" rows="2" x-text="editItem.description" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editItem = null" class="px-4 py-2 bg-slate-900 text-slate-400 text-xs font-semibold rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 text-slate-950 text-xs font-bold rounded-xl">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
