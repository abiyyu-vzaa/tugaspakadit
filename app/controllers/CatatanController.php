<?php

include_once 'config/database.php';
include_once 'app/models/CatatanModel.php';
include_once 'app/models/KategoriModel.php';

class CatatanController {

    private $db;
    private $catatanModel;
    private $kategoriModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        $this->catatanModel = new CatatanModel($this->db);
        $this->kategoriModel = new KategoriModel($this->db);
    }

    // TAMPILKAN DAFTAR CATATAN
    public function index() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit;
        }

        $data_catatan = $this->catatanModel->getAll();
        include 'app/views/catatan/index.php';
    }

    // TAMPILKAN FORM TAMBAH CATATAN
    public function tambah()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit;
        }

        $data_kategori = $this->kategoriModel->getAll();
        include 'app/views/catatan/tambah.php';
    }

    // PROSES TAMBAH CATATAN
    public function tambahproses() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit;
        }
    
        if ($_POST) {
            $judul = $_POST['judul'];
            $isi = $_POST['isi'];
            $kategori_id = $_POST['kategori_id'];
            $admin_id = $_SESSION['admin_id'];

            if (!empty($judul) && !empty($isi)) {
                $this->catatanModel->create($judul, $isi, $kategori_id, $admin_id);
                $_SESSION['success_msg'] = "Berhasil tambah catatan";
                header("Location: index.php?act=catatan");
                exit;
            } else {
                $_SESSION['error_msg'] = "Judul dan isi tidak boleh kosong";
                header("Location: index.php?act=catatan-tambah");
                exit;
            }
        }
    }
    
    // TAMBAHKAN METHOD INI - untuk menampilkan form edit
    public function edit() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit;
        }
        
        $id = isset($_GET['id']) ? $_GET['id'] : die('Error: ID kosong');
        $catatan = $this->catatanModel->getById($id);
        $data_kategori = $this->kategoriModel->getAll();
        
        include 'app/views/catatan/edit.php';
    }
    
    // TAMBAHKAN METHOD INI - untuk memproses edit catatan
    public function editproses() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit;
        }
        
        if ($_POST) {
            $id = $_POST['id'];
            $judul = $_POST['judul'];
            $isi = $_POST['isi'];
            $kategori_id = $_POST['kategori_id'];
            
            if (!empty($judul) && !empty($isi) && !empty($id)) {
                $this->catatanModel->update($id, $judul, $isi, $kategori_id);
                $_SESSION['success_msg'] = "Berhasil edit catatan";
            } else {
                $_SESSION['error_msg'] = "Judul dan isi tidak boleh kosong";
                header("Location: index.php?act=catatan-edit&id=" . $id);
                exit;
            }
        }
        header("Location: index.php?act=catatan");
        exit;
    }

    // HAPUS CATATAN
    public function hapus() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit;
        }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->catatanModel->delete($id);
            $_SESSION['success_msg'] = "Berhasil hapus catatan";
        }

        header("Location: index.php?act=catatan");
        exit;
    }
}
?>
