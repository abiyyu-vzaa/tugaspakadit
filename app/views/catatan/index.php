<!DOCTYPE html>
<html>
<head>
    <title>Daftar Catatan</title>
</head>
<body>

<?php include "app/views/components/nav.php"; ?>

<div class="container" style="margin-top: 80px;">

    <div class="card">
        <h2>Daftar Catatan</h2>

        <a href="index.php?act=catatan-tambah">Tambah Catatan Baru</a>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div style="color:green;">
                <?= $_SESSION['success_msg']; ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <div>

            <?php if (count($data_catatan) > 0): ?>

                <?php foreach ($data_catatan as $row): ?>

                    <div style="border:1px solid #ccc; padding:15px; margin-bottom:10px;">

                        <h3><?= htmlspecialchars($row['judul']) ?></h3>

                        <div>
                            <?= nl2br(htmlspecialchars($row['isi'])) ?>
                        </div>

                        <div>
                            <strong>Kategori:</strong>
                            <?= $row['nama_kategori'] ? htmlspecialchars($row['nama_kategori']) : 'Tidak ada' ?>
                        </div>

                        <div>
                            <strong>Oleh:</strong>
                            <?= htmlspecialchars($row['nama_admin']) ?>
                        </div>

                        <div style="margin-top:10px;">
                            <a href="index.php?act=catatan-edit&id=<?= $row['id']; ?>">Edit</a>
                            |
                            <a href="index.php?act=catatan-hapus&id=<?= $row['id']; ?>"
                               onclick="return confirm('Yakin ingin menghapus catatan ini?')">
                               Hapus
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>Belum ada catatan. Silakan tambah catatan baru.</p>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>