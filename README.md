# CD Jajan

Sistem Informasi Katalog dan Pemesanan Jajanan UMKM di CD Berbasis Web (Laravel 13).
Proyek Kelompok 2.

Tampilan memakai template **Sarab** (Bestwpware, lisensi MIT) yang sudah dipasang di `public/assets`.

## Kebutuhan

- PHP 8.3 atau lebih baru (ekstensi: mbstring, openssl, pdo_sqlite, fileinfo)
- Composer
- Git

Windows: Laravel Herd menyediakan PHP dan Composer sekaligus.

## Menjalankan di komputer sendiri

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # Windows (cmd): type nul > database\database.sqlite
php artisan migrate --seed
php artisan storage:link              # agar foto yang diunggah bisa tampil
php artisan serve
```

Buka http://127.0.0.1:8000

Database bawaan memakai SQLite. Untuk MySQL, ubah bagian `DB_*` di `.env`, lalu jalankan `php artisan migrate --seed`.

## Akun contoh (kata sandi semuanya `password`)

| Peran | Email |
|---|---|
| Admin | admin@cd.test |
| Pemilik stand | sate@cd.test, kopi@cd.test, cilok@cd.test, waffle@cd.test, nasgor@cd.test, lila@cd.test |
| Pengajuan di daftar tunggu | pending@cd.test (belum bisa masuk sampai diterima admin) |

Ganti kata sandi admin sebelum dipakai sungguhan.

## Alur sistem

1. **Pembeli** membuka `/` tanpa login: melihat stand, menu, harga, lokasi, mengisi keranjang, lalu menekan "Pesan via WhatsApp". Pesan otomatis dibuat per stand dan dibuka di `wa.me` penjual. Keranjang disimpan di browser.
2. **Pemilik stand** mengajukan stand lewat `/daftar`. Status akun `pending`, stand belum tampil, dan akun belum bisa masuk (masuk daftar tunggu).
3. **Admin** membuka `/admin`, melihat daftar tunggu, lalu memilih **Terima** atau **Tolak**.
   - Terima: status `approved`, stand aktif di katalog, pemilik menerima pemberitahuan lewat email dan banner di dashboard.
   - Tolak: status `rejected`, pemilik menerima email penolakan.
4. **Pemilik stand yang diterima** masuk lewat `/masuk` ke `/penjual` untuk mengubah info stand, menambah menu, mengubah harga, dan menandai menu tersedia atau habis. Penjual hanya bisa mengubah data stand miliknya sendiri.

### Email pemberitahuan

Secara bawaan `MAIL_MAILER=log`, jadi email tidak benar-benar terkirim tetapi tertulis di `storage/logs/laravel.log`. Untuk mengirim email sungguhan, isi pengaturan `MAIL_*` di `.env` dengan SMTP (misalnya Mailtrap atau Gmail).

## Struktur singkat

| Bagian | Lokasi |
|---|---|
| Route | `routes/web.php` |
| Katalog publik | `app/Http/Controllers/CatalogController.php`, `resources/views/home.blade.php`, `public/assets/js/cd.js` |
| Login dan pengajuan stand | `app/Http/Controllers/AuthController.php`, `resources/views/auth/` |
| Dashboard penjual | `app/Http/Controllers/Seller/`, `resources/views/seller/` |
| Dashboard admin | `app/Http/Controllers/Admin/`, `resources/views/admin/` |
| Pembatasan peran | `app/Http/Middleware/EnsureRole.php` |
| Pemberitahuan | `app/Notifications/` |
| Tabel | migrasi di `database/migrations/` (`users`, `stands`, `products`, `notifications`) |

## Mengunggah ke GitHub

Buat repository kosong di GitHub (tanpa README), lalu:

```bash
git init
git add .
git commit -m "Project awal CD Jajan"
git branch -M main
git remote add origin https://github.com/USERNAME/cd-jajan.git
git push -u origin main
```

`.env`, `vendor/`, dan file database SQLite sudah diabaikan oleh `.gitignore`.

## Catatan

- Foto stand dan produk yang belum diunggah memakai gambar bawaan template sebagai pengganti (burger, pizza, donat) sesuai kategori.
- Belum ada fitur lupa kata sandi dan pengujian otomatis. Itu bisa ditambahkan sebagai pengembangan lanjutan.
