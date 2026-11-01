# SILAPOR — Sistem Informasi Pengambilan & Pengembalian Laptop

**SILAPOR** adalah aplikasi web modern berbasis **Laravel 13 (REST API)** dan **Vue 3 (Vite + Tailwind CSS v4)** untuk mengelola siklus harian pengambilan pagi, pemantauan peminjaman, pengembalian siang, pendataan mandiri laptop peserta didik, serta pengiriman notifikasi pengingat WhatsApp otomatis kepada orang tua / wali murid.

---

## ✨ Fitur Utama

### 1. Pendataan Mandiri Laptop Peserta Didik (`/form-laptop`)
- **Form Publik Tanpa Login**: Peserta didik dapat mendaftarkan atau memperbarui data perangkat laptop miliknya secara mandiri melalui halaman `/form-laptop`.
- **Filter Search Option Siswa**: Pencarian nama siswa, NIS, atau kelas secara *real-time*.
- **Otomatisasi Kode Aset**: Sistem otomatis menautkan perangkat ke siswa terkait (`LAP-{NIS}`) sehingga saat sesi pagi dipindai, laptop milik siswa otomatis terpilih.

### 2. Sesi Pagi — Scan Pengambilan Laptop (`/pickup`)
- **Dual Mode Scanner**: Mendukung *2D USB Barcode/QR Scanner* (input otomatis fokus) maupun *Web Camera QR Scanner* (`html5-qrcode`) melalui kamera laptop/smartphone.
- **Pencarian Fleksibel**: Mendukung pemindaian token QR Code unik siswa maupun pengetikan langsung **NIS** siswa.
- **Auto-Link Perangkat Siswa**: Otomatis menggunakan laptop milik siswa yang terdaftar tanpa perlu mengetik kode aset secara manual.

### 3. Sesi Siang — Scan Pengembalian Laptop (`/return`)
- **Verifikasi Pengembalian Cepat**: Memvalidasi transaksi pinjaman aktif siswa pada hari berjalan.
- **Pencatatan Kondisi Fisik**: Mencatat kondisi perangkat saat dikembalikan (`Baik / Normal`, `Ada Kendala / Rusak`, atau `Hilang`) beserta catatan opsional petugas.

### 4. Dashboard Bento & Pemantauan Real-Time (`/dashboard` & `/monitoring`)
- **Ringkasan Eksekutif Harian**: Statistik *real-time* jumlah laptop diambil pagi ini, sudah dikembalikan, belum kembali, serta status keterlambatan (*overdue*).
- **Filter Riwayat Harian**: Memantau seluruh aktivitas transaksi berdasarkan tanggal dengan status badge yang jelas.

### 5. Notifikasi Pengingat WhatsApp Wali Murid (`/notifications`)
- **Deteksi Keterlambatan (*Overdue Check*)**: Memeriksa siswa yang belum mengembalikan laptop melewati batas jadwal sesi siang.
- **Integrasi WhatsApp Gateway (Fonnte)**: Mengirimkan pesan pengingat otomatis ke nomor WhatsApp wali murid, lengkap dengan log status pengiriman (`SENT`, `FAILED`, `PENDING`) dan fitur **Kirim Ulang (Retry)**.

### 6. Manajemen Data Master & Kartu QR Siswa
- **Data Peserta Didik & Cetak Kartu QR (`/students`)**: Kelola data siswa, regenerasi token QR keamanan, serta cetak langsung kartu identitas QR siswa siap tempel.
- **Data Aset Laptop (`/laptops`)**: Pantau daftar perangkat laptop milik siswa beserta status ketersediaannya (`Tersedia`, `Dipinjam`, `Perbaikan`).
- **Data Kelas (`/classes`), Wali Murid (`/parents`), & Jadwal Sesi (`/schedules`)**: Pengaturan struktur akademik dan jadwal operasional harian.

### 7. Desain Modern Humanist & Responsif
- **Warm Minimalist Aesthetics**: Menggunakan palet *Warm Alabaster* (`#FAF8F5`), *Deep Teal* (`#0F766E`), dan tipografi `Plus Jakarta Sans` + `JetBrains Mono`.
- **Multi-Device Responsive**: Tampilan adaptif untuk **Desktop**, **Tablet**, dan **Mobile** (dilengkapi *slide-over navigation drawer* dan *sticky top bar*).
- **Smooth GPU Animations**: Transisi halaman, modal, *alert banner*, dan *dropdown* yang halus serta ringan menggunakan Vue `<Transition>`.

---

## 🛠️ Tech Stack

| Layer | Teknologi |
| :--- | :--- |
| **Backend API** | PHP 8.3+, Laravel 13, Laravel Sanctum (Token Auth), Endroid QR Code |
| **Frontend SPA** | Vue 3 (Composition API), Vite 8, Pinia (State Management), Vue Router 5 |
| **Styling & UI** | Tailwind CSS v4, Plus Jakarta Sans & JetBrains Mono, Custom GPU Transitions |
| **QR Scanner** | `html5-qrcode` (Kamera Web/Mobile) & Hardware 2D Barcode/QR Scanner |
| **Database** | MySQL / MariaDB (mendukung juga SQLite) |
| **WhatsApp API** | Fonnte WhatsApp Gateway (dengan *fallback log mode* untuk pengembangan lokal) |

