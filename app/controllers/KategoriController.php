<?php

include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../models/KategoriModel.php';

class KategoriController
{
    private $kategoriModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $db = $database->getConnection();
        $this->kategoriModel = new KategoriModel($db);
    }

    // DAFTAR KATEGORI milik admin yang sedang login
    public function index()
    {
        require_login();

        $data_kategori = $this->kategoriModel->getAll($_SESSION['admin_id']);
        include __DIR__ . '/../views/kategori/index.php';
    }

    // TAMPILKAN FORM TAMBAH KATEGORI
    public function tambah()
    {
        require_login();

        include __DIR__ . '/../views/kategori/tambah.php';
    }

    // PROSES TAMBAH KATEGORI (POST saja, dengan CSRF)
    public function tambahproses()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=kategori-tambah');
        }
        verify_csrf();

        $nama     = trim($_POST['nama_kategori'] ?? '');
        $admin_id = $_SESSION['admin_id'];

        if ($nama === '') {
            flash_set('error', 'Nama kategori tidak boleh kosong.');
            redirect('index.php?act=kategori-tambah');
        }

        if (strlen($nama) > 100) {
            keep_old_input(['nama_kategori']);
            flash_set('error', 'Nama kategori maksimal 100 karakter.');
            redirect('index.php?act=kategori-tambah');
        }

        if ($this->kategoriModel->exists($nama, $admin_id)) {
            keep_old_input(['nama_kategori']);
            flash_set('error', 'Kategori "' . $nama . '" sudah ada.');
            redirect('index.php?act=kategori-tambah');
        }

        if ($this->kategoriModel->create($admin_id, $nama)) {
            flash_set('success', 'Kategori berhasil ditambahkan.');
            redirect('index.php?act=kategori');
        }

        keep_old_input(['nama_kategori']);
        flash_set('error', 'Gagal menambahkan kategori. Silakan coba lagi.');
        redirect('index.php?act=kategori-tambah');
    }

    // TAMPILKAN FORM EDIT KATEGORI
    public function edit()
    {
        require_login();

        $id       = $_GET['id'] ?? '';
        $kategori = $id !== '' ? $this->kategoriModel->getById($id, $_SESSION['admin_id']) : false;

        if (!$kategori) {
            flash_set('error', 'Kategori tidak ditemukan.');
            redirect('index.php?act=kategori');
        }

        include __DIR__ . '/../views/kategori/edit.php';
    }

    // PROSES EDIT KATEGORI (POST saja, dengan CSRF)
    public function editproses()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=kategori');
        }
        verify_csrf();

        $id        = $_POST['id'] ?? '';
        $nama_baru = trim($_POST['nama_kategori'] ?? '');

        if ($id === '') {
            flash_set('error', 'ID kategori tidak valid.');
            redirect('index.php?act=kategori');
        }

        if ($nama_baru === '') {
            keep_old_input(['nama_kategori']);
            flash_set('error', 'Nama kategori tidak boleh kosong.');
            redirect('index.php?act=kategori-edit&id=' . urlencode($id));
        }

        if (strlen($nama_baru) > 100) {
            keep_old_input(['nama_kategori']);
            flash_set('error', 'Nama kategori maksimal 100 karakter.');
            redirect('index.php?act=kategori-edit&id=' . urlencode($id));
        }

        if ($this->kategoriModel->exists($nama_baru, $_SESSION['admin_id'], $id)) {
            keep_old_input(['nama_kategori']);
            flash_set('error', 'Kategori "' . $nama_baru . '" sudah digunakan.');
            redirect('index.php?act=kategori-edit&id=' . urlencode($id));
        }

        if ($this->kategoriModel->update($id, $_SESSION['admin_id'], $nama_baru)) {
            flash_set('success', 'Kategori berhasil diperbarui.');
        } else {
            flash_set('error', 'Kategori tidak ditemukan atau gagal diperbarui.');
        }
        redirect('index.php?act=kategori');
    }

    // HAPUS KATEGORI (POST saja, dengan CSRF).
    // Catatan di dalamnya tidak ikut terhapus — menjadi "tanpa kategori"
    // (ON DELETE SET NULL), dan pengguna diberi tahu jumlahnya.
    public function hapus()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=kategori');
        }
        verify_csrf();

        $id       = $_POST['id'] ?? '';
        $admin_id = $_SESSION['admin_id'];

        $kategori = $id !== '' ? $this->kategoriModel->getById($id, $admin_id) : false;
        if (!$kategori) {
            flash_set('error', 'Kategori tidak ditemukan.');
            redirect('index.php?act=kategori');
        }

        $jumlah_catatan = $this->kategoriModel->countCatatan($id);
        $this->kategoriModel->delete($id, $admin_id);

        if ($jumlah_catatan > 0) {
            flash_set('success', 'Kategori berhasil dihapus. ' . $jumlah_catatan . ' catatan terkait kini berstatus tanpa kategori.');
        } else {
            flash_set('success', 'Kategori berhasil dihapus.');
        }
        redirect('index.php?act=kategori');
    }
}
