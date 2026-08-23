<?php
// Koneksi database terpusat (PDO). Nama class: Database.
class Database
{
    private $host     = "localhost";
    private $db_name  = "db_catatan";
    private $username = "root";
    private $password = "";

    private $conn = null;

    public function getConnection()
    {
        // Gunakan koneksi yang sama selama request berjalan.
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $exception) {
            http_response_code(500);
            exit(
                '<div style="font-family:Segoe UI,Arial,sans-serif;max-width:560px;margin:60px auto;padding:24px;'
                . 'border:1px solid #e3b6ac;background:#f3d9d3;border-radius:8px;color:#b04a3d;">'
                . '<h2 style="margin-top:0;">Koneksi database gagal</h2>'
                . '<p>Pastikan MySQL/MariaDB (XAMPP) sudah berjalan dan database <b>db_catatan</b> sudah di-import '
                . '(lihat <b>sql/schema.sql</b>).</p>'
                . '<p style="font-size:13px;">Detail: ' . htmlspecialchars($exception->getMessage()) . '</p>'
                . '</div>'
            );
        }

        return $this->conn;
    }
}
