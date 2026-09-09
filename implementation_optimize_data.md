# Perencanaan Optimasi Master Data: Search, Sort, Loading State & CRUD Async

Dokumen ini memuat rancangan arsitektur dan langkah implementasi untuk menambahkan fitur **Search (Pencarian Live)**, **Sort (Pengurutan Kolom)**, **Visual Loading Indicator**, serta **Optimasi Proses CRUD (Simpan, Edit, Delete)** pada seluruh modul Master Data dan Detailnya pada sistem **Document Management System (DMS) PT Indraco**.

---

## 1. Tujuan & Scope Optimasi

1. **Pencarian Live (Live Search Engine)**:
   - Pengguna dapat mengetik kata kunci pencarian pada search bar di atas tabel data.
   - Pencarian bekerja secara instan/debounced tanpa perlu me-reload seluruh halaman.
   - Pencarian mencakup seluruh atribut penting (Kode, Nama, Deskripsi, Penanggung Jawab, Status, Lokasi Rak, dll).

2. **Pengurutan Kolom (Interactive Column Sorting)**:
   - Pengguna dapat mengklik header kolom tabel untuk mengurutkan data secara **Ascending (A-Z, 0-9)** atau **Descending (Z-A, 9-0)**.
   - Dilengkapi indikator visual panah sort (`lucide-arrow-up-down`, `lucide-arrow-up`, `lucide-arrow-down`).

3. **Visual Loading State & Skeleton Screen**:
   - Menampilkan spinner / skeleton loader saat data sedang difilter, diurutkan, atau dimuat dari server.
   - Menghilangkan kesan "aplikasi freeze" saat melakukan operasi data.

4. **Optimasi Proses CRUD (Simpan, Edit, Hapus)**:
   - **Simpan & Edit**: Tombol submit menampilkan animasi loading spinner (`animate-spin`), ter-disable otomatis untuk mencegah *double submit*.
   - **Hapus (Delete)**: Modal konfirmasi interaktif dengan indikator proses hapus.
   - Respon feedback cepat melalui Toast Alert / Flash Notice.

5. **Optimasi Backend & Memory Query**:
   - Menggunakan Eager Loading (`with()`, `withCount()`) di Controller Laravel untuk mencegah N+1 Query.
   - Penggunaan query builder berindeks untuk filter dan sorting cepat di level database.

---

## 2. Rincian Modul yang Dioptimasi

| Modul | Komponen Master / Detail | Fitur Search & Sort | Optimasi Load & CRUD |
|---|---|---|---|
| **Master Departemen** | `master/departments.blade.php` | Live Search Kode/Nama/Deskripsi + Sort Kolom | Alpine Data Table + Loading State Modal |
| **Master Gudang & Rak** | `master/warehouses.blade.php` | Search Gudang, Search Rak, Sort Kapasitas | Eager Load Rak & Status Terisi + Async Delete |
| **Custom Engine Format Box** | `master/numbering.blade.php` | Search Pattern & Preview Code + Sort Counter | Live Preview Generator + Async Switch Active |
| **Kelola User & Hak Akses** | `master/users.blade.php` | Search Nama/Email/Role/Dept + Sort Role/Nama | Password Hashing check + Loading button |
| **Katalog & Booking Arsip** | `archives/index.blade.php` | Multi-filter Dept/Gudang/Status + Sort Tgl | Server-side & Client-side Filter Hybrid |
| **Peminjaman Dokumen** | `borrowings/index.blade.php` | Search Peminjam/No Box + Sort Tgl Pinjam | Status Confirm Button Loader |
| **Retention & Pemusnahan** | `destructions/index.blade.php` | Filter Retention Status + Sort Tgl Pemusnahan | Multi-select action loader |
| **Log & Audit Trail** | `logs/index.blade.php` | Search User/Aksi/IP + Sort Timestamp | Fast Log Pagination & Filtering |

---

## 3. Desain Komponen Client-Side JavaScript (Alpine.js)

Komponen JavaScript ringan berbasis **Alpine.js** yang akan disematkan di setiap Blade view:

```javascript
function dataTableController(initialItems = [], options = {}) {
    return {
        searchQuery: '',
        sortColumn: options.defaultSort || 'id',
        sortDirection: 'asc',
        isLoading: false,
        items: initialItems,

        get filteredAndSortedItems() {
            let result = [...this.items];

            // 1. Live Filter Search
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(item => {
                    return Object.values(item).some(val => 
                        val && val.toString().toLowerCase().includes(q)
                    );
                });
            }

            // 2. Sorting Logic
            result.sort((a, b) => {
                let valA = a[this.sortColumn] ?? '';
                let valB = b[this.sortColumn] ?? '';
                
                if (typeof valA === 'string') valA = valA.toLowerCase();
                if (typeof valB === 'string') valB = valB.toLowerCase();

                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });

            return result;
        },

        sortBy(column) {
            this.isLoading = true;
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            setTimeout(() => { this.isLoading = false; }, 150);
        }
    }
}
```

---

## 4. Langkah Implementasi & Rencana Verifikasi

1. **Tahap 1**: Buat komponen `dataTable` Alpine.js yang reusable di `layouts/app.blade.php` atau helper script.
2. **Tahap 2**: Update `master/departments.blade.php` & `DepartmentController.php` dengan search bar, sort header, dan loading state.
3. **Tahap 3**: Update `master/warehouses.blade.php` & `WarehouseController.php` dengan search gudang/rak, sort & async loader.
4. **Tahap 4**: Update `master/numbering.blade.php` & `NumberingFormatController.php`.
5. **Tahap 5**: Update `master/users.blade.php` & `UserController.php`.
6. **Tahap 6**: Update modul transaksi (`archives`, `borrowings`, `destructions`, `logs`) agar mendukung search, sort, dan loading indicator yang seragam.
7. **Verifikasi**: Jalankan `php artisan view:clear` dan uji interaktivitas di browser.
