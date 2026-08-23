# Catatan — Aplikasi Catatan MVC (PHP + MySQL)

Aplikasi catatan bertema "kartu katalog": setiap catatan tampil seperti kartu
indeks bernomor, dikelompokkan per kategori, bisa disematkan (pin), dicari,
difilter, dan diurutkan.

## Cara pakai
1. Salin folder ini ke `htdocs/db_catatan` (XAMPP) atau document root PHP kamu.
2. **Database baru:** import `sql/schema.sql` di phpMyAdmin.
   **Database lama (sudah ada isinya):** import `sql/migration_upgrade.sql` —
   menambah kolom `is_pinned`, `created_at`, `updated_at`, serta mengubah
   unique constraint kategori menjadi per-akun, tanpa menghapus data.
3. Sesuaikan `config/database.php` bila nama DB/host berbeda.
4. Buka `index.php` di browser, register akun admin, lalu login.

## Fitur
- CRUD catatan & kategori lengkap.
- Pencarian catatan (judul & isi), filter per kategori, dan urutan
  (terbaru / terlama / A-Z / Z-A).
- Sematkan (pin) catatan penting — otomatis naik ke atas daftar.
- Dashboard: statistik (total catatan, kategori, jumlah disematkan) dan
  5 catatan terbaru.
- Halaman kategori menampilkan jumlah catatan per kategori; menghapus kategori
  **tidak** menghapus catatannya (catatan menjadi "tanpa kategori" dan jumlah
  yang terdampak diumumkan lewat flash message).
- Registrasi dengan validasi konfirmasi password & panjang minimal.
- Flash message sukses/gagal konsisten di semua halaman, dan isian form
  dipertahankan (old input) bila validasi gagal.

## Arsitektur & alur
- Front controller tunggal `index.php` dengan routing `?act=...`.
- Pola: `Controller -> Model (PDO prepared statement) -> View`.
- Semua endpoint pengubah data (tambah/edit/hapus/pin) **hanya menerima POST**
  dan dilindungi **token CSRF**.
- Alur **Post/Redirect/Get**: setiap proses selalu diakhiri redirect + flash
  message, sehingga refresh tidak mengirim ulang data.
- **Data terpisah per akun**: setiap query catatan/kategori dibatasi
  `admin_id` milik sesi yang login — akun satu tidak bisa melihat/mengubah
  data akun lain.
- Session di-regenerasi saat login (anti session fixation), dan dibersihkan
  total saat logout.

## Keamanan
- Password di-hash `password_hash()` (bcrypt) — tidak pernah disimpan polos.
- Prepared statement PDO di semua query (anti SQL injection).
- Escaping output `e()` (htmlspecialchars) di semua view; data disimpan apa
  adanya sehingga tidak terjadi double-encoding.
- Token CSRF di semua form; validasi input (kosong, panjang maksimum) di sisi
  server untuk setiap endpoint.

## Struktur
```
index.php                    -> front controller + routing ?act=
app/helpers.php              -> CSRF, flash, old input, auth guard, e(), excerpt, waktu
app/controllers/             -> AdminController, CatatanController, KategoriController
app/models/                  -> AdminModel, CatatanModel, KategoriModel
app/views/                   -> tampilan (auth, dashboard, catatan, kategori, components)
app/views/components/        -> sidebar.php, flash.php (terpakai ulang)
config/database.php          -> koneksi PDO terpusat
public/css/style.css         -> satu design system terpusat
sql/schema.sql               -> skema lengkap untuk instalasi baru
sql/migration_upgrade.sql    -> migrasi untuk database lama
```
