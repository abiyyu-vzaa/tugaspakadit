<?php

include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../models/AdminModel.php';
include_once __DIR__ . '/../models/CatatanModel.php';
include_once __DIR__ . '/../models/KategoriModel.php';

class AdminController
{
    private $adminModel;
    private $catatanModel;
    private $kategoriModel;

    public function __construct()
    {
        // Pastikan session aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $db = $database->getConnection();
        $this->adminModel    = new AdminModel($db);
        $this->catatanModel  = new CatatanModel($db);
        $this->kategoriModel = new KategoriModel($db);
    }

    // Halaman login. Sudah login? Langsung ke dashboard.
    public function index()
    {
        if (is_logged_in()) {
            redirect('index.php?act=dashboard');
        }

        include __DIR__ . '/../views/auth/login.php';
    }

    // Halaman register.
    public function viewregister()
    {
        if (is_logged_in()) {
            redirect('index.php?act=dashboard');
        }

        include __DIR__ . '/../views/auth/register.php';
    }

    // Proses register (POST saja, dengan CSRF). Alur: validasi -> cek duplikat
    // -> simpan -> selalu redirect (Post/Redirect/Get) agar tidak double-submit.
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=register');
        }
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($username === '' || $password === '' || $confirm === '') {
            keep_old_input(['username']);
            flash_set('error', 'Semua kolom wajib diisi.');
            redirect('index.php?act=register');
        }

        if (strlen($username) > 50) {
            keep_old_input(['username']);
            flash_set('error', 'Username maksimal 50 karakter.');
            redirect('index.php?act=register');
        }

        if ($password !== $confirm) {
            keep_old_input(['username']);
            flash_set('error', 'Konfirmasi password tidak cocok.');
            redirect('index.php?act=register');
        }

        if (strlen($password) < 6) {
            keep_old_input(['username']);
            flash_set('error', 'Password minimal 6 karakter.');
            redirect('index.php?act=register');
        }

        if ($this->adminModel->usernameExists($username)) {
            keep_old_input(['username']);
            flash_set('error', 'Username sudah dipakai. Silakan pilih username lain.');
            redirect('index.php?act=register');
        }

        if ($this->adminModel->register($username, $password)) {
            flash_set('success', 'Registrasi berhasil! Silakan login dengan akunmu.');
            redirect('index.php?act=login');
        }

        keep_old_input(['username']);
        flash_set('error', 'Gagal mendaftar. Silakan coba lagi.');
        redirect('index.php?act=register');
    }

    // Proses login (POST saja, dengan CSRF).
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?act=login');
        }
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $admin = $this->adminModel->login($username, $password);

        if ($admin) {
            // Cegah session fixation: pakai ID session baru setelah login.
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['username'] = $admin['username'];

            redirect('index.php?act=dashboard');
        }

        keep_old_input(['username']);
        flash_set('error', 'Username atau password salah.');
        redirect('index.php?act=login');
    }

    // Dashboard dengan statistik milik admin yang sedang login.
    public function dashboard()
    {
        require_login();
        $admin_id = $_SESSION['admin_id'];

        $total_catatan    = $this->catatanModel->count($admin_id);
        $total_kategori   = $this->kategoriModel->count($admin_id);
        $total_disematkan = $this->catatanModel->countPinned($admin_id);
        $catatan_terbaru  = $this->catatanModel->getRecent($admin_id, 5);

        include __DIR__ . '/../views/dashboard/index.php';
    }

    // Logout: kosongkan data sesi dan pakai ID sesi baru (ID lama langsung
    // tidak berlaku), lalu bawa pesan flash ke halaman login.
    public function logout()
    {
        $_SESSION = [];
        session_regenerate_id(true);
        flash_set('success', 'Anda telah keluar dari sistem.');

        redirect('index.php?act=login');
    }
}
