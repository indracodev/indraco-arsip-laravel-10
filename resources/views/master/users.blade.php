@extends('layouts.app')

@section('title', 'Kelola User & Hak Akses - DMS PT Indraco')

@section('content')
<div class="space-y-6" x-data="{ 
    openAdd: false,
    editUserItem: null,
    searchQuery: '',
    sortColumn: 'name',
    sortDirection: 'asc',
    submitting: false,
    isLoading: false,
    currentUserId: {{ auth()->id() }},
    users: {{ json_encode($users) }},
    departments: {{ json_encode($departments) }},

    get filteredUsers() {
        let res = [...this.users];
        if (this.searchQuery.trim() !== '') {
            const q = this.searchQuery.toLowerCase();
            res = res.filter(u => {
                const deptName = u.department ? u.department.name : '';
                const deptCode = u.department ? u.department.code : '';
                return (u.name && u.name.toLowerCase().includes(q)) ||
                       (u.email && u.email.toLowerCase().includes(q)) ||
                       (u.role && u.role.toLowerCase().includes(q)) ||
                       (u.phone && u.phone.toLowerCase().includes(q)) ||
                       (deptName && deptName.toLowerCase().includes(q)) ||
                       (deptCode && deptCode.toLowerCase().includes(q));
            });
        }
        res.sort((a, b) => {
            let valA = a[this.sortColumn] ?? '';
            let valB = b[this.sortColumn] ?? '';
            if (this.sortColumn === 'department') {
                valA = a.department ? a.department.name : 'Global';
                valB = b.department ? b.department.name : 'Global';
            }
            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();
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
                <i data-lucide="users" class="w-7 h-7 text-blue-600 dark:text-blue-400"></i>
                Kelola Pengguna & Hak Akses System
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium">Pengaturan role Super Admin, PIC Gudang Arsip, dan PIC Departemen Client.</p>
        </div>

        <button @click="openAdd = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs sm:text-sm shadow-md transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Live Search & Counter Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari nama, email, role, atau dept..." 
                class="w-full pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition font-medium"
            >
            <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Menampilkan <span class="text-blue-600 dark:text-blue-400 font-bold" x-text="filteredUsers.length"></span> dari <span class="font-bold" x-text="users.length"></span> pengguna
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4 relative">
        <!-- Loading Overlay -->
        <div x-show="isLoading" x-cloak class="absolute inset-0 bg-white/60 dark:bg-slate-950/60 backdrop-blur-xs rounded-3xl z-10 flex items-center justify-center">
            <div class="flex items-center gap-2 text-xs font-bold text-blue-600 dark:text-blue-400">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Mengurutkan pengguna...
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 select-none">
                        <th @click="sortBy('name')" class="py-3.5 px-4 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition">
                            <div class="flex items-center gap-1.5">
                                Nama Pengguna
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'name'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'name' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'name' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th @click="sortBy('email')" class="py-3.5 px-4 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition">
                            <div class="flex items-center gap-1.5">
                                Email
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'email'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'email' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'email' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th @click="sortBy('role')" class="py-3.5 px-4 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition">
                            <div class="flex items-center gap-1.5">
                                Peran (Role)
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'role'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'role' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'role' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th @click="sortBy('department')" class="py-3.5 px-4 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition">
                            <div class="flex items-center gap-1.5">
                                Departemen Linked
                                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 opacity-40" x-show="sortColumn !== 'department'"></i>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'department' && sortDirection === 'asc'"></i>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-blue-600" x-show="sortColumn === 'department' && sortDirection === 'desc'"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4">No. Telepon</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-sm">
                    <template x-for="usr in filteredUsers" :key="usr.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition font-medium">
                            <td class="py-4 px-4 font-bold text-slate-900 dark:text-white" x-text="usr.name"></td>
                            <td class="py-4 px-4 text-xs font-mono text-slate-700 dark:text-slate-300" x-text="usr.email"></td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span x-show="usr.role === 'admin'" class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-500/30">Super Admin</span>
                                <span x-show="usr.role === 'pic_gudang'" class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-500/30">PIC Gudang Arsip</span>
                                <span x-show="usr.role === 'pic_dept'" class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30">PIC Departemen</span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-700 dark:text-slate-300 font-bold" x-text="usr.department ? (usr.department.code + ' - ' + usr.department.name) : 'Global / Seluruh'"></td>
                            <td class="py-4 px-4 text-xs text-slate-500 dark:text-slate-400" x-text="usr.phone || '-'"></td>
                            <td class="py-4 px-4 text-right flex items-center justify-end gap-1.5">
                                <button @click="editUserItem = Object.assign({}, usr)" class="p-1.5 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg transition" title="Edit Pengguna">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>

                                <template x-if="currentUserId !== usr.id">
                                    <form :action="'{{ url('/master/users') }}/' + usr.id" method="POST" class="inline" @submit="submitting = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus user pengguna ini?')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Hapus User">
                                            <i data-lucide="user-minus" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </template>
                                <template x-if="currentUserId === usr.id">
                                    <span class="text-xs text-slate-400 italic">Akun Anda</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredUsers.length === 0">
                        <td colspan="6" class="py-8 text-center text-slate-500 text-xs font-medium">Tidak ada data pengguna yang cocok dengan kata kunci pencarian.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add -->
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Pengguna Baru</h3>
            <form action="{{ route('master.users.store') }}" method="POST" class="space-y-4" @submit="submitting = true">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Alamat Email</label>
                    <input type="email" name="email" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Password</label>
                    <input type="password" name="password" required minlength="6" value="password" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Peran / Role</label>
                    <select name="role" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                        <option value="pic_dept">PIC Departemen Client</option>
                        <option value="pic_gudang">PIC Gudang Arsip (Curator)</option>
                        <option value="admin">Super Admin / Management</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Departemen Linked (Khusus PIC Dept)</label>
                    <select name="department_id" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                        <option value="">-- Tanpa Departemen (Global) --</option>
                        <template x-for="dept in departments" :key="dept.id">
                            <option :value="dept.id" x-text="dept.code + ' - ' + dept.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="081234567890" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-xl transition disabled:opacity-50">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                        <span x-text="submitting ? 'Memproses...' : 'Simpan User'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <template x-if="editUserItem">
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Pengguna</h3>
                <form :action="'{{ url('/master/users') }}/' + editUserItem.id" method="POST" class="space-y-4" @submit="submitting = true">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" :value="editUserItem.name" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Alamat Email</label>
                        <input type="email" name="email" :value="editUserItem.email" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Password Baru (Biarkan Kosong Jika Tidak Diubah)</label>
                        <input type="password" name="password" minlength="6" placeholder="******" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Peran / Role</label>
                        <select name="role" x-model="editUserItem.role" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                            <option value="pic_dept">PIC Departemen Client</option>
                            <option value="pic_gudang">PIC Gudang Arsip (Curator)</option>
                            <option value="admin">Super Admin / Management</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">Departemen Linked</label>
                        <select name="department_id" x-model="editUserItem.department_id" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                            <option value="">-- Tanpa Departemen (Global) --</option>
                            <template x-for="dept in departments" :key="dept.id">
                                <option :value="dept.id" x-text="dept.code + ' - ' + dept.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-400 mb-1">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" :value="editUserItem.phone" placeholder="081234567890" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 font-medium">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editUserItem = null" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-slate-950 text-xs font-black rounded-xl transition disabled:opacity-50">
                            <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="submitting"></i>
                            <span x-text="submitting ? 'Memperbarui...' : 'Update User'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

