<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="/db_catatan/public/css/kategori.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle"></i> Tambah Kategori</h2>
                <a href="index.php?act=kategori" class="btn">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['error_msg'])):?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_msg']?>
                </div>
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>

            <form action="index.php?act=kategori-tambah-proses" method="POST" class="form-tambah">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Masukkan nama kategori baru" required>
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </button>
            </form>
        </div>
    </div>
</body>
</html>
