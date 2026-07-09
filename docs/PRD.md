# PRD — Learning Management System (LMS) Berbasis Laravel
**Proyek Portofolio Mandiri — Persiapan Lamaran Developer Intern (Fun Teacher Private)**
Disusun oleh: Bintang Darma Sakti | Telkom University Jakarta — Teknologi Informasi (S1) | Juli 2026

---

## 1. Ringkasan Eksekutif

Proyek ini adalah LMS (Learning Management System) berbasis Laravel yang dibangun sebagai portofolio mandiri untuk melamar posisi **Developer Intern (Laravel + React Native)** di **Fun Teacher Private (PT Cari Inovasi Teknologi)**, startup edutech bidang les privat.

LMS dipilih karena: (1) relevan dengan domain bisnis Fun Teacher Private, dan (2) cakupan fiturnya cukup luas untuk membuktikan seluruh kompetensi yang diminta di job description — Laravel, REST API + validasi, arsitektur siap-mobile, Git workflow, dan dokumentasi teknis.

---

## 2. Latar Belakang & Tujuan

### 2.1 Latar Belakang
Job description Fun Teacher Private membutuhkan: fitur kecil di web internal dengan Laravel, REST API (CRUD + validasi dasar), UI sederhana di React Native, integrasi API mobile (fetch/submit/handle error), bug fixing ringan, dokumentasi teknis, dan Git workflow (branching, commit, PR). CV pelamar saat ini kuat di Flutter/Dart dan analisis data, namun belum ada bukti proyek Laravel — inilah gap yang diisi proyek ini.

### 2.2 Tujuan Proyek
- **Portofolio**: proyek Laravel lengkap & terdokumentasi untuk dilampirkan di CV/lamaran.
- **Produk**: platform LMS fungsional — instruktur membuat kursus, siswa belajar & mengerjakan kuis/tugas, progres terpantau.
- **Teknis**: backend API-first yang siap dikonsumsi aplikasi mobile React Native tanpa perubahan besar — mencerminkan stack Fun Teacher Private.

---

## 3. Kesesuaian dengan Lowongan Magang

| Poin Job Description | Bukti / Modul pada Proyek LMS |
|---|---|
| Fitur kecil web internal dengan Laravel | Seluruh backend & panel web (admin, instruktur, siswa) dibangun end-to-end dengan Laravel |
| REST API (CRUD, validasi dasar) | API CRUD penuh untuk Course, Module, Lesson, Quiz, Enrollment dengan Form Request validation |
| UI sederhana React Native | API dirancang mobile-ready; stretch goal: 1 layar React Native konsumsi API |
| Integrasi API mobile (fetch, submit, handle error) | Format respons API konsisten (status, message, data, errors) |
| Bug fixing ringan | Feature test & unit test sebagai bukti kode yang dapat diuji |
| Dokumentasi teknis (API, workflow, README) | README lengkap, Postman collection, diagram alur |
| Git (branching, commit, PR) | GitHub Flow: branch per fitur, commit konsisten, PR (disimulasikan) |
| Koordinasi & review dev senior | Kode mengikuti PSR-12, struktur modular, mudah direview |

---

## 4. Target Pengguna & Peran

**Admin** — kelola pengguna (instruktur/siswa), kategori kursus, laporan platform.

**Instruktur** — buat/kelola kursus, modul, lesson, kuis, tugas; nilai tugas; pantau progres siswa.

**Siswa** — jelajah & enroll kursus, akses materi berurutan, kerjakan kuis/tugas, pantau progres, unduh sertifikat.

---

## 5. Ruang Lingkup

### 5.1 In-Scope (v1)
- Autentikasi role-based (Sanctum)
- CRUD Kursus, Kategori, Modul, Lesson
- Enrollment & progress tracking
- Kuis pilihan ganda (auto-grade)
- Tugas upload file (manual grade)
- Sertifikat otomatis (PDF)
- Dashboard admin & instruktur
- Notifikasi in-app/email dasar
- REST API penuh, siap mobile

### 5.2 Out-of-Scope (v1)
- Payment gateway
- Live streaming / video conference
- Chat real-time
- Implementasi penuh aplikasi React Native (API disiapkan; implementasi mobile opsional/stretch goal)

---

