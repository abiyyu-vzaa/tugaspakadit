<?php

class CatatanModel
{
    private $conn;
    private $table_name = "catatan";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Ambil daftar catatan milik satu admin, dengan dukungan pencarian,
    // filter kategori, dan pengurutan. Catatan disematkan selalu di atas.
    // $opts: keyword, kategori_id, sort (terbaru|terlama|az|za)
    public function search($admin_id, $opts = [])
    {
        $keyword     = trim($opts['keyword'] ?? '');
        $kategori_id = $opts['kategori_id'] ?? '';
        $sort        = $opts['sort'] ?? 'terbaru';

        $where  = ["c.admin_id = :admin_id"];
        $params = [':admin_id' => $admin_id];

        if ($keyword !== '') {
            // PDO native prepare tidak boleh memakai placeholder bernama yang sama dua kali.
            $where[] = "(c.judul LIKE :kw_judul OR c.isi LIKE :kw_isi)";
            $params[':kw_judul'] = '%' . $keyword . '%';
            $params[':kw_isi']   = '%' . $keyword . '%';
        }
        if ($kategori_id !== '' && $kategori_id !== null) {
            $where[] = "c.kategori_id = :kategori_id";
            $params[':kategori_id'] = $kategori_id;
        }

        switch ($sort) {
            case 'terlama': $order = "c.is_pinned DESC, c.id ASC";   break;
            case 'az':      $order = "c.is_pinned DESC, c.judul ASC"; break;
            case 'za':      $order = "c.is_pinned DESC, c.judul DESC"; break;
            default:        $order = "c.is_pinned DESC, c.id DESC";
        }

        $query = "SELECT c.*, k.nama_kategori, a.username AS nama_admin
                  FROM {$this->table_name} c
                  LEFT JOIN kategori k ON c.kategori_id = k.id
                  LEFT JOIN admin a ON c.admin_id = a.id
                  WHERE " . implode(' AND ', $where)
                  . " ORDER BY " . $order;

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAll($admin_id)
    {
        return $this->search($admin_id);
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

    public function countPinned($admin_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total FROM {$this->table_name}
             WHERE admin_id = :admin_id AND is_pinned = 1"
        );
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetch()['total'];
    }

    public function getRecent($admin_id, $limit = 5)
    {
        $limit = max(1, (int) $limit);
        $query = "SELECT c.*, k.nama_kategori
                  FROM {$this->table_name} c
                  LEFT JOIN kategori k ON c.kategori_id = k.id
                  WHERE c.admin_id = :admin_id
                  ORDER BY c.id DESC
                  LIMIT $limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Ambil satu catatan, hanya bila milik admin yang sedang login.
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

    public function togglePin($id, $admin_id)
    {
        $query = "UPDATE {$this->table_name}
                  SET is_pinned = 1 - is_pinned
                  WHERE id = :id AND admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Simpan tanpa htmlspecialchars: escaping dilakukan di view saat output.
    public function create($admin_id, $judul, $isi, $kategori_id)
    {
        $query = "INSERT INTO {$this->table_name}
                  (judul, isi, kategori_id, admin_id, created_at)
                  VALUES (:judul, :isi, :kategori_id, :admin_id, :created_at)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':judul', $judul);
        $stmt->bindValue(':isi', $isi);
        $stmt->bindValue(':kategori_id', $this->validateKategori($kategori_id, $admin_id), PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));

        return $stmt->execute();
    }

    public function update($id, $admin_id, $judul, $isi, $kategori_id)
    {
        $query = "UPDATE {$this->table_name}
                  SET judul = :judul, isi = :isi, kategori_id = :kategori_id, updated_at = :updated_at
                  WHERE id = :id AND admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':judul', $judul);
        $stmt->bindValue(':isi', $isi);
        $stmt->bindValue(':kategori_id', $this->validateKategori($kategori_id, $admin_id), PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s'));
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

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

    // Kategori yang dipilih harus milik admin yang sama; kosong/tidak valid -> NULL.
    private function validateKategori($kategori_id, $admin_id)
    {
        if (empty($kategori_id)) {
            return null;
        }

        $stmt = $this->conn->prepare(
            "SELECT id FROM kategori WHERE id = :id AND admin_id = :admin_id LIMIT 1"
        );
        $stmt->bindValue(':id', $kategori_id, PDO::PARAM_INT);
        $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch() ? (int) $kategori_id : null;
    }
}
