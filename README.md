# 🎓 LMS RESTful API (Laravel 11)

LMS (Learning Management System) RESTful API yang dibangun menggunakan Laravel 11 dan Sanctum sebagai sistem autentikasi token. Proyek ini dirancang sebagai portofolio backend edutech dengan arsitektur yang rapi, validasi input terisolasi (Form Requests), pemformatan respons terstandarisasi (API Resources), otorisasi ketat (Policies), dan enkapsulasi logika bisnis (Service Classes).

---

## 🚀 Fitur Utama
1. **Autentikasi API**: Registrasi, login, logout, info profil, dan pembaruan profil & kata sandi terproteksi Sanctum.
2. **Katalog Kursus & Konten**: CRUD Kursus, Modul, dan Pelajaran (Lesson) lengkap dengan fitur unggah file video/dokumen (max 20MB) serta penjelajahan kursus dengan filter kategori dan pencarian teks.
3. **Pendaftaran & Progres Belajar**: Siswa dapat mendaftar (enroll) ke kursus yang dipublikasikan, menandai materi pelajaran selesai, serta mendapatkan perhitungan progres persen secara real-time.
4. **Kuis Pilihan Ganda (PG)**: Instruktur dapat membuat kuis berstruktur bersarang (Kuis -> Pertanyaan -> Opsi Jawaban), kunci jawaban otomatis disembunyikan untuk siswa, dan penilaian kuis dilakukan secara otomatis saat disubmit.
5. **Manajemen Tugas**: Instruktur membuat tugas dengan deadline dan bobot nilai tertentu. Siswa mengumpulkan berkas tugas sebelum deadline, dan instruktur dapat memberikan penilaian manual beserta umpan balik.
6. **Sertifikat Kelulusan**: Menerbitkan sertifikat otomatis dengan kode sertifikat unik (format: `CERT-YYYYMMDD-[A-Z4]`) saat progres siswa mencapai 100%, lengkap dengan file mock PDF.
7. **Statistik Dashboard**: Ringkasan data analitik terpisah untuk peran Administrator (Platform Stats) dan Instruktur (Kursus & Siswa Stats).

---

## 🛠️ Prasyarat (Requirements)
* PHP >= 8.2
* Composer
* MySQL / MariaDB

---

## 📦 Instalasi & Konfigurasi

1. **Clone & Masuk ke Direktori Proyek**
   ```bash
   cd "c:\Users\DELL\OneDrive\Documents\Laravel LMS\lms-api"
   ```

2. **Instal Dependensi PHP**
   ```bash
   composer install
   ```

3. **Salin & Konfigurasi Environment File**
   ```bash
   cp .env.example .env
   ```
   *Buka file `.env` lalu sesuaikan konfigurasi database Anda:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_lms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi & Database Seeder**
   ```bash
   php artisan migrate --seed
   ```
   *Perintah ini akan membuat semua tabel dan mendaftarkan 3 akun pengujian default:*
   * **Admin**: `admin@example.com` (password: `password`)
   * **Instruktur**: `instructor@example.com` (password: `password`)
   * **Siswa**: `student@example.com` (password: `password`)

6. **Hubungkan Symbolic Link Storage**
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   API akan berjalan di `http://127.0.0.1:8000`.

---

## 🧪 Menjalankan Pengujian (Testing)
Seluruh fungsionalitas utama dilindungi oleh 26 skenario pengujian unit & fitur (PHPUnit):

```bash
php artisan test
```

---

## 📖 Dokumentasi Endpoint API (`/api/v1`)

### 1. Autentikasi (`/auth`)
| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| **POST** | `/auth/register` | Publik | Mendaftarkan akun siswa baru |
| **POST** | `/auth/login` | Publik | Login mendapatkan token Sanctum |
| **POST** | `/auth/logout` | Token | Menghapus token aktif (Logout) |
| **GET** | `/auth/me` | Token | Mengambil info profil user aktif |
| **PUT** | `/auth/profile` | Token | Memperbarui profil & kata sandi |

### 2. Modul Kursus & Konten (`/courses`, `/categories`, `/modules`, `/lessons`)
| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| **GET** | `/categories` | Publik | Mendapatkan katalog kategori |
| **GET** | `/courses` | Publik | Daftar kursus published (Filter & Search) |
| **GET** | `/courses/{id}` | Publik | Detail lengkap kursus + modul + lesson |
| **POST** | `/courses` | Instruktur | Membuat kursus baru |
| **PUT** | `/courses/{id}` | Instruktur | Memperbarui kursus miliknya |
| **DELETE**| `/courses/{id}` | Instruktur | Menghapus kursus miliknya |
| **POST** | `/courses/{id}/modules`| Instruktur | Membuat modul baru di bawah kursus |
| **POST** | `/modules/{id}/lessons`| Instruktur | Membuat lesson baru (Teks / File Media) |
| **PUT** | `/lessons/{id}` | Instruktur | Memperbarui lesson |

### 3. Pendaftaran & Progres (`/enroll`, `/my-courses`, `/progress`)
| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| **POST** | `/courses/{id}/enroll` | Siswa | Mendaftar ke kursus (Status published) |
| **GET** | `/my-courses` | Siswa | Daftar pendaftaran kursus siswa aktif |
| **POST** | `/lessons/{id}/complete` | Siswa | Menandai lesson selesai & update progres |
| **GET** | `/courses/{id}/progress` | Siswa/Instruktur | Breakdown progres per materi pelajaran |

### 4. Kuis & Tugas (`/quizzes`, `/assignments`, `/submissions`)
| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| **POST** | `/modules/{id}/quizzes` | Instruktur | Membuat kuis + pertanyaan + pilihan PG |
| **GET** | `/quizzes/{id}` | Siswa/Semua | Mengambil soal kuis (Tanpa kunci jawaban) |
| **POST** | `/quizzes/{id}/submit` | Siswa | Mengirim jawaban kuis (Auto scoring) |
| **POST** | `/modules/{id}/assignments`| Instruktur | Membuat tugas baru dengan deadline |
| **POST** | `/assignments/{id}/submit`| Siswa | Mengunggah kiriman file jawaban tugas |
| **PUT** | `/submissions/{id}/grade`| Instruktur | Memberikan nilai & feedback manual |

### 5. Sertifikat & Dashboard (`/certificates`, `/dashboard`)
| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| **GET** | `/certificates` | Siswa | Daftar sertifikat kelulusan pribadi |
| **GET** | `/certificates/verify/{code}` | Publik | Verifikasi status kode sertifikat |
| **GET** | `/dashboard/instructor`| Instruktur | Statistik data analitik kursus & siswa |
| **GET** | `/dashboard/admin` | Admin | Statistik platform data analitik global |
