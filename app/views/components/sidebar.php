<?php
// Komponen sidebar. Set $active_menu sebelum include untuk highlight menu aktif.
// Nilai yang didukung: 'dashboard', 'kategori', 'catatan'
$active_menu = $active_menu ?? '';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <h1>Catatan</h1>
        <span class="stamp">MVC</span>
    </div>

    <nav class="sidebar-nav">
        <a href="index.php?act=dashboard" class="<?= $active_menu === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-grip"></i> Dashboard
        </a>
        <a href="index.php?act=catatan" class="<?= $active_menu === 'catatan' ? 'active' : '' ?>">
            <i class="fas fa-note-sticky"></i> Catatan
        </a>
        <a href="index.php?act=kategori" class="<?= $active_menu === 'kategori' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i> Kategori
        </a>
    </nav>

    <div class="sidebar-foot">
        <div class="sidebar-user">
            Masuk sebagai
            <b><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '-' ?></b>
        </div>
        <div class="sidebar-nav">
            <a href="index.php?act=logout" class="logout"><i class="fas fa-right-from-bracket"></i> Keluar</a>
        </div>
    </div>
</aside>
