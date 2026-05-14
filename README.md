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

## Struktur Direktori

- `app/`: Berisi inti aplikasi (Controllers, Models, Views, Middleware, dll).
  - `Core/`: Core library framework (Router, Request, Response, Database).
  - `Repositories/`: Menangani query database kompleks.
  - `Services/`: Menangani logika bisnis.
- `public/`: Document root yang dapat diakses publik (index.php, CSS, JS, Gambar).
- `routes/`: Definisi routing aplikasi (`web.php`).
- `storage/`: Tempat penyimpanan file log, dll.
- `config/`: Konfigurasi tambahan aplikasi (opsional).
