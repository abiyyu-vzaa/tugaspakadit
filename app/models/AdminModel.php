<?php

class AdminModel
{
    private $conn;
    private $table_name = "admin";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Cek apakah username sudah terdaftar.
    public function usernameExists($username)
    {
        $query = "SELECT id FROM {$this->table_name} WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }

    // Register admin baru.
    // Catatan: data disimpan apa adanya (tanpa htmlspecialchars) — escaping
    // dilakukan di sisi tampilan agar tidak terjadi double-encoding.
    public function register($username, $password)
    {
        $query = "INSERT INTO {$this->table_name} (username, password, created_at)
                  VALUES (:username, :password, :created_at)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
            $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));

            return $stmt->execute();
        } catch (PDOException $e) {
            // Gagal insert, misal username duplikat (UNIQUE constraint).
            return false;
        }
    }

    // Login: kembalikan array data admin bila cocok, selain itu false.
    public function login($username, $password)
    {
        $query = "SELECT id, username, password
                  FROM {$this->table_name}
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['password'])) {
            unset($row['password']);
            return $row;
        }

        return false;
    }

    public function getById($id)
    {
        $query = "SELECT id, username, created_at
                  FROM {$this->table_name}
                  WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
