-- =========================================================
-- Migrasi untuk database db_catatan yang SUDAH ADA datanya
-- (skema lama: isi catatan ada di kolom `catatan`, belum ada
-- is_pinned/updated_at, kategori_id NOT NULL).
-- Jalankan SEKALI di phpMyAdmin / mysql client.
-- Migrasi ini ADDITIF: tidak menghapus data sama sekali.
-- =========================================================

USE db_catatan;

-- 1. Pastikan kolom `isi` ada (skema lama menyimpan isi di kolom `catatan`).
ALTER TABLE catatan ADD COLUMN IF NOT EXISTS isi TEXT NULL AFTER judul;

-- 2. Salinkan isi lama ke kolom `isi` (data lama tidak hilang).
UPDATE catatan SET isi = catatan WHERE isi IS NULL;
UPDATE catatan SET isi = '' WHERE isi IS NULL;

-- 3. Tambah kolom fitur baru: sematkan (pin) dan updated_at.
ALTER TABLE catatan ADD COLUMN IF NOT EXISTS is_pinned TINYINT(1) NOT NULL DEFAULT 0 AFTER admin_id;
ALTER TABLE catatan ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL AFTER created_at;

-- 4. kategori_id boleh NULL (catatan boleh tanpa kategori).
ALTER TABLE catatan MODIFY kategori_id INT NULL;

-- 5. Ganti FK kategori: saat kategori dihapus, catatannya TETAP ADA
--    (hanya menjadi "tanpa kategori"), bukan ikut terblokir/terhapus.
ALTER TABLE catatan DROP FOREIGN KEY IF EXISTS catatan_ibfk_2;
ALTER TABLE catatan ADD CONSTRAINT fk_catatan_kategori
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL;

-- 6. Unique nama kategori per-akun, supaya dua akun berbeda boleh punya
--    nama kategori yang sama (data aplikasi kini dipisah per akun).
ALTER TABLE kategori DROP INDEX IF EXISTS nama_kategori;
ALTER TABLE kategori DROP INDEX IF EXISTS uq_kategori_admin;
ALTER TABLE kategori ADD UNIQUE KEY uq_kategori_admin (admin_id, nama_kategori);
