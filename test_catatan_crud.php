<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/CatatanModel.php';

$database = new Database();
$db = $database->getConnection();

$model = new CatatanModel($db);

$adminId = 1;

echo "=== Test CRUD CatatanModel ===\n";

$created = $model->create($adminId, 'Judul Test Otomatis', 'Isi catatan untuk pengujian.', null);
echo $created ? "[PASS] create()\n" : "[FAIL] create()\n";

$list = $model->getAll($adminId);
$newId = $list[0]['id'] ?? null;

if (!$newId) {
    echo "[FAIL] Tidak bisa menemukan catatan yang baru dibuat, test dihentikan.\n";
    exit(1);
}

$catatan = $model->getById($newId, $adminId);
echo ($catatan && $catatan['judul'] === 'Judul Test Otomatis')
    ? "[PASS] getById() mengembalikan data yang benar\n"
    : "[FAIL] getById()\n";

$updated = $model->update($newId, $adminId, 'Judul Sudah Diubah', 'Isi sudah diubah.', null);
$afterUpdate = $model->getById($newId, $adminId);
echo ($updated && $afterUpdate['judul'] === 'Judul Sudah Diubah')
    ? "[PASS] update()\n"
    : "[FAIL] update()\n";

$deleted = $model->delete($newId, $adminId);
$afterDelete = $model->getById($newId, $adminId);
echo ($deleted && $afterDelete === false)
    ? "[PASS] delete() dan data benar-benar terhapus\n"
    : "[FAIL] delete()\n";

echo "=== Selesai ===\n";