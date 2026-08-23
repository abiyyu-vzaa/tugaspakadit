<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-shell">
        <div class="auth-side">
            <span class="stamp-big">CATATAN &middot; MVC</span>
            <h2>Setiap catatan punya tempatnya sendiri.</h2>
            <p>Kelola catatan kuliah, ide, dan tugasmu dalam satu katalog rapi — dikelompokkan per kategori, gampang dicari, gampang ditemukan lagi.</p>
            <div class="card-fan">
                <div class="fan-card" style="--r:-8deg;">No. 001</div>
                <div class="fan-card" style="--r:-2deg;">No. 002</div>
                <div class="fan-card" style="--r:4deg;">No. 003</div>
            </div>
        </div>

        <div class="auth-form-wrap">
            <div class="card">
                <div class="card-header">
                    <h2>Masuk</h2>
                    <p>Masuk untuk mengelola catatanmu.</p>
                </div>

                <?php include __DIR__ . '/../components/flash.php'; ?>

                <form action="index.php?act=login-process" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                               value="<?= e(old('username')) ?>" maxlength="50" required autofocus>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn"><i class="fas fa-right-to-bracket"></i> Masuk</button>
                </form>

                <div class="divider"><span>atau</span></div>

                <a href="index.php?act=register" class="text-center">Belum punya akun? Daftar</a>
            </div>
        </div>
    </div>
</body>
</html>
