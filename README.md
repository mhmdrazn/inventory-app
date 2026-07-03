# Inventory App — PT Telkomsel

Prototipe **Sistem Manajemen Inventaris Berbasis Web** yang dibangun untuk seleksi magang Sistem Informasi PT Telkomsel. Aplikasi ini memudahkan admin dan staf gudang untuk mengelola stok barang, mencatat peminjaman & pengembalian, serta menghasilkan laporan inventaris secara realtime.

> 🔗 **Demo:** _Coming soon — will be deployed on Railway._

---

## ✨ Fitur

### Wajib (Core)
- **Autentikasi & Otorisasi** — Laravel Breeze + role-based (Admin, Staff, Manager) via `RoleMiddleware`.
- **Manajemen Produk** — CRUD produk lengkap dengan kode, kategori, stok, lokasi, kondisi (baik / rusak_ringan / rusak_berat), dan upload gambar.
- **Manajemen Kategori** — CRUD kategori (Admin).
- **Sistem Peminjaman** — pencatatan peminjaman multi-item dengan validasi stok berjalan di dalam DB transaction (locking) supaya stok tidak minus.
- **Proses Pengembalian** — otomatis mengembalikan stok saat item dikembalikan.
- **Dashboard Statistik** — kartu ringkasan (total stok, sedang dipinjam, tersedia, total kategori), chart tren peminjaman 12 bulan (Chart.js), tabel peminjaman terbaru, tabel stok menipis, daftar peminjaman terlambat.
- **Manajemen User** — CRUD user & assignment role (Admin only).

### Bonus
- 📄 **Export PDF** — laporan inventaris + riwayat peminjaman dalam PDF via `barryvdh/laravel-dompdf`.
- 📊 **Export Excel** — dua sheet (Inventaris & Peminjaman) via `maatwebsite/excel`.
- 🌗 **Dark Mode Toggle** — toggle di navigation (Alpine.js) dengan preferensi tersimpan di `localStorage` dan fallback ke `prefers-color-scheme`.
- 🔔 **Notifikasi Stok Menipis** — banner peringatan otomatis di dashboard bila ada barang dengan stok ≤ 5.
- 🔌 **REST API v1** — endpoint versi 1 dengan autentikasi token Laravel Sanctum dan Eloquent API Resources.
- 🏗️ **Deployment ready** — `Procfile` + `nixpacks.toml` untuk deploy ke Railway.

---

## 🛠️ Tech Stack

| Layer         | Teknologi                                                                 |
|---------------|---------------------------------------------------------------------------|
| Backend       | Laravel 13, PHP 8.5                                                       |
| Frontend      | Blade + Alpine.js 3 + Tailwind CSS 3, Chart.js                            |
| Auth Scaffold | Laravel Breeze (Blade preset)                                             |
| API Auth      | Laravel Sanctum                                                           |
| Database      | PostgreSQL (Supabase, pooler port 6543)                                   |
| PDF Export    | barryvdh/laravel-dompdf                                                   |
| Excel Export  | maatwebsite/excel                                                         |
| Testing       | PHPUnit 12                                                                |
| Build         | Vite                                                                       |
| Deployment    | Railway (Nixpacks builder)                                                |

---

## 📐 ERD & Database Schema

