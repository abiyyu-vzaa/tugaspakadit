<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan — Catatan</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <?php $active_menu = 'catatan'; include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="content">
        <div class="page-head">
            <div>
                <span class="eyebrow">Katalog</span>
                <h2 style="font-size:26px;">Daftar Catatan</h2>
                <p class="sub"><?= count($data_catatan) ?> catatan ditemukan.</p>
            </div>
            <a href="index.php?act=catatan-tambah" class="btn"><i class="fas fa-plus"></i> Catatan Baru</a>
        </div>

        <?php include __DIR__ . '/../components/flash.php'; ?>

        <form method="GET" action="index.php" class="toolbar">
            <input type="hidden" name="act" value="catatan">
            <div class="search-wrap">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Cari judul atau isi catatan..." value="<?= e($keyword) ?>">
            </div>
            <select name="kategori_id" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <?php foreach ($data_kategori as $k): ?>
                    <option value="<?= (int) $k['id'] ?>" <?= ((string) $kategori_id === (string) $k['id']) ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                <option value="terlama" <?= $sort === 'terlama' ? 'selected' : '' ?>>Terlama</option>
                <option value="az" <?= $sort === 'az' ? 'selected' : '' ?>>Judul A-Z</option>
                <option value="za" <?= $sort === 'za' ? 'selected' : '' ?>>Judul Z-A</option>
            </select>
            <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-filter"></i> Terapkan</button>
        </form>

        <?php if (count($data_catatan) > 0): ?>
            <div class="card-grid">
                <?php foreach ($data_catatan as $row): ?>
                    <div class="note-card <?= $row['is_pinned'] ? 'pinned' : '' ?>">
                        <div class="note-idx">No. <?= str_pad((string) $row['id'], 3, '0', STR_PAD_LEFT) ?></div>

                        <?php if ($row['nama_kategori']): ?>
                            <span class="tag <?= kategori_tag_class($row['kategori_id']) ?>"><?= e($row['nama_kategori']) ?></span>
                        <?php endif; ?>

                        <h3><?= e($row['judul']) ?></h3>
                        <div class="note-body"><?= nl2br(e(excerpt($row['isi'], 160))) ?></div>

                        <div class="note-foot">
                            <span><i class="far fa-clock"></i> <?= waktu_indo($row['created_at'] ?? null) ?> &middot; <?= e($row['nama_admin']) ?></span>
                            <div class="note-actions">
                                <form method="POST" action="index.php?act=catatan-pin" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <?php if ($keyword !== ''): ?><input type="hidden" name="q" value="<?= e($keyword) ?>"><?php endif; ?>
                                    <?php if ($kategori_id !== ''): ?><input type="hidden" name="kategori_id" value="<?= e($kategori_id) ?>"><?php endif; ?>
                                    <?php if ($sort !== 'terbaru'): ?><input type="hidden" name="sort" value="<?= e($sort) ?>"><?php endif; ?>
                                    <button type="submit" class="pin-btn <?= $row['is_pinned'] ? 'is-pinned' : '' ?>"
                                            title="<?= $row['is_pinned'] ? 'Lepas sematan' : 'Sematkan' ?>">
                                        <i class="fas fa-thumbtack"></i>
                                    </button>
                                </form>
                                <a href="index.php?act=catatan-edit&id=<?= (int) $row['id'] ?>" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="index.php?act=catatan-hapus" class="inline-form"
                                      onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <button type="submit" class="del-btn" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state card">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada catatan yang cocok. Coba kata kunci lain, atau buat catatan baru.</p>
                <a href="index.php?act=catatan-tambah" class="btn btn-sm"><i class="fas fa-plus"></i> Tambah Catatan</a>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
