<?php
// index.php — front controller: routing semua request lewat parameter ?act=
session_start();
date_default_timezone_set('Asia/Jakarta');

include_once 'app/helpers.php';
include_once 'app/controllers/AdminController.php';
include_once 'app/controllers/KategoriController.php';
include_once 'app/controllers/CatatanController.php';

// Inisialisasi controller
$AdminController = new AdminController();
$KategoriController = new KategoriController();
$CatatanController = new CatatanController();

// Routing Sederhana
$action = isset($_GET['act']) ? $_GET['act'] : 'login';

switch ($action) {
    case 'register':
        $AdminController->viewregister();
        break;

    case 'register-process':
        $AdminController->register();
        break;

    case 'logout':
        $AdminController->logout();
        break;

    case 'login':
        $AdminController->index();
        break;

    case 'login-process':
        $AdminController->login();
        break;

    case 'dashboard':
        $AdminController->dashboard();
        break;

    case 'kategori':
        $KategoriController->index();
        break;

    case 'kategori-tambah':
        $KategoriController->tambah();
        break;

    case 'kategori-tambah-proses':
        $KategoriController->tambahproses();
        break;

    case 'kategori-edit':
        $KategoriController->edit();
        break;

    case 'kategori-edit-proses':
        $KategoriController->editproses();
        break;

    case 'kategori-hapus':
        $KategoriController->hapus();
        break;

    case 'catatan':
        $CatatanController->index();
        break;

    case 'catatan-tambah':
        $CatatanController->tambah();
        break;

    case 'catatan-tambah-proses':
        $CatatanController->tambahproses();
        break;

    case 'catatan-edit':
        $CatatanController->edit();
        break;

    case 'catatan-edit-proses':
        $CatatanController->editproses();
        break;

    case 'catatan-hapus':
        $CatatanController->hapus();
        break;

    case 'catatan-pin':
        $CatatanController->pin();
        break;

    default:
        $AdminController->index();
        break;
}
