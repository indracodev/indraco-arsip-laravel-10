# 🖥️ INDRACO DMS - Workstation Desktop Client

Aplikasi **Desktop Client Container (Electron / PWA)** untuk sistem **INDRACO Arsip (Document Management System - PT Indraco)**.

Aplikasi ini membungkus antarmuka web Laravel INDRACO DMS ke dalam jendela aplikasi desktop mandiri (*standalone desktop app*) lintas platform (**Windows & Linux Ubuntu**) yang dilengkapi dengan **file konfigurasi eksternal (`config.json`)** untuk menentukan URL server sasaran secara dinamis.

---

## 💻 Dukungan Platform OS

- 🪟 **Windows 10 / 11 (x64)**: Format Installer (`.exe` NSIS Setup) & Executable Portable (`.exe`).
- 🐧 **Linux Ubuntu / Debian (x64)**: Format Paket Debian (`.deb`) & Portable Binary (`.AppImage` / `linux-unpacked`).

---

## ⚡ Petunjuk Menjalankan dari Awal (Jika Folder `node_modules` & `dist` Belum Ada)

Jika Anda baru saja me-clone repositori ini atau folder `node_modules/` dan `dist/` belum dibuat (karena diabaikan oleh `.gitignore`), ikuti 3 langkah sederhana ini:

### 1️⃣ Langkah 1: Install Dependensi Node.js (`node_modules`)

Buka terminal / CMD, masuk ke folder `dekstop-apps`, lalu jalankan:

```bash
cd C:\laragon\www\#Project2026\indraco-arsip-laravel-10\dekstop-apps
cmd /c "npm install"
```

*Perintah ini akan secara otomatis mengunduh seluruh library Node.js & Electron yang diperlukan dan membuat folder `node_modules/`.*

---

### 2️⃣ Langkah 2: Menjalankan Aplikasi Desktop Client (Mode Dev/Pengujian)

Pastikan server Laravel sudah berjalan (`php artisan serve`), lalu buka aplikasi desktop client:

```bash
cmd /c "npm start"
```

---

### 3️⃣ Langkah 3: Membangun Ulang Paket Executable Installer (`dist/`)

Untuk membuat kembali folder `dist/` beserta file installer executable Windows (`.exe`) dan versi portable Linux Ubuntu:

```bash
cmd /c "npm run build:all"
```

*Folder `dist/` beserta installer `INDRACO DMS Workstation Setup 1.0.0.exe` dan binary Linux akan secara otomatis tergenerasi kembali.*

---

## 🚀 Cara Menjalankan & Membangun Executable

### 1. Menjalankan Aplikasi Desktop Client (Mode Dev/Pengujian)

Untuk membuka aplikasi desktop client secara langsung dalam mode pengujian:

```bash
cd C:\laragon\www\#Project2026\indraco-arsip-laravel-10\dekstop-apps
cmd /c "npm start"
```

---

### 2. Membangun Paket Linux Ubuntu (`.deb` & `.AppImage`)

Untuk mengompilasi aplikasi menjadi installer Linux Ubuntu Debian (`.deb`) dan aplikasi portable (`.AppImage`):

#### a. Build Semua Format Linux Ubuntu (`.deb` & `.AppImage`):

```bash
cd C:\laragon\www\#Project2026\indraco-arsip-laravel-10\dekstop-apps
cmd /c "npm run build:linux"
```

#### b. Build Spesifik Paket `.deb` (Debian/Ubuntu):

```bash
cmd /c "npm run build:deb"
```

#### c. Build Spesifik Portable `.AppImage`:

```bash
cmd /c "npm run build:appimage"
```

*Hasil kompilasi Linux akan secara otomatis disimpan di direktori `dekstop-apps/dist/`:*

- Paket Installer Ubuntu: `dekstop-apps/dist/indraco-dms-desktop_1.0.0_amd64.deb`
- Portable Executable Ubuntu: `dekstop-apps/dist/INDRACO DMS Workstation-1.0.0.AppImage`

