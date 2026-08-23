<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <?php $active_menu = 'kategori'; include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="content" style="max-width:520px;">
        <div class="page-head">
            <div>
                <span class="eyebrow">Ubah</span>
                <h2 style="font-size:24px;">Edit Kategori</h2>
            </div>
            <a href="index.php?act=kategori" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <?php include __DIR__ . '/../components/flash.php'; ?>

        <div class="card">
            <form action="index.php?act=kategori-edit-proses" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $kategori['id'] ?>">

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control"
                           value="<?= e(old('nama_kategori', $kategori['nama_kategori'])) ?>" maxlength="100" required>
                </div>

                <button type="submit" class="btn"><i class="fas fa-save"></i> Update Kategori</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
