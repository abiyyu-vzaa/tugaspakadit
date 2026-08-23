<?php

include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../models/CatatanModel.php';
include_once __DIR__ . '/../models/KategoriModel.php';

class CatatanController
{
    private $catatanModel;
    private $kategoriModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $db = $database->getConnection();
        $this->catatanModel  = new CatatanModel($db);
        $this->kategoriModel = new KategoriModel($db);
    }

    // TAMPILKAN DAFTAR CATATAN (dengan pencarian, filter kategori, dan urutan)
    public function index()
    {
        require_login();

        $keyword     = trim($_GET['q'] ?? '');
        $kategori_id = $_GET['kategori_id'] ?? '';
        $sort        = $_GET['sort'] ?? 'terbaru';

        $data_catatan = $this->catatanModel->search($_SESSION['admin_id'], [
            'keyword'     => $keyword,
            'kategori_id' => $kategori_id,
            'sort'        => $sort,
        ]);
        $data_kategori = $this->kategoriModel->getAll($_SESSION['admin_id']);

        include __DIR__ . '/../views/catatan/index.php';
    }

    // TAMPILKAN FORM TAMBAH CATATAN
    public function tambah()
    {
        require_login();

        $data_kategori = $this->kategoriModel->getAll($_SESSION['admin_id']);
        include __DIR__ . '/../views/catatan/tambah.php';
    }

    // PROSES TAMBAH CATATAN (POST saja, dengan CSRF)
    public function tambahproses()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=catatan-tambah');
        }
        verify_csrf();

        $judul       = trim($_POST['judul'] ?? '');
        $isi         = trim($_POST['isi'] ?? '');
        $kategori_id = $_POST['kategori_id'] ?? '';

        if ($judul === '' || $isi === '') {
            keep_old_input(['judul', 'isi', 'kategori_id']);
            flash_set('error', 'Judul dan isi tidak boleh kosong.');
            redirect('index.php?act=catatan-tambah');
        }

        if (strlen($judul) > 150) {
            keep_old_input(['judul', 'isi', 'kategori_id']);
            flash_set('error', 'Judul maksimal 150 karakter.');
            redirect('index.php?act=catatan-tambah');
        }

        $this->catatanModel->create($_SESSION['admin_id'], $judul, $isi, $kategori_id);
        flash_set('success', 'Catatan berhasil ditambahkan.');
        redirect('index.php?act=catatan');
    }

    // TAMPILKAN FORM EDIT CATATAN
    public function edit()
    {
        require_login();

        $id = $_GET['id'] ?? '';
        if ($id === '') {
            flash_set('error', 'ID catatan tidak valid.');
            redirect('index.php?act=catatan');
        }

        $catatan = $this->catatanModel->getById($id, $_SESSION['admin_id']);
        if (!$catatan) {
            flash_set('error', 'Catatan tidak ditemukan.');
            redirect('index.php?act=catatan');
        }

        $data_kategori = $this->kategoriModel->getAll($_SESSION['admin_id']);
        include __DIR__ . '/../views/catatan/edit.php';
    }

    // PROSES EDIT CATATAN (POST saja, dengan CSRF)
    public function editproses()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=catatan');
        }
        verify_csrf();

        $id          = $_POST['id'] ?? '';
        $judul       = trim($_POST['judul'] ?? '');
        $isi         = trim($_POST['isi'] ?? '');
        $kategori_id = $_POST['kategori_id'] ?? '';

        if ($id === '') {
            flash_set('error', 'ID catatan tidak valid.');
            redirect('index.php?act=catatan');
        }

        if ($judul === '' || $isi === '') {
            keep_old_input(['judul', 'isi', 'kategori_id']);
            flash_set('error', 'Judul dan isi tidak boleh kosong.');
            redirect('index.php?act=catatan-edit&id=' . urlencode($id));
        }

        if (strlen($judul) > 150) {
            keep_old_input(['judul', 'isi', 'kategori_id']);
            flash_set('error', 'Judul maksimal 150 karakter.');
            redirect('index.php?act=catatan-edit&id=' . urlencode($id));
        }

        if ($this->catatanModel->update($id, $_SESSION['admin_id'], $judul, $isi, $kategori_id)) {
            flash_set('success', 'Catatan berhasil diperbarui.');
        } else {
            flash_set('error', 'Catatan tidak ditemukan atau gagal diperbarui.');
        }
        redirect('index.php?act=catatan');
    }

    // HAPUS CATATAN (POST saja, dengan CSRF)
    public function hapus()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=catatan');
        }
        verify_csrf();

        $id = $_POST['id'] ?? '';
        if ($id !== '' && $this->catatanModel->delete($id, $_SESSION['admin_id'])) {
            flash_set('success', 'Catatan berhasil dihapus.');
        } else {
            flash_set('error', 'Catatan tidak ditemukan.');
        }
        redirect('index.php?act=catatan');
    }

    // SEMATKAN / LEPAS SEMATAN (POST saja, dengan CSRF).
    // Setelah selesai, kembali ke daftar dengan kondisi cari/filter/sort utuh.
    public function pin()
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=catatan');
        }
        verify_csrf();

        $id = $_POST['id'] ?? '';
        if ($id !== '') {
            $this->catatanModel->togglePin($id, $_SESSION['admin_id']);
        }

        $qs = [];
        foreach (['q', 'kategori_id', 'sort'] as $key) {
            if (isset($_POST[$key]) && $_POST[$key] !== '') {
                $qs[$key] = $_POST[$key];
            }
        }
        $qs['act'] = 'catatan';

        redirect('index.php?' . http_build_query($qs));
    }
}
