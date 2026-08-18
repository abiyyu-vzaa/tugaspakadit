<!DOCTYPE html>
<html lang="en">
<head>
    <title>login</title>
    <link rel="stylesheet" href="/db_catatan/public/css/login.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Login</h1>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>" ?>
            <?php if(isset($success)) echo "<div class='alertalert-success'>$success</div>" ?>

            <form action="/db_catatan/index.php?act=login-process" method="POST">
            <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            </form>
            <a href="app/views/auth/register.php" class="text-center">belum punya akun? daftar</a>
        </div>
    </div>
</body>
</html>