## 6. Kebutuhan Fungsional

### 6.1 Autentikasi & Otorisasi
- Registrasi siswa (self-register); akun instruktur dibuat/diundang admin
- Login menghasilkan token API (Sanctum)
- Role-based access control via middleware & policy
- Reset password via email
- Endpoint profil: lihat/update, ganti password

### 6.2 Manajemen Kursus
- Instruktur buat kursus: judul, deskripsi, kategori, thumbnail, level, status (draft/terbit)
- Kursus → Modul → Lesson (teks/video/dokumen)
- Urutan modul & lesson dapat diatur
- Admin dapat menonaktifkan kursus yang melanggar ketentuan

### 6.3 Pendaftaran & Progres
- Siswa enroll ke kursus berstatus terbit
- Sistem catat lesson yang sudah diselesaikan
- Progress % = (lesson selesai / total lesson) × 100
- Siswa lihat daftar kursus diikuti + status progres

### 6.4 Kuis & Penilaian Otomatis
- Instruktur buat kuis per modul (pilihan ganda, 1 jawaban benar)
- Siswa kerjakan kuis, skor otomatis saat submit
- Riwayat attempt (skor, waktu) tersimpan
- Instruktur lihat rekap nilai kuis seluruh siswa

### 6.5 Tugas (Assignment)
- Instruktur buat tugas: deskripsi, deadline, bobot nilai
- Siswa upload file jawaban sebelum deadline
- Instruktur beri nilai & feedback
- Siswa dapat notifikasi saat tugas dinilai

### 6.6 Sertifikat
- Sertifikat PDF otomatis saat progres 100% + nilai rata-rata memenuhi ambang minimum
- Memuat nama siswa, judul kursus, tanggal selesai, kode verifikasi unik
- Endpoint publik verifikasi sertifikat via kode

### 6.7 Dashboard & Laporan
- Admin: total pengguna, total kursus, kursus terpopuler, avg completion rate
- Instruktur: jumlah siswa per kursus, avg nilai kuis, tugas belum dinilai
- Siswa: kursus aktif, progres, tugas/kuis mendatang

### 6.8 Notifikasi
- In-app (tabel `notifications`): enroll berhasil, tugas dinilai, kursus baru, sertifikat terbit
- Email opsional via Laravel Notification + Queue

### 6.9 Lapisan REST API (API-First)
- Semua fitur diekspos via REST API terdokumentasi
- Format respons konsisten:
  - Sukses: `{ "success": true, "message": "...", "data": {...} }`
  - Gagal: `{ "success": false, "message": "...", "errors": {...} }`
- Auth via Bearer Token (Sanctum)
- Validasi via Form Request, pesan error Bahasa Indonesia

---

## 7. Kebutuhan Non-Fungsional

| Aspek | Ketentuan |
|---|---|
| Keamanan | Password bcrypt, token Sanctum, validasi semua endpoint, rate limiting login, Policy/Gate per resource |
| Performa | Eager loading (hindari N+1), pagination semua listing API, index pada FK & kolom pencarian |
| Skalabilitas | Proses berat (email, generate PDF sertifikat) lewat Queue |
| Maintainability | PSR-12, struktur modular (Controllers/Api & Web terpisah), Form Request & API Resource |
| Dokumentasi | README lengkap + koleksi Postman/OpenAPI |
| Testing | Feature test alur utama (auth, enrollment, kuis, tugas) via PHPUnit/Pest |
| Portabilitas | Jalan di lokal (Sail/XAMPP) & bisa dideploy ke Railway/Render |

---

## 8. Tech Stack

| Lapisan | Teknologi | Keterangan |
|---|---|---|
| Backend | Laravel 11.x, PHP 8.2+ | Struktur utama, routing, Eloquent |
| Database | MySQL 8 / MariaDB | Relasi kursus, pengguna, progres |
| Auth API | Laravel Sanctum | Token-based auth SPA/mobile |
| Frontend Panel | Blade + Tailwind CSS (Livewire opsional) | Panel admin/instruktur/siswa |
| Dokumentasi API | Postman Collection / L5-Swagger | Referensi endpoint |
| Testing | PHPUnit / Pest | Feature test alur utama |
| Queue & Notifikasi | Laravel Queue (database driver) | Email, generate sertifikat |
| Storage | Laravel Filesystem (local/S3) | Thumbnail, materi, tugas |
| Version Control | Git + GitHub (GitHub Flow) | Branch per fitur, PR |
| Deployment (opsional) | Railway / Render / VPS | Demo publik untuk CV |

