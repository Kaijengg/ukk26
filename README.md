# Perpustakaan Sekolah Digital

Aplikasi perpustakaan sekolah berbasis Laravel 10, Filament 3, dan MySQL untuk mengelola koleksi buku, data anggota, serta transaksi peminjaman dan pengembalian.

## Peran dan fitur

- **Admin**: masuk ke panel `/admin`; CRUD buku dan anggota; pencarian dan filter transaksi; pencatatan pengembalian.
- **Siswa**: registrasi/login, katalog dan pencarian buku, peminjaman, riwayat transaksi, pengembalian mandiri.
- **Aturan pinjam**: maksimal tiga transaksi aktif per anggota, tujuh hari masa pinjam, stok tersedia diperiksa dan diperbarui saat transaksi berubah.
- **Fitur tambahan**: penanda keterlambatan dan filter status transaksi.

## Menjalankan di Laragon

1. Jalankan Apache dan MySQL dari Laragon.
2. Buat database `perpustakaan_sekolah` lewat phpMyAdmin atau jalankan SQL di `database/schema.sql`.
3. Periksa `.env` dan sesuaikan koneksi MySQL.
4. Buka terminal pada folder proyek dan jalankan:

   ```powershell
   php artisan key:generate
   php artisan config:clear
   php artisan migrate
   php artisan make:filament-user
   php artisan serve
   ```

5. Buka katalog di `http://127.0.0.1:8000` dan panel admin di `http://127.0.0.1:8000/admin`.
6. Siswa dapat mendaftar lewat tombol **Daftar Siswa** pada katalog.

## Struktur penting

- `app/Models`: model `Book`, `Member`, dan `Loan` beserta relasi serta aturan stok.
- `app/Filament/Resources`: halaman admin untuk data buku, anggota, dan transaksi.
- `app/Http/Controllers/LibraryController.php`: katalog, autentikasi siswa, pinjam, dan kembali.
- `database/migrations`: definisi skema yang dapat dijalankan ulang.
- `database/schema.sql`: skema SQL untuk impor phpMyAdmin.
- `docs/Dokumentasi_Perpustakaan_Sekolah_Digital.docx`: ERD, penjelasan program, panduan, debugging, dan evaluasi.

## Akun

Buat akun admin secara interaktif dengan `php artisan make:filament-user`. Siswa membuat akun dari halaman registrasi. Jangan memasukkan file `.env` ke repositori publik.
