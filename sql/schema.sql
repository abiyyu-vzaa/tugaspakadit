-- =========================================================
-- Skema database: db_catatan
-- Gunakan file ini untuk instalasi BARU (database kosong).
-- Jika database lama sudah ada isinya, pakai migration.sql saja.
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_catatan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_catatan;

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    admin_id INT NOT NULL,
    UNIQUE KEY uq_kategori_admin (admin_id, nama_kategori),
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS catatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    kategori_id INT NULL,
    admin_id INT NOT NULL,
    is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY idx_catatan_admin (admin_id),
    KEY idx_catatan_kategori (kategori_id),
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB;
