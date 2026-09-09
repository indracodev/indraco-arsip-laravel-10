@extends('layouts.app')

@section('title', 'Kelola User & Hak Akses - DMS PT Indraco')

@section('content')
<div class="space-y-6" x-data="{ openAdd: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i data-lucide="users" class="w-7 h-7 text-blue-400"></i>
                Kelola Pengguna & Hak Akses System
            </h1>
            <p class="text-slate-400 text-sm">Pengaturan role Super Admin, PIC Gudang Arsip, dan PIC Departemen Client.</p>
        </div>

        <button @click="openAdd = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-sm shadow-lg transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4">Nama Pengguna</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Departemen Linked</th>
                        <th class="py-3.5 px-4">No. Telepon</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm">
                    @forelse($users as $usr)
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="py-4 px-4 font-bold text-white">
                            {{ $usr->name }}
                        </td>
                        <td class="py-4 px-4 text-xs font-mono text-slate-300">
                            {{ $usr->email }}
                        </td>
                        <td class="py-4 px-4">
                            @if($usr->isSuperAdmin())
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Super Admin</span>
                            @elseif($usr->isPicGudang())
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">PIC Gudang Arsip</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">PIC Departemen</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-300">
                            {{ $usr->department->name ?? 'Global / Seluruh' }}
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-400">
                            {{ $usr->phone ?? '-' }}
                        </td>
                        <td class="py-4 px-4 text-right">
                            @if(auth()->id() !== $usr->id)
                            <form action="{{ route('master.users.destroy', $usr) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus user pengguna ini?')" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-900 rounded-lg transition">
                                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-slate-500 italic">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add -->
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-6 max-w-md w-full space-y-4">
            <h3 class="text-lg font-bold text-white">Tambah Pengguna Baru</h3>
            <form action="{{ route('master.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Alamat Email</label>
                    <input type="email" name="email" required class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Password</label>
                    <input type="password" name="password" required minlength="6" value="password" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Peran / Role</label>
                    <select name="role" required class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500">
                        <option value="pic_dept">PIC Departemen Client</option>
                        <option value="pic_gudang">PIC Gudang Arsip (Curator)</option>
                        <option value="admin">Super Admin / Management</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Departemen Linked (Khusus PIC Dept)</label>
                    <select name="department_id" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Tanpa Departemen (Global) --</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="081234567890" class="w-full p-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 bg-slate-900 text-slate-400 text-xs font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-slate-950 text-xs font-bold rounded-xl">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
