<?php

class AdminModel
{
    private $conn;
    private $table_name = "admin";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Register admin
    public function register($username, $password)
    {
        $query = " INSERT INTO " . $this->table_name . " (username, password, created_at)
                  VALUES (:username, :password, :created_at) ";
        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        date_default_timezone_set("Asia/Jakarta");
        
        $created_at = date('Y-m-d H:i:s');

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute());{
            return true;
        }
        return false;
    }

    // Login admin
    public function login($username, $password)
    {
        $query = "SELECT id, username, password
                  FROM {$this->table_name}
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }
    public function getbyid($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHARE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindparam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