> [!IMPORTANT]
> **Catatan Penting Kompilasi Paket `.deb` (Debian/Ubuntu)**:
> Perintah `npm run build:deb` membutuhkan utilitas bawaan Linux (`fpm` / `dpkg-deb`).
>
> - Jika dijalankan di **Windows Host**, `electron-builder` akan menampilkan error `cause=exec: "fpm": executable file not found in %PATH%`.
> - **Solusi jika Anda di Windows**: Gunakan `npm run build:all` atau `npm run build:dir` untuk menghasilkan versi Linux Unpacked Portable (`dist/linux-unpacked/`) secara langsung tanpa error.
> - **Solusi jika Anda ingin file `.deb`**: Jalankan perintah `npm run build:deb` dari dalam sistem operasi **Linux Ubuntu / Terminal WSL Ubuntu**.

---

### 3. Cara Menginstal di Linux Ubuntu / Debian

#### Opsi A: Menggunakan Paket Debian (`.deb`)

Buka Terminal di Ubuntu dan jalankan perintah berikut:

```bash
# 1. Install paket .deb menggunakan dpkg atau apt
sudo dpkg -i indraco-dms-desktop_1.0.0_amd64.deb

# 2. Jika ada ketergantungan library yang kurang, perbaiki dengan:
sudo apt-get install -f
```

Setelah terinstall, aplikasi **INDRACO DMS Workstation** dapat ditemukan di menu aplikasi Ubuntu (**Applications -> Office / Utility**).

#### Opsi B: Menggunakan Portable Binary (`.AppImage`)

```bash
# 1. Berikan izin eksekusi pada file AppImage
chmod +x "INDRACO DMS Workstation-1.0.0.AppImage"

# 2. Jalankan aplikasi langsung tanpa perlu install:
./"INDRACO DMS Workstation-1.0.0.AppImage"
```

---

### 4. Membangun Installer Windows (`.exe`)

Untuk mengompilasi aplikasi menjadi installer Windows (`.exe` setup) dan versi *portable*:

```bash
cd C:\laragon\www\#Project2026\indraco-arsip-laravel-10\dekstop-apps
cmd /c "npm run build:win"
```

*Berkas installer Windows `.exe` akan secara otomatis dihasilkan pada direktori:*

- Setup Installer: `dekstop-apps/dist/INDRACO DMS Workstation Setup 1.0.0.exe`
- Standalone Portable: `dekstop-apps/dist/INDRACO DMS Workstation 1.0.0.exe`

---

### 5. Membangun Paket Semua Platform (Windows & Linux Ubuntu Sekaligus)

```bash
cmd /c "npm run build:all"
```

---

## ⚙️ Pengaturan Konfigurasi Server (`config.json`)

File `config.json` berada di direktori `dekstop-apps/config.json`. Anda dapat mengubah `target_url` server tanpa perlu melakukan rekompilasi aplikasi:

```json
{
  "app_name": "INDRACO DMS Desktop Client",
  "version": "1.0.0",
  "server_config": {
    "target_url": "http://127.0.0.1:8000",
    "fallback_url": "http://localhost:8000",
    "connection_timeout_ms": 5000,
    "auto_reconnect": true,
    "reconnect_interval_ms": 3000
  },
  "window_settings": {
    "title": "INDRACO DMS - Workstation Desktop Edition",
    "width": 1366,
    "height": 768,
    "min_width": 1024,
    "min_height": 600,
    "maximized_on_start": true
  }
}
```

- **`target_url`**: Ubah alamat IP server/domain Laravel yang ingin dibuka (misal: `http://127.0.0.1:8000` atau `https://dms.indraco.com`).

---

## 📂 Struktur Berkas Desktop Client

```
dekstop-apps/
├── config.json                 # Berkas konfigurasi dinamis URL & window
├── main.js                     # Electron Main Process Script
├── preload.js                  # IPC Context Bridge
├── offline.html                # Layar Connection Retry jika server terputus
├── settings.html               # Halaman Pengaturan Server URL desktop
├── package.json                # Manifesto paket & script build (Windows & Linux)
├── README.md                   # Petunjuk penggunaan, instalasi Ubuntu & .exe
└── implementation_dekstop_apps.md # Perencanaan arsitektur & roadmap
```
