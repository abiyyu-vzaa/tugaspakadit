<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="public/css/indexdashboard.css">    
    <style>
        body {
            display: block;
            background-color: #f4f4f9;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="#">Catatan</a>
        <div class="nav-btn">
            <a href="index.php?act=kategori" class="btn btn-danger">kategori</a>
            <a href="index.php?act=logout" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container-dashboard" style="margin-top: 80px;">
        <div class="card">
            <h3>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h3>
        </div>
    </div>
</body>
</html>