---

## 9. Arsitektur Aplikasi

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          -> Controller khusus REST API (V1)
│   │   └── Web/          -> Controller panel Blade
│   ├── Requests/         -> Form Request (validasi per endpoint)
│   ├── Resources/        -> API Resource (format respons JSON)
│   └── Middleware/       -> Role/permission middleware
├── Models/               -> Eloquent models & relasi
├── Policies/             -> Otorisasi per-resource
├── Services/             -> Business logic (hitung progres, generate sertifikat)
└── Notifications/        -> Notifikasi in-app & email

routes/
├── web.php               -> Rute panel Blade
└── api.php               -> Rute REST API (prefix /api/v1)
```

Logika bisnis inti (perhitungan progres, penilaian kuis, penerbitan sertifikat) diletakkan di **Service class** agar dipakai bersama oleh Controller Web maupun API — menghindari duplikasi & memudahkan code review.

---

## 10. Skema Basis Data

| Tabel | Kolom Utama | Relasi |
|---|---|---|
| users | id, name, email, password, role (admin/instructor/student), avatar | hasMany courses (sbg instruktur), hasMany enrollments |
| categories | id, name, slug | hasMany courses |
| courses | id, instructor_id, category_id, title, slug, description, thumbnail, level, status | belongsTo instructor, belongsTo category, hasMany modules, hasMany enrollments |
| modules | id, course_id, title, order | belongsTo course, hasMany lessons, hasMany quizzes |
| lessons | id, module_id, title, content_type, content, order | belongsTo module, hasMany lesson_progress |
| enrollments | id, user_id, course_id, enrolled_at, progress_percent, completed_at | belongsTo user, belongsTo course |
| lesson_progress | id, enrollment_id, lesson_id, completed_at | belongsTo enrollment, belongsTo lesson |
| quizzes | id, module_id, title, passing_score | belongsTo module, hasMany questions |
| quiz_questions | id, quiz_id, question_text | belongsTo quiz, hasMany options |
| quiz_options | id, question_id, option_text, is_correct | belongsTo question |
| quiz_attempts | id, quiz_id, user_id, score, submitted_at | belongsTo quiz, belongsTo user |
| assignments | id, module_id, title, description, due_date, max_score | belongsTo module, hasMany submissions |
| assignment_submissions | id, assignment_id, user_id, file_path, score, feedback, submitted_at, graded_at | belongsTo assignment, belongsTo user |
| certificates | id, user_id, course_id, certificate_code, issued_at, file_path | belongsTo user, belongsTo course |
| notifications | id, user_id, type, data (json), read_at | belongsTo user (Laravel Notification bawaan) |

---

## 11. Spesifikasi Endpoint REST API

Prefix: `/api/v1`. Semua endpoint (kecuali login/register/verifikasi sertifikat) butuh header `Authorization: Bearer {token}`.

### 11.1 Autentikasi
| Method | Endpoint | Deskripsi | Akses |
|---|---|---|---|
| POST | /auth/register | Registrasi akun siswa baru | Publik |
| POST | /auth/login | Login & terima token | Publik |
| POST | /auth/logout | Logout & revoke token | Semua role |
| GET | /auth/me | Profil pengguna login | Semua role |
| PUT | /auth/profile | Update profil | Semua role |

### 11.2 Kursus & Konten
| Method | Endpoint | Deskripsi | Akses |
|---|---|---|---|
| GET | /courses | Daftar kursus (pagination, filter) | Publik/Semua role |
| POST | /courses | Buat kursus baru | Instruktur |
| GET | /courses/{id} | Detail kursus + modul & lesson | Publik/Semua role |
| PUT | /courses/{id} | Update kursus | Instruktur pemilik |
| DELETE | /courses/{id} | Hapus kursus | Instruktur pemilik/Admin |
| POST | /courses/{id}/modules | Tambah modul | Instruktur pemilik |
| POST | /modules/{id}/lessons | Tambah lesson | Instruktur pemilik |
| PUT | /lessons/{id} | Update lesson | Instruktur pemilik |

### 11.3 Pendaftaran & Progres
| Method | Endpoint | Deskripsi | Akses |
|---|---|---|---|
| POST | /courses/{id}/enroll | Daftar ke kursus | Siswa |
| GET | /my-courses | Kursus diikuti + progres | Siswa |
| POST | /lessons/{id}/complete | Tandai lesson selesai | Siswa |
| GET | /courses/{id}/progress | Detail progres siswa | Siswa/Instruktur |

### 11.4 Kuis & Tugas
| Method | Endpoint | Deskripsi | Akses |
|---|---|---|---|
| POST | /modules/{id}/quizzes | Buat kuis + pertanyaan | Instruktur |
| GET | /quizzes/{id} | Lihat soal (tanpa jawaban benar) | Siswa |
| POST | /quizzes/{id}/submit | Submit jawaban, skor otomatis | Siswa |
| POST | /modules/{id}/assignments | Buat tugas | Instruktur |
| POST | /assignments/{id}/submit | Upload jawaban tugas | Siswa |
| PUT | /submissions/{id}/grade | Nilai & feedback tugas | Instruktur |

### 11.5 Sertifikat & Lainnya
| Method | Endpoint | Deskripsi | Akses |
|---|---|---|---|
| GET | /certificates | Daftar sertifikat siswa | Siswa |
| GET | /certificates/verify/{code} | Verifikasi sertifikat | Publik |
| GET | /notifications | Notifikasi pengguna | Semua role |
| GET | /dashboard | Statistik ringkas sesuai role | Semua role |

---

## 12. Alur Pengguna Utama

### 12.1 Alur Siswa
1. Registrasi/Login
2. Jelajahi katalog & buka detail kursus
3. Enroll ke kursus
4. Akses lesson berurutan, tandai selesai
5. Kerjakan kuis akhir modul, dapat skor otomatis
6. Upload tugas sebelum deadline, tunggu penilaian
7. Pantau progres via dashboard
8. Unduh sertifikat setelah 100% selesai

### 12.2 Alur Instruktur
1. Login ke panel instruktur
2. Buat kursus + modul + lesson
3. Tambah kuis/tugas per modul
4. Terbitkan kursus
5. Pantau siswa & progres
6. Nilai tugas & beri feedback

---

## 13. Rencana Pengerjaan (Milestone)

| Minggu | Fokus | Deliverable |
|---|---|---|
| 1 | Setup & Fondasi | Instalasi Laravel, migration & model semua entitas, Sanctum, seeder dummy |
| 2 | Modul Kursus & Konten | CRUD Kategori/Kursus/Modul/Lesson (web & API) + upload file |
| 3 | Enrollment & Progres | Enroll, tandai lesson selesai, hitung progres otomatis, dashboard siswa |
| 4 | Kuis & Tugas | CRUD kuis + auto-grade, CRUD tugas + upload & grade manual |
| 5 | Sertifikat, Notifikasi & Dashboard | Generate PDF, notifikasi in-app, dashboard admin/instruktur |
| 6 | Testing, Dokumentasi & Deployment | Feature test, Postman collection, README, deploy demo, video demo |

---

## 14. Kriteria Keberhasilan (Definition of Done)
- Semua endpoint Bagian 11 berjalan & teruji manual via Postman
- Minimal 70% alur utama (auth, kursus, enrollment, kuis, tugas) punya feature test lulus
- README mencakup: deskripsi, tech stack, instalasi, migrasi/seeder, cara test, link Postman collection
- Riwayat commit Git rapi (GitHub Flow: branch per fitur, commit deskriptif, minimal 1 simulasi PR)
- Aplikasi dapat diakses via demo online (sangat direkomendasikan)
- Setiap fitur dapat dikaitkan ke satu poin job description (lihat Bagian 3)

---

## 15. Rekomendasi Langkah Selanjutnya
- Deploy demo ke Railway/Render agar reviewer bisa coba langsung
- Buat video demo singkat (2–3 menit): alur siswa & instruktur, sematkan di README
- Tulis case study singkat di GitHub profile README/LinkedIn
- Cantumkan link repo & demo di CV/email lamaran, subjek "Developer Intern - Bintang Darma Sakti"
