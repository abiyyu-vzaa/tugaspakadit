<?php

class KategoriModel
{
    private $conn;
    private $table_name = "kategori";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Semua kategori milik satu admin, beserta jumlah catatan per kategori.
    public function getAll($admin_id)
    {
        $query = "SELECT k.*, a.username AS nama_admin,
                         (SELECT COUNT(*) FROM catatan c WHERE c.kategori_id = k.id) AS jumlah_catatan
                  FROM {$this->table_name} k
                  JOIN admin a ON k.admin_id = a.id
                  WHERE k.admin_id = :admin_id
                  ORDER BY k.nama_kategori ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count($admin_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total FROM {$this->table_name} WHERE admin_id = :admin_id"
        );
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetch()['total'];
    }

    // Cek duplikasi nama untuk admin yang sama. $exclude_id dipakai saat edit
    // agar nama lama milik record itu sendiri tidak dianggap duplikat.
    public function exists($nama_kategori, $admin_id, $exclude_id = 0)
    {
        $query = "SELECT id FROM {$this->table_name}
                  WHERE nama_kategori = :nama AND admin_id = :admin_id AND id <> :exclude_id
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nama', $nama_kategori);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', (int) $exclude_id, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }

    // Simpan tanpa htmlspecialchars: escaping dilakukan di view saat output.
    public function create($admin_id, $nama_kategori)
    {
        $query = "INSERT INTO {$this->table_name} (nama_kategori, admin_id)
                  VALUES (:nama, :admin_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nama', $nama_kategori);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Gagal insert, misal terbentur unique constraint (nama duplikat).
            return false;
        }
    }

    // Ambil satu kategori, hanya bila milik admin yang sedang login.
    public function getById($id, $admin_id)
    {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE id = :id AND admin_id = :admin_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function update($id, $admin_id, $nama_kategori)
    {
        $query = "UPDATE {$this->table_name}
                  SET nama_kategori = :nama
                  WHERE id = :id AND admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nama', $nama_kategori);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }

        return $stmt->rowCount() > 0;
    }

    public function delete($id, $admin_id)
    {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id AND admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Jumlah catatan pada satu kategori (dipakai saat hapus kategori).
    public function countCatatan($kategori_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total FROM catatan WHERE kategori_id = :kategori_id"
        );
        $stmt->bindValue(':kategori_id', $kategori_id, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetch()['total'];
    }
}
