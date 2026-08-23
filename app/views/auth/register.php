<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-shell">
        <div class="auth-side">
            <span class="stamp-big">CATATAN &middot; MVC</span>
            <h2>Buat katalog catatanmu sendiri.</h2>
            <p>Satu akun admin untuk mengelola semua catatan dan kategori. Registrasi cuma butuh username dan password.</p>
            <div class="card-fan">
                <div class="fan-card" style="--r:-8deg;">Kuliah</div>
                <div class="fan-card" style="--r:-2deg;">Ide</div>
                <div class="fan-card" style="--r:4deg;">Tugas</div>
            </div>
        </div>

        <div class="auth-form-wrap">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-user-plus"></i> Daftar Admin</h2>
                    <p>Buat akun baru untuk mengakses sistem.</p>
                </div>

                <?php include __DIR__ . '/../components/flash.php'; ?>

                <form action="index.php?act=register-process" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                               value="<?= e(old('username')) ?>" maxlength="50" required autofocus>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Konfirmasi Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" minlength="6" required>
                    </div>
                    <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Daftar Sekarang</button>
                </form>

                <div class="divider"><span>atau</span></div>

                <a href="index.php?act=login" class="text-center"><i class="fas fa-arrow-left"></i> Sudah punya akun? Masuk</a>
            </div>
        </div>
    </div>
</body>
</html>
