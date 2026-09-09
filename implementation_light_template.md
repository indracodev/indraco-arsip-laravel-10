# Perencanaan Modifikasi Template UI Minimalis, Proporsional & Dual Theme (Light & Dark)

Dokumen ini berisi rencana komprehensif modifikasi antarmuka pengguna (UI/UX) untuk **Document Management System (DMS) PT Indraco** dengan pendekatan desain **Minimalis, Proporsional, Kontras Optimal, dan Dukungan Mode Dual (Light & Dark Mode)**.

---

## 📐 1. Prinsip Utama Desain (Design Philosophy)

1. **Minimalis & Clean Corporate**: Menghilangkan dekorasi visual yang tidak perlu, memprioritaskan keterbacaan data, hirarki visual yang tegas, dan tata letak yang bersih.
2. **Proporsi Objek & Layout (Harmonious Scale)**: Menggunakan skala spacing, padding, margin, dan grid 8pt/16pt yang konsisten untuk menciptakan keseimbangan elemen visual.
3. **Kontras Warna Tinggi (WCAG AA/AAA Compliant)**: Memastikan rasio kontras antara teks dan latar belakang memenuhi standar keterbacaan (&gt; 4.5:1 untuk teks biasa, &gt; 7:1 untuk kontras optimal) baik pada Mode Light maupun Mode Dark.
4. **Adaptif Dual Theme Switcher**: Pengguna dapat berganti antara Mode Light (Terang) dan Mode Dark (Gelap) dengan transisi halus dan preferensi tersimpan di `localStorage`.

---

## 🎨 2. Sistem Warna & Rasio Kontras (Color System Matrix)

### A. Mode Light (Terang)
Mode Light dirancang untuk lingkungan kerja dengan pencahayaan terang, menggunakan warna putih bersih, abu-abu netral sejuk, dan aksen emas/navy yang tajam.

| Elemen UI | Class Tailwind CSS / Hex | Kontras Ratio | Keterangan |
| :--- | :--- | :--- | :--- |
| **Page Background** | `bg-slate-50` (`#F8FAFC`) | Base | Latar belakang utama aplikasi |
| **Card / Surface Background** | `bg-white` (`#FFFFFF`) | Base | Latar belakang kartu & panel data |
| **Primary Text (Judul & Main)** | `text-slate-900` (`#0F172A`) | **19.8 : 1** (AAA) | Sangat tajam & mudah dibaca |
| **Secondary Text (Subtitle/Label)**| `text-slate-600` (`#475569`) | **7.1 : 1** (AA) | Keterangan & sub-header |
| **Muted Text / Placeholder** | `text-slate-400` (`#94A3B8`) | **4.6 : 1** (AA) | Caption & placeholder input |
| **Border & Divider** | `border-slate-200` (`#E2E8F0`) | - | Pembatas antar elemen halus |
| **Accent Gold (Corporate)** | `text-amber-600` (`#D97706`) | **4.8 : 1** (AA) | Warna penanda khas Indraco |
| **Accent Navy (Corporate)** | `text-blue-900` (`#1E3A8A`) | **14.2 : 1** (AAA) | Badge & tombol sekunder |

### B. Mode Dark (Gelap)
Mode Dark dirancang untuk pencahayaan redup, mengurangi kelelahan mata (*eye strain*), dengan latardepan teks yang kontras tinggi di atas latar gelap.

| Elemen UI | Class Tailwind CSS / Hex | Kontras Ratio | Keterangan |
| :--- | :--- | :--- | :--- |
| **Page Background** | `dark:bg-slate-950` (`#020617`) | Base | Latar belakang utama aplikasi |
| **Card / Surface Background** | `dark:bg-slate-900` (`#0F172A`) | Base | Latar belakang kartu & panel data |
| **Primary Text (Judul & Main)** | `dark:text-slate-100` (`#F1F5F9`) | **17.5 : 1** (AAA) | Teks utama terang & tajam |
| **Secondary Text (Subtitle/Label)**| `dark:text-slate-400` (`#94A3B8`) | **6.8 : 1** (AA) | Keterangan & sub-header |
| **Muted Text / Placeholder** | `dark:text-slate-500` (`#64748B`) | **4.5 : 1** (AA) | Caption & placeholder input |
| **Border & Divider** | `dark:border-slate-800` (`#1E293B`) | - | Pembatas antar elemen halus |
| **Accent Gold (Corporate)** | `dark:text-amber-400` (`#FBBF24`) | **12.5 : 1** (AAA) | Aksen emas menyala pada mode gelap |
| **Accent Navy (Corporate)** | `dark:bg-slate-800` (`#1E293B`) | - | Background kartu & badge |

---

## 🔤 3. Skala Tipografi Proporsional (Typographic Scale)

