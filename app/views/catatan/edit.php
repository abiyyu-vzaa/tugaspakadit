<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Catatan — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <?php $active_menu = 'catatan'; include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="content" style="max-width:640px;">
        <div class="page-head">
            <div>
                <span class="eyebrow">No. <?= str_pad((string) $catatan['id'], 3, '0', STR_PAD_LEFT) ?></span>
                <h2 style="font-size:24px;">Edit Catatan</h2>
            </div>
            <a href="index.php?act=catatan" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <?php include __DIR__ . '/../components/flash.php'; ?>

        <div class="card">
            <form action="index.php?act=catatan-edit-proses" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $catatan['id'] ?>">

                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Judul</label>
                    <input type="text" name="judul" class="form-control"
                           value="<?= e(old('judul', $catatan['judul'])) ?>"
                           placeholder="Masukkan judul catatan" maxlength="150" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Isi Catatan</label>
                    <textarea name="isi" class="form-control" rows="7"
                              placeholder="Masukkan isi catatan" required><?= e(old('isi', $catatan['isi'])) ?></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Kategori <span class="form-optional">(opsional)</span></label>
                    <select name="kategori_id" class="form-control">
                        <option value="">Tanpa kategori</option>
                        <?php foreach ($data_kategori as $kategori): ?>
                            <option value="<?= (int) $kategori['id'] ?>"
                                    <?= ((string) old('kategori_id', $catatan['kategori_id']) === (string) $kategori['id']) ? 'selected' : '' ?>>
                                <?= e($kategori['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (count($data_kategori) === 0): ?>
                        <p class="form-hint">Belum punya kategori? <a href="index.php?act=kategori-tambah">Buat kategori dulu</a> bila mau mengelompokkan catatan.</p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
