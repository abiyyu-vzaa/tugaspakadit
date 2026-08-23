<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <?php $active_menu = 'kategori'; include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="content">
        <div class="page-head">
            <div>
                <span class="eyebrow">Pengelompokan</span>
                <h2 style="font-size:26px;">Daftar Kategori</h2>
                <p class="sub"><?= count($data_kategori) ?> kategori tersedia.</p>
            </div>
            <a href="index.php?act=kategori-tambah" class="btn"><i class="fas fa-plus"></i> Tambah Kategori</a>
        </div>

        <?php include __DIR__ . '/../components/flash.php'; ?>

        <div class="card">
            <?php if (count($data_kategori) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Catatan</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($data_kategori as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="tag <?= kategori_tag_class($row['id']) ?>"><?= e($row['nama_kategori']) ?></span></td>
                                <td><?= (int) $row['jumlah_catatan'] ?></td>
                                <td><?= e($row['nama_admin']) ?></td>
                                <td>
                                    <a href="index.php?act=kategori-edit&id=<?= (int) $row['id'] ?>" class="btn btn-outline btn-sm">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <form method="POST" action="index.php?act=kategori-hapus" class="inline-form"
                                          onsubmit="return confirm('Yakin ingin menghapus kategori ini? Catatan di dalamnya tidak ikut terhapus.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Belum ada kategori.</p>
                    <a href="index.php?act=kategori-tambah" class="btn btn-sm"><i class="fas fa-plus"></i> Tambah Kategori</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
