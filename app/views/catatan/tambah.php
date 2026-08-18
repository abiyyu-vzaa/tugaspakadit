<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Catatan</title>
    <link rel="stylesheet" href="/db_catatan/public/css/kategori.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle"></i> Tambah Catatan</h2>
                <a href="index.php?act=catatan" class="btn">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_msg'] ?>
                </div>
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>

            <form action="index.php?act=catatan-tambah-proses" method="POST" class="form-tambah">
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Judul</label>
                    <input type="text" name="judul" class="form-control" placeholder="Masukkan judul catatan" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Isi Catatan</label>
                    <textarea name="isi" class="form-control" rows="5" placeholder="Masukkan isi catatan" required></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php if (count($data_kategori) > 0): ?>
                            <?php foreach ($data_kategori as $kategori): ?>
                                <option value="<?= $kategori['id'] ?>">
                                    <?= htmlspecialchars($kategori['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Tidak ada kategori tersedia</option>
                        <?php endif; ?>
            </select>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-plus"></i> Simpan Catatan
                </button>
            </form>
        </div>
    </div>
</body>
</html>