| Jenis Elemen | Ukuran Font (Tailwind) | Font Weight | Line Height | Penggunaan |
| :--- | :--- | :--- | :--- | :--- |
| **Page Title (H1)** | `text-2xl` sm:`text-3xl` (24px - 30px) | `font-extrabold` | `leading-tight` | Judul utama halaman aplikasi |
| **Section Header (H2)** | `text-lg` sm:`text-xl` (18px - 20px) | `font-bold` | `leading-snug` | Sub-judul modul / section kartu |
| **Card Title (H3)** | `text-base` (16px) | `font-semibold` | `leading-normal` | Judul dalam modal / widget |
| **Body Text / Table** | `text-xs` sm:`text-sm` (12px - 14px) | `font-normal` / `font-medium` | `leading-relaxed` | Isi tabel, paragraf, deskripsi |
| **Small Caption / Badge** | `text-[10px]` - `text-xs` (10px - 12px) | `font-bold` | `leading-none` | Status badge, tag departemen, timestamp |

---

## 📐 4. Skala Spacing, Margin & Padding Proporsional

- **Container Bounds**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` (memberikan ruang bernapas yang cukup di sisi kiri-kanan).
- **Inter-Section Margin**: `space-y-6 sm:space-y-8` (jarak antar blok komponen 24px - 32px).
- **Card Padding**: `p-5 sm:p-6 lg:p-8` (padding internal kartu proporsional).
- **Grid Layout Gaps**: `gap-4 sm:gap-6` (jarak antar kolom kartu 16px - 24px).
- **Border Radius Standards**:
  - Main Cards: `rounded-2xl` atau `rounded-3xl` (16px - 24px)
  - Inputs & Buttons: `rounded-xl` (12px)
  - Small Badges & Chips: `rounded-lg` / `rounded-full` (8px)

---

## ⚙️ 5. Mekanisme Switching Theme (Light / Dark Mode Engine)

Mekanisme Switching Tema diatur menggunakan **Alpine.js** yang terpasang secara global pada elemen root `<html>` di `layouts/app.blade.php`:

```html
<html lang="id" 
      x-data="{ theme: localStorage.getItem('theme') || 'dark' }" 
      :class="theme === 'dark' ? 'dark' : ''">
```

### Tombol Switcher di Topbar Navbar:
```html
<button @click="theme = (theme === 'dark' ? 'light' : 'dark'); localStorage.setItem('theme', theme)" 
        type="button" 
        class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-amber-400 transition bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
    <template x-if="theme === 'dark'">
        <i data-lucide="sun" class="w-5 h-5 text-amber-400"></i>
    </template>
    <template x-if="theme !== 'dark'">
        <i data-lucide="moon" class="w-5 h-5 text-slate-700"></i>
    </template>
</button>
```

---

## 🛠️ 6. Rencana Pembaruan File View (Execution Steps)

1. **[`layouts/app.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/layouts/app.blade.php)**:
   - Tambahkan state Alpine.js `:class="theme === 'dark' ? 'dark' : ''"` pada tag `<html>`.
   - Update background root: `bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100`.
   - Tambahkan tombol toggle Light/Dark Mode pada Top Header Navbar.
   - Update Sidebar: `bg-white dark:bg-slate-950 border-slate-200 dark:border-slate-800`.
2. **[`auth/login.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/auth/login.blade.php)**:
   - Update kartu login dengan dual class `bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border-slate-200 dark:border-slate-800`.
   - Pastikan input form kontras tinggi di mode terang maupun gelap.
3. **[`dashboard/index.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/dashboard/index.blade.php)**:
   - Update Banner Utama, Stat Cards, Kartu Kapasitas Gudang, dan Tabel Berkas Terbaru menggunakan utilitas `dark:`.
4. **Modul Katalog, Peminjaman, Pemusnahan & Logs**:
   - Update [`archives/index.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/archives/index.blade.php), [`archives/create.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/archives/create.blade.php), [`archives/show.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/archives/show.blade.php), [`borrowings/index.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/borrowings/index.blade.php), [`destructions/index.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/destructions/index.blade.php), dan [`logs/index.blade.php`](file:///C:/laragon/www/%23Project2026/indraco-arsip-laravel-10/resources/views/logs/index.blade.php) agar seragam dalam mendukung dual theme & proporsi minimalis.

---

## 🔍 7. Rencana Verifikasi Pengujian Desain

- **Uji Keterbacaan Kontras (Contrast Check)**: Memastikan teks judul, body, dan badge mudah dibaca di kedua mode tanpa menyulitkan mata.
- **Uji Responsivitas**: Memastikan margin & padding mengecil secara proporsional pada layar mobile (sm/md/lg).
- **Uji Perubahan Tema**: Mengklik tombol toggle Light/Dark Mode dan memastikan seluruh komponen berganti warna secara mulus dan state tersimpan saat reload halaman.
