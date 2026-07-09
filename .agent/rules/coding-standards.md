# Project Rules — LMS Laravel Portfolio Project

> Simpan file ini di `.agent/rules/coding-standards.md` di dalam project folder.
> Antigravity otomatis memuat isi file ini ke setiap sesi chat pada project ini,
> jadi kamu tidak perlu mengulang aturan ini di setiap prompt.

## Konteks Produk
Proyek portofolio: Learning Management System (LMS) berbasis Laravel 11, dengan
arsitektur API-first agar siap dikonsumsi aplikasi mobile React Native di masa
depan. Tujuan: melamar posisi Developer Intern (Laravel + React Native) di
startup edutech. Lihat `PRD.md` di root project untuk detail fitur lengkap.

## Tech Stack Wajib
- Laravel 11.x, PHP 8.2+, MySQL.
- Autentikasi API: Laravel Sanctum (token-based).
- Panel web: Blade + Tailwind CSS (Livewire boleh jika lebih efisien).
- Testing: PHPUnit atau Pest.
- Queue: database driver untuk proses generate sertifikat & notifikasi.

## Konvensi Kode
- Ikuti standar PSR-12 untuk seluruh kode PHP.
- Gunakan Form Request class untuk **semua** validasi input API maupun web.
- Gunakan API Resource class untuk memformat **semua** respons JSON.
- Logika bisnis inti (hitung progres, penilaian kuis otomatis, generate
  sertifikat) wajib diletakkan di Service class (`app/Services/`), bukan
  langsung di Controller — agar bisa dipakai bersama oleh Controller Web
  maupun Controller Api tanpa duplikasi.
- Terapkan Policy/Gate Laravel untuk otorisasi per-resource (contoh:
  instruktur hanya boleh mengedit kursus miliknya sendiri).
- Pisahkan Controller menjadi `app/Http/Controllers/Api/` dan
  `app/Http/Controllers/Web/`.

## Format Respons API (wajib konsisten di semua endpoint)
Sukses:
```json
{ "success": true, "message": "string", "data": {} }
```
Gagal:
```json
{ "success": false, "message": "string", "errors": {} }
```

## Keamanan & Performa
- Password di-hash dengan bcrypt (default Laravel).
- Rate limiting pada endpoint login.
- Gunakan eager loading (`with()`) untuk menghindari N+1 query.
- Semua endpoint listing wajib pakai pagination.
- Index pada kolom foreign key dan kolom yang sering dicari/difilter.

## Proses Kerja yang Diharapkan dari Agent
- Setiap kali membuat fitur baru, jelaskan singkat file apa saja yang
  dibuat/diubah dan alasannya — seolah menulis deskripsi pull request.
- Setelah modul besar selesai, tunjukkan contoh request & response JSON dari
  1–2 endpoint terkait agar bisa langsung diverifikasi.
- Jelaskan singkat bagian penting Laravel yang dipakai (misalnya: kenapa
  Form Request, kenapa Policy diperlukan di sini) — jangan asumsikan saya
  sudah paham semua detail Laravel.
- Ikuti urutan pengerjaan di `PRD.md` Bagian 13 (Milestone), jangan lompat
  ke fitur lanjutan sebelum fondasi (migration, model, auth) selesai.

## Testing
- Setiap alur utama (auth, enrollment, submit kuis, submit tugas) wajib
  punya minimal satu feature test yang lulus sebelum dianggap selesai.

## Dokumentasi
- Perbarui `README.md` setiap kali modul besar selesai: cara instalasi,
  migrasi, seeding, menjalankan test, dan link koleksi Postman.
