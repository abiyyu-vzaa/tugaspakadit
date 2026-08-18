<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori</title>
    <link rel="stylesheet" href="/db_catatan/public/css/kategori.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include __DIR__. '/../components/nav.php' ?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-tags"></i> Daftar Kategori</h2>
                <a href="index.php?act=kategori-tambah" class="btn">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="success-msg">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['success_msg'];?>
                </div>
                <?php unset($_SESSION['success_msg']); ?>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Kategori</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (count($data_kategori) > 0) :
                     foreach($data_kategori as $row):?>
                        <tr>
                            <td><?= $no?></td>
                            <td><?= $row['nama_kategori']?></td>
                            <td><?= $row['nama_admin']?></td>
                            <td>
                                <a href="index.php?act=kategori-edit&id=<?= $row['id']?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="index.php?act=kategori-hapus&id=<?= $row['id']?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php
                    $no++;
                    endforeach;?>

                    <?php else : ?>
                    <tr>
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-folder-open"></i> Belum ada kategori
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <a href="index.php?act=dashboard" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
