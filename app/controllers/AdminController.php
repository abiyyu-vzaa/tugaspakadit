<?php

include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../models/AdminModel.php';

class AdminController
{
    private $adminModel;
    private $db;

    public function __construct()
    {
        // Pastikan session aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->adminModel = new AdminModel($this->db);
    }

    // Halaman awal
    public function index()
    {
        if (isset($_SESSION['admin_id'])) {
            header('Location: index.php?act=dashboard');
            exit;
        }

        include 'app/views/auth/login.php';
    }

    // Menampilkan halaman register
    public function viewregister()
    {
        include 'app/views/auth/register.php';
    }

    // Proses register
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($this->adminModel->register($username, $password)) {
                $success = "Registrasi berhasil! Silakan login.";
                include 'app/views/auth/login.php';
            } else {
                $error = "Gagal mendaftar. Username mungkin sudah dipakai.";
                include 'app/views/auth/register.php';
            }
        }
    }

    // Proses login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $admin = $this->adminModel->login($username, $password);
    
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
    
                header('Location: index.php?act=dashboard');
                exit;
            } else {
                $error = "Username atau Password salah!";
                include 'app/views/auth/login.php';
            }
        }
    }
   // Dashboard
    public function dashboard()
    {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit;
    }
        include __DIR__  . '/../views/dashboard/index.php';
        // require_once __DIR__ . '/../views/dashboard/index.php';
    }


    // Logout
    public function logout()
    {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

