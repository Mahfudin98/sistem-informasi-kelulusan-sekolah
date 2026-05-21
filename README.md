# App Kelulusan - PHP Native Modern MVC

Sistem Pengecekan Kelulusan Online yang dibangun menggunakan arsitektur MVC (Model-View-Controller) dengan PHP Native yang modern, scalable, dan clean.

## Fitur Utama

- **Clean Architecture:** Struktur folder yang rapi seperti framework modern (Controller, Model, View, Repository, Service, Middleware).
- **Keamanan:** Dilengkapi CSRF Protection, PDO Prepared Statements (mencegah SQL Injection), Session Management yang aman, dan Password Hashing.
- **Performa & Skalabilitas:** Menggunakan Repository & Service pattern untuk memisahkan logic database dan business rules dari Controller.
- **Desain Modern:** Menggunakan desain UI/UX yang modern dengan gaya Glassmorphism, responsif untuk semua perangkat, dan dilengkapi animasi yang smooth.
- **Routing:** Sistem routing yang powerful dengan dukungan parameter dan middleware group.

## Persyaratan Sistem

- PHP 8.2 atau lebih baru
- MySQL / MariaDB
- Composer (untuk autoload dan environment management)

## Instalasi

1. **Clone/Download** repository ini.
2. Buka terminal di direktori proyek dan jalankan perintah:
   ```bash
   composer install
   ```
3. **Build CSS:**
   ```bash
   npm install
   npm run build
   ```
4. Sesuaikan konfigurasi database di file `.env`. Pastikan database MySQL sudah berjalan.
5. Jalankan script setup database untuk membuat tabel dan akun admin default:
   ```bash
   php database/setup_db.php
   ```
6. Jalankan development server lokal:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Buka browser dan akses `http://localhost:8000`.

## Akses Admin

- **URL:** `http://localhost:8000/login`
- **Username:** `admin`
- **Password:** `admin123`
- **Catatan:** Segera ubah password setelah login pertama kali untuk keamanan.

## Pengujian (Testing)

Aplikasi ini menggunakan **Pest Framework** untuk pengujian otomatis.

1. **Persiapan Database Testing (Opsional tapi Disarankan):**
   - Buat database baru bernama `nama_database_test` (sesuai nama di `.env` ditambah suffix `_test`).
   - Aktifkan konfigurasi database test di `tests/bootstrap.php`.

2. **Menjalankan Tes:**
   ```bash
   composer test
   ```

3. **Struktur Tes:**
   - `tests/Unit`: Berisi pengujian unit untuk service dan logika bisnis murni.
   - `tests/Feature`: Berisi pengujian fitur untuk alur proses (seperti login, CRUD, dll).

## Struktur Direktori

- `app/`: Berisi inti aplikasi (Controllers, Models, Views, Middleware, dll).
  - `Core/`: Core library framework (Router, Request, Response, Database).
  - `Repositories/`: Menangani query database kompleks.
  - `Services/`: Menangani logika bisnis.
- `public/`: Document root yang dapat diakses publik (index.php, CSS, JS, Gambar).
- `routes/`: Definisi routing aplikasi (`web.php`).
- `storage/`: Tempat penyimpanan file log, dll.
- `config/`: Konfigurasi tambahan aplikasi (opsional).
## Manajemen Lisensi (Untuk Penjual)

Aplikasi ini dilengkapi dengan sistem proteksi lisensi. Source code aplikasi **App Kelulusan** telah dipisahkan dari alat pembuat lisensinya (Keygen) demi keamanan.

### Persiapan Keamanan:
Sebelum menjual source code aplikasi ini, **UBAH KATA SANDI RAHASIA ANDA**:
1. Buka file `app/Services/LicenseService.php`.
2. Ubah isi variabel `SECRET_SALT` (contoh: `PerusahaanSaya_2025_Un1qu3`).
3. Ingat baik-baik `SECRET_SALT` ini, karena Anda akan membutuhkannya saat men-generate kunci untuk pembeli.

### Cara Generate Lisensi untuk Pembeli Baru:
> **PENTING:** Alat pembuat lisensi berada di luar folder aplikasi ini, tepatnya di folder `keygen-kelulusan`. JANGAN PERNAH memberikan folder `keygen-kelulusan` kepada pembeli!

1. Buka terminal/command prompt.
2. Masuk ke folder pembuat lisensi Anda:
   ```bash
   cd c:\Users\mahfu\OneDrive\Desktop\Project\keygen-kelulusan
   ```
3. Jalankan aplikasi Keygen:
   ```bash
   php generate.php
   ```
4. Jawab pertanyaan di terminal:
   - Masukkan `Secret Salt` yang sama persis dengan yang Anda tulis di `LicenseService.php`.
   - Masukkan nama **Domain** sekolah pembeli (contoh: `smk1-jakarta.sch.id`).
5. Salin kode LISENSI panjang yang dihasilkan oleh terminal.
6. Berikan kode tersebut kepada pembeli.

### Cara Pembeli Mengaktifkan Aplikasi:
Aplikasi ini sudah dilengkapi dengan **Sistem Instalasi Otomatis (Auto-Installer)**. Pembeli **TIDAK PERLU** lagi menyentuh kode, membuat file `.env`, atau mengeksekusi script database secara manual.

1. Pembeli cukup mengunggah *source code* ke server/hosting mereka.
2. Buat sebuah database kosong di MySQL/MariaDB (misal: `app_kelulusan`).
3. Saat pembeli membuka website mereka untuk pertama kalinya, mereka akan otomatis dialihkan ke halaman **Setup Instalasi** (`/setup`).
4. Di halaman tersebut, pembeli tinggal mengisi:
   - Nama & URL Aplikasi
   - Kredensial Database (Host, User, Password, Nama DB)
   - **License Key** yang sudah Anda berikan.
5. Klik **Install Aplikasi Sekarang**. 
6. Sistem akan otomatis menghubungkan database, menjalankan semua migrasi tabel, dan menyimpan konfigurasi. Jika lisensi valid, aplikasi akan langsung terbuka secara permanen!

> **PENTING:** Kode lisensi secara ketat mendeteksi nama domain (termasuk *subdomain*). Jika lisensi dibuat untuk `sekolah.sch.id`, maka tidak akan bisa dipakai di `app.sekolah.sch.id`. Anda harus membuat kunci yang persis sama dengan domain yang digunakan pembeli.
