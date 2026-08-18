<!DOCTYPE html>
<html>
<head>
    <title>Register Admin</title>
    <link rel="stylesheet" href="/db_catatan/public/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus"></i> Daftar Admin</h2>
                <p>Buat akun baru untuk mengakses sistem</p>
            </div>

            <?php if(isset($error)) echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> $error</div>"; ?>
            
            <form action="/db_catatan/index.php?act=register-process" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password" required>
                </div>
                <button type="submit" class="btn">Daftar Sekarang</button>
            </form>
            
            <div class="divider"><span>atau</span></div>
            
            <a href="/db_catatan/app/views/auth/login.php" class="text-center">
                <i class="fas fa-sign-in-alt"></i> Sudah punya akun? Login
            </a>
        </div>
    </div>
</body>
</html>