---

## 📂 Struktur Direktori

```text
silapor/
├── backend/                  # Laravel 13 REST API Backend
│   ├── app/
│   │   ├── Console/Commands/ # Command pengecekan keterlambatan (overdue)
│   │   ├── Http/Controllers/ # API Controllers (Auth, Pickup, Return, Laptop, dll.)
│   │   ├── Models/           # Eloquent Models (Student, Laptop, Loan, ReturnRecord, dll.)
│   │   └── Services/         # QrCodeService & WhatsAppService
│   ├── database/
│   │   ├── migrations/       # Skema tabel database
│   │   └── seeders/          # DatabaseSeeder (Akun default, kelas, siswa & QR awal)
│   └── routes/
│       └── api.php           # Definisi endpoint REST API
│
└── frontend/                 # Vue 3 + Vite + Tailwind CSS v4 SPA
    ├── src/
    │   ├── assets/           # Global CSS (main.css) & konfigurasi animasi
    │   ├── components/       # Komponen Layout, Scanner Kamera, & StatusBadge
    │   ├── router/           # Konfigurasi rute & navigation guard
    │   ├── services/         # Axios instance & interceptor autentikasi
    │   ├── stores/           # Pinia stores (auth, monitoring)
    │   └── views/            # Halaman utama (Dashboard, Scanner, Form Siswa, Master Data)
    └── package.json
```

---

## 🚀 Panduan Instalasi & Menjalankan Project

### Prasyarat Sistem
- **PHP** >= 8.3 (beserta ekstensi `pdo_mysql`, `gd`, `mbstring`, `openssl`)
- **Composer** >= 2.x
- **Node.js** >= 22.18.0 (atau >= 24.12.0) & **NPM**
- **MySQL / MariaDB** (например melalui Laragon / XAMPP)

---

### 1. Konfigurasi Backend (Laravel)

Buka terminal dan masuk ke direktori `backend`:

```bash
cd backend
```

1. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

2. **Salin file environment dan buat Application Key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Atur konfigurasi database di file `backend/.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=silapor
   DB_USERNAME=root
   DB_PASSWORD=

   # Opsional: Token Fonnte untuk integrasi WhatsApp
   FONNTE_TOKEN=your_fonnte_api_token_here
   ```

4. **Jalankan migrasi database beserta data awal (*seeder*) dan buat *storage link*:**
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Jalankan server pengembangan Laravel:**
   ```bash
   php artisan serve --port=8000
   ```
   Backend API akan aktif di `http://localhost:8000/api`.

---

### 2. Konfigurasi Frontend (Vue 3 + Vite)

Buka terminal baru dan masuk ke direktori `frontend`:

```bash
cd frontend
```

1. **Instal dependensi Node.js:**
   ```bash
   npm install
   ```

2. **Konfigurasi URL API (Opsional):**
   Secara default, frontend terhubung ke `http://localhost:8000/api`. Jika menggunakan URL berbeda, buat file `frontend/.env`:
   ```env
   VITE_API_URL=http://localhost:8000/api
   ```

3. **Jalankan server pengembangan Vite:**
   ```bash
   npm run dev
   ```
   Aplikasi frontend dapat diakses melalui browser di `http://localhost:5173`.

4. **Build untuk Produksi:**
   ```bash
   npm run build
   ```

---

## 🔐 Akun Demo Default

Setelah menjalankan `php artisan migrate:fresh --seed`, Anda dapat masuk menggunakan akun berikut:

| Peran | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@silapor.com` | `password` | Akses penuh (Dashboard, Scanner, Monitoring, Notifikasi WA, & Seluruh Data Master) |
| **Petugas Lab** | `petugas@silapor.com` | `password` | Operasional harian (Dashboard, Scan Pengambilan, Scan Pengembalian, Monitoring, & Form Laptop) |

> **Catatan:** Peserta didik tidak memerlukan login untuk mengisi data laptop mereka—cukup akses langsung melalui menu **Form Laptop Siswa** (`/form-laptop`) di halaman depan.

---

## 🔌 Ringkasan Endpoint API Utama

| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/auth/login` | Publik | Login petugas / administrator |
| `GET` | `/api/public/students` | Publik | Daftar siswa untuk pencarian di form laptop mandiri |
| `POST` | `/api/public/laptop-form` | Publik | Simpan / perbarui data laptop milik peserta didik |
| `GET` | `/api/dashboard/stats` | Auth | Statistik harian & daftar peminjaman aktif hari ini |
| `POST` | `/api/pickups/scan` | Auth | Catat pengambilan laptop sesi pagi (via QR Token / NIS) |
| `POST` | `/api/returns/scan` | Auth | Catat pengembalian laptop sesi siang (via QR Token / NIS) |
| `POST` | `/api/notifications/check-overdue` | Auth | Cek pinjaman terlambat & kirim pengingat WhatsApp ke wali murid |
| `POST` | `/api/students/{id}/generate-qr` | Auth | Regenerasi token & gambar QR Code kartu siswa |

---

## 📄 Lisensi

Project **SILAPOR** ini dikembangkan untuk mendukung digitalisasi tata kelola inventaris dan ketertiban penggunaan perangkat belajar siswa di lingkungan sekolah. Dibuat dengan lisensi [MIT](https://opensource.org/licenses/MIT).