Skema database didefinisikan dalam **DBML** di [`schema.dbml`](./schema.dbml). Untuk melihat diagram interaktif, tempelkan isi file tersebut ke [dbdiagram.io](https://dbdiagram.io/).

Ringkasan tabel:

| Tabel               | Deskripsi                                                        |
|---------------------|------------------------------------------------------------------|
| `roles`             | Master role (admin, staff, manager)                              |
| `users`             | Akun user + `role_id`                                            |
| `categories`        | Kategori barang                                                  |
| `products`          | Data barang (kode, stok, kondisi, lokasi, gambar)                |
| `borrowings`        | Header peminjaman (peminjam, tanggal, status, catatan)           |
| `borrowing_details` | Baris item per peminjaman (produk & jumlah)                      |

---

## 🚀 Instalasi Lokal

### Prasyarat
- PHP 8.3+ (rekomendasi 8.5)
- Composer 2.x
- Node.js 20+ dan npm
- Instance PostgreSQL (lokal atau Supabase)

### Langkah demi Langkah

```bash
# 1. Clone repositori
git clone https://github.com/<username>/inventory-app.git
cd inventory-app

# 2. Install dependency PHP
composer install

# 3. Install dependency frontend
npm install

# 4. Copy file environment
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi database di .env
# DB_CONNECTION=pgsql
# DB_HOST=<supabase-host>
# DB_PORT=6543
# DB_DATABASE=postgres
# DB_USERNAME=<user>
# DB_PASSWORD=<password>

# 7. Migrate + seed data awal (roles, users, categories, products)
php artisan migrate:fresh --seed

# 8. Buat symlink storage (untuk upload gambar produk)
php artisan storage:link
```

### Menjalankan Aplikasi

```bash
# Terminal 1 — Laravel dev server
php artisan serve

# Terminal 2 — Vite HMR
npm run dev
```

Buka `http://localhost:8000`.

---

## 👥 Akun Uji

Setelah `php artisan migrate:fresh --seed` selesai, tersedia tiga akun uji:

| Role    | Email                    | Password   | Akses                                                      |
|---------|--------------------------|------------|------------------------------------------------------------|
| Admin   | admin@telkomsel.test     | `password` | Semua modul: dashboard, CRUD produk/kategori/user, laporan |
| Staff   | staff@telkomsel.test     | `password` | CRUD produk, peminjaman, dashboard                         |
| Manager | manager@telkomsel.test   | `password` | Read-only: dashboard, laporan, riwayat peminjaman          |

---

## 🔌 REST API

Base URL: `/api/v1`. Semua endpoint terlindungi Sanctum bearer token, kecuali `POST /login`.

### Login (Terbitkan Token)

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@telkomsel.test","password":"password","device_name":"postman"}'
```

Respons:

```json
{
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "user": { "id": 1, "name": "Admin", "email": "admin@telkomsel.test", "role": "admin" }
  },
  "message": "Login successful.",
  "status": 200
}
```

Sertakan token ke header pada request berikutnya: `Authorization: Bearer <token>`.

### Endpoint

| Method | URL                                      | Deskripsi                              |
|--------|------------------------------------------|----------------------------------------|
| POST   | `/api/v1/login`                          | Login & keluarkan Sanctum token        |
| POST   | `/api/v1/logout`                         | Revoke token aktif                     |
| GET    | `/api/v1/user`                           | Info user terautentikasi               |
| GET    | `/api/v1/products`                       | List produk (paginasi + filter)        |
| POST   | `/api/v1/products`                       | Buat produk                            |
| GET    | `/api/v1/products/{id}`                  | Detail produk                          |
| PUT    | `/api/v1/products/{id}`                  | Update produk                          |
| DELETE | `/api/v1/products/{id}`                  | Hapus produk                           |
| GET    | `/api/v1/borrowings`                     | List peminjaman                        |
| POST   | `/api/v1/borrowings`                     | Buat peminjaman (multi-item)           |
| GET    | `/api/v1/borrowings/{id}`                | Detail peminjaman                      |
| PUT    | `/api/v1/borrowings/{id}`                | Update peminjaman (notes / due_at)     |
| DELETE | `/api/v1/borrowings/{id}`                | Hapus peminjaman (bila sudah selesai)  |
| PATCH  | `/api/v1/borrowings/{id}/return`         | Proses pengembalian                    |
| GET    | `/api/v1/categories`                     | List kategori                          |
| GET    | `/api/v1/categories/{id}`                | Detail kategori                        |
| GET    | `/api/v1/dashboard/stats`                | Statistik dashboard (agregat)          |

Format respons konsisten:

```json
{
  "data": { ... },
  "message": "...",
  "status": 200
}
```

---

## 🧪 Testing

```bash
# Jalankan seluruh test
php artisan test --compact

# Jalankan satu file test
php artisan test --compact tests/Feature/ExampleTest.php

# Filter test berdasarkan nama
php artisan test --compact --filter=nama_test
```

Format kode PHP dengan Pint:

```bash
vendor/bin/pint --dirty --format agent
```

---

## 📸 Screenshots

_Placeholder — akan ditambahkan setelah deploy._

| Halaman                | Preview             |
|------------------------|---------------------|
| Login                  | _coming soon_       |
| Dashboard              | _coming soon_       |
| Manajemen Produk       | _coming soon_       |
| Peminjaman             | _coming soon_       |
| Laporan + Export PDF   | _coming soon_       |
| Dark Mode              | _coming soon_       |

---

## 🚢 Deployment (Railway)

1. Push repo ke GitHub.
2. Buat project baru di [Railway](https://railway.app) → **Deploy from GitHub repo**.
3. Set env vars di dashboard Railway:
   ```
   APP_KEY=<isi hasil php artisan key:generate --show>
   APP_URL=https://<railway-domain>
   DB_CONNECTION=pgsql
   DB_HOST=<supabase-pooler-host>
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=<user>
   DB_PASSWORD=<password>
   ```
4. Railway otomatis membaca `nixpacks.toml` (build) dan `Procfile` (start).
5. Setelah deploy pertama, jalankan `php artisan migrate --force` melalui shell Railway.

---

## 📁 Struktur Direktori Kunci

```
app/
├── Exports/InventoryExport.php          # Excel export (2 sheet)
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/                      # REST API v1
│   │   ├── BorrowingController.php
│   │   ├── CategoryController.php
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── ReportController.php         # PDF + Excel exporter
│   │   └── UserController.php
│   ├── Middleware/RoleMiddleware.php
│   ├── Requests/                        # Form Request validation
│   └── Resources/                       # Eloquent API Resources
├── Models/
│   ├── Borrowing.php
│   ├── BorrowingDetail.php
│   ├── Category.php
│   ├── Product.php
│   ├── Role.php
│   └── User.php
resources/
├── css/app.css
├── js/app.js
└── views/
    ├── borrowings/
    ├── categories/
    ├── products/
    ├── reports/
    │   ├── index.blade.php
    │   └── pdf.blade.php                # Template PDF DomPDF
    └── users/
routes/
├── api.php                              # Sanctum-protected /api/v1
└── web.php                              # Web (role-scoped)
database/
├── migrations/
└── seeders/
```

---

## 📄 Lisensi

Kode ini dibuat sebagai bagian dari proses seleksi magang PT Telkomsel dan tidak dimaksudkan untuk penggunaan komersial.
