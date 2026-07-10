# 🎓 Smart Class LMS (Laravel 13)

<p align="center">
  <img src="https://laravel.com/img/logotype.min.svg" alt="Laravel Logo" width="350">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38BDF8?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/SQLite-Database-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/Postman-API_Collection-FF6C37?style=for-the-badge&logo=postman&logoColor=white" alt="Postman">
  <img src="https://img.shields.io/badge/PHPUnit-Tests_Passed-4F5B93?style=for-the-badge&logo=php&logoColor=white" alt="PHPUnit">
</p>

---

## 📖 Tentang Proyek

**Smart Class LMS** adalah platform *Learning Management System* (LMS) modern berbasis hybrid yang menggabungkan kekuatan **RESTful API** skala produksi dengan **Frontend Web Portal** interaktif yang dibangun menggunakan Laravel Blade dan TailwindCSS.

Proyek ini dirancang sebagai portofolio kelas industri (*industry-ready portfolio*) dengan arsitektur yang sangat rapi, pemisahan logika bisnis yang bersih (*Service-Repository Pattern*), validasi input terisolasi (*Form Requests*), pemformatan respons terstandarisasi (*API Resources*), otorisasi berbasis kebijakan (*Laravel Policies*), serta perlindungan penuh melalui *Automated Testing*.

---

## 🚀 Fitur Utama & Keunggulan Arsitektur

### 1. Sistem Autentikasi Ganda (Dual-Authentication)
* **Web Session**: Login aman untuk Instruktur dan Siswa menggunakan sesi web standar.
* **REST API Token**: Autentikasi token *stateful* menggunakan **Laravel Sanctum** untuk mendukung integrasi dengan aplikasi mobile (React Native) di masa depan.

### 2. Manajemen Kursus Visual Penuh (CRUD Web)
* **Instruktur** dapat membuat, memperbarui, mengedit tingkat kesulitan, serta menghapus kursus langsung dari dashboard web.
* **Manajemen Bab Modul**: Form penambahan modul dinamis dan penghapusan modul yang aman.
* **Manajemen Materi (Lesson)**: Tambahkan materi baru dalam hitungan detik.

### 3. Pembuat Kuis & Tugas Interaktif
* **Kelola Pertanyaan Kuis**: Halaman khusus bagi Instruktur untuk membuat pertanyaan kuis pilihan ganda, memasukkan 4 opsi jawaban, dan memilih kunci jawaban yang benar secara visual.
* **Form Tugas Baru**: Buat tugas lengkap dengan deskripsi, nilai maksimal, serta batas waktu (*Due Date*).
* **Penilaian Otomatis & Manual**: Kuis dinilai secara instan saat dikirim oleh siswa. Tugas dievaluasi dan diberi *feedback* secara manual oleh instruktur.

### 4. Alur Belajar Siswa & Penerbitan Sertifikat Otomatis
* Siswa dapat menjelajahi katalog kelas, mendaftar (*enroll*) sekali klik, membaca materi, menandai materi selesai, dan memantau kemajuan belajar via *progress bar*.
* **Sertifikat Kelulusan**: Kode sertifikat unik diterbitkan secara otomatis setelah kemajuan belajar siswa mencapai 100% (format: `CERT-YYYYMMDD-[RANDOM]`).

---

## 🛠️ Prasyarat (Requirements)
* PHP >= 8.2 (Disarankan PHP 8.3+)
* Composer
* Node.js & NPM
* SQLite (Aktif secara default)

---

## 📦 Panduan Instalasi Lokal

1. **Clone & Masuk ke Direktori Proyek**
   ```bash
   git clone <URL_REPOSITORY_GITHUB_ANDA>
   cd lms-api
   ```

2. **Instal Dependensi Backend & Frontend**
   ```bash
   composer install
   npm install
   ```

3. **Salin & Konfigurasi Environment File**
   ```bash
   cp .env.example .env
   ```
   *Secara default, database menggunakan SQLite lokal yang sangat praktis dan tidak membutuhkan konfigurasi server database tambahan.*

4. **Jalankan Migrasi & Database Seeder**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
   *Perintah seeder di atas akan otomatis membuat data contoh kelas dan mendaftarkan 3 akun pengujian default:*
   * **Admin**: `admin@example.com` (password: `password`)
   * **Instruktur**: `instructor@example.com` (password: `password`)
   * **Siswa**: `student@example.com` (password: `password`)

5. **Hubungkan Symbolic Link Storage & Compile Asset**
   ```bash
   php artisan storage:link
   npm run build
   ```

6. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi Anda kini sudah siap diakses melalui browser di **`http://127.0.0.1:8000`**!

---

## 🧪 Pengujian Otomatis (Testing)
Fungsionalitas core (autentikasi, pendaftaran kelas, progres belajar, pengiriman kuis, dan penilaian tugas) dilindungi penuh oleh 26 skenario pengujian unit & fitur:

```bash
php artisan test
```

---

## 🗂️ Koleksi API Postman
Dokumentasi endpoint REST API lengkap (50+ endpoint) untuk integrasi mobile app telah disediakan dalam format JSON di file **`docs/LMS_API_Collection.json`**. Anda tinggal mengimpor file tersebut langsung ke Postman Anda.
