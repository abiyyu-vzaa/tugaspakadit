<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <?php $active_menu = 'dashboard'; include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="content">
        <div class="page-head">
            <div>
                <span class="eyebrow">Ringkasan</span>
                <h2 style="font-size:26px;">Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
                <p class="sub">Begini kondisi catatanmu hari ini.</p>
            </div>
            <a href="index.php?act=catatan-tambah" class="btn"><i class="fas fa-plus"></i> Catatan Baru</a>
        </div>

        <?php include __DIR__ . '/../components/flash.php'; ?>

        <div class="stat-row">
            <div class="stat-tile">
                <span class="num"><?= (int) $total_catatan ?></span>
                <span class="label">Total Catatan</span>
            </div>
            <div class="stat-tile accent-navy">
                <span class="num"><?= (int) $total_kategori ?></span>
                <span class="label">Kategori</span>
            </div>
            <div class="stat-tile accent-amber">
                <span class="num"><?= (int) $total_disematkan ?></span>
                <span class="label">Disematkan</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;">Catatan Terbaru</h3>
                <a href="index.php?act=catatan" class="btn btn-outline btn-sm">Lihat semua <i class="fas fa-arrow-right"></i></a>
            </div>

            <?php if (count($catatan_terbaru) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Ditulis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catatan_terbaru as $row): ?>
                            <tr>
                                <td>
                                    <a href="index.php?act=catatan-edit&id=<?= (int) $row['id'] ?>" style="font-weight:600; color:var(--navy-dark);">
                                        <?= $row['is_pinned'] ? '<i class="fas fa-thumbtack" style="color:var(--amber); font-size:11px;"></i> ' : '' ?><?= e($row['judul']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($row['nama_kategori']): ?>
                                        <span class="tag <?= kategori_tag_class($row['kategori_id']) ?>"><?= e($row['nama_kategori']) ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--ink-soft); font-size:12px;">Tanpa kategori</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= waktu_indo($row['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-note-sticky"></i>
                    <p>Belum ada catatan. Yuk buat yang pertama.</p>
                    <a href="index.php?act=catatan-tambah" class="btn btn-sm"><i class="fas fa-plus"></i> Tambah Catatan</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
