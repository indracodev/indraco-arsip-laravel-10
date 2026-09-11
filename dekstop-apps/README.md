# 🖥️ INDRACO DMS - Workstation Desktop Client

Aplikasi **Desktop Client Container (Electron / PWA)** untuk sistem **INDRACO Arsip (Document Management System - PT Indraco)**.

Aplikasi ini membungkus antarmuka web Laravel INDRACO DMS ke dalam jendela aplikasi desktop mandiri (*standalone desktop app*) yang dilengkapi dengan **file konfigurasi eksternal (`config.json`)** untuk menentukan URL server sasaran secara dinamis.

---

## 🚀 Cara Menjalankan & Membangun Executable (.exe)

### 1. Menjalankan Aplikasi Desktop Client (Mode Dev/Pengujian)

Untuk membuka aplikasi desktop client secara langsung dalam mode pengujian:

```bash
cd C:\laragon\www\#Project2026\indraco-arsip-laravel-10\dekstop-apps
cmd /c "npm start"
```

---

### 2. Membangun Installer Windows `.exe`

Untuk mengompilasi aplikasi menjadi installer Windows (`.exe` setup) dan versi *portable*:

```bash
cd C:\laragon\www\#Project2026\indraco-arsip-laravel-10\dekstop-apps
cmd /c "npm run build:win"
```

*Berkas installer Windows `.exe` akan secara otomatis dihasilkan pada direktori:*
`dekstop-apps/dist/INDRACO DMS Workstation Setup 1.0.0.exe`

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
├── package.json                # Manifesto paket & script build electron-builder
├── README.md                   # Petunjuk penggunaan & kompilasi (.exe)
└── implementation_dekstop_apps.md # Perencanaan arsitektur & roadmap
```
