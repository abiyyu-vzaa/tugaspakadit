<?php
// =============================================================
// Smoke test end-to-end aplikasi Catatan.
// Jalankan: php test_smoke.php  (dari folder project)
// Memakai database sesuai config/database.php.
// =============================================================
error_reporting(E_ALL);
define('BASE', 'http://127.0.0.1:8088/index.php');
define('COOKIE', sys_get_temp_dir() . '/catatan_smoke_cookies.txt');
@unlink(COOKIE);

include_once __DIR__ . '/config/database.php';

$pass = 0; $fail = 0;
function check($name, $cond) {
    global $pass, $fail;
    if ($cond) { $pass++; echo "[PASS] $name\n"; }
    else       { $fail++; echo "[FAIL] $name\n"; }
}

function req($qs, $post = null, $jar = COOKIE) {
    $ch = curl_init(BASE . '?' . $qs);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $jar,
        CURLOPT_COOKIEFILE => $jar,
    ]);
    if (is_array($post)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $body = curl_exec($ch);
    $url  = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return [$body, $url];
}

function csrf_token() {
    // Ambil dari halaman form: saat login terbuka form tambah,
    // saat belum login request di-redirect ke halaman login (juga punya token).
    [$html] = req('act=catatan-tambah');
    preg_match('/name="_csrf" value="([^"]+)"/', $html, $m);
    return $m[1] ?? '';
}

$suffix = substr((string) time(), -6);
$u1 = "smoke_a_$suffix"; $u2 = "smoke_b_$suffix";
$pwd = 'rahasia123';

// ---------- Register user pertama ----------
$t = csrf_token();
check('register: token CSRF tersedia', $t !== '');

[$body, $url] = req('act=register-process', ['_csrf' => $t, 'username' => $u1, 'password' => $pwd, 'confirm_password' => $pwd]);
check('register: sukses -> redirect login', strpos($url, 'act=login') !== false && strpos($body, 'Registrasi berhasil') !== false);

[$body] = req('act=register-process', ['_csrf' => csrf_token(), 'username' => $u1, 'password' => $pwd, 'confirm_password' => $pwd]);
check('register: username duplikat ditolak', strpos($body, 'sudah dipakai') !== false);

[$body] = req('act=register-process', ['_csrf' => csrf_token(), 'username' => "smoke_x_$suffix", 'password' => 'abc', 'confirm_password' => 'xyz']);
check('register: konfirmasi password tidak cocok ditolak', strpos($body, 'tidak cocok') !== false);

// Tanpa token CSRF harus ditolak.
[$body] = req('act=register-process', ['username' => "smoke_y_$suffix", 'password' => $pwd, 'confirm_password' => $pwd]);
check('register: tanpa token CSRF ditolak', strpos($body, 'tidak valid') !== false || strpos($body, 'ditolak') !== false);

// ---------- Login ----------
[$body, $url] = req('act=login-process', ['_csrf' => csrf_token(), 'username' => $u1, 'password' => 'salah_salah']);
check('login: password salah ditolak', strpos($body, 'salah') !== false && strpos($url, 'act=dashboard') === false);

[$body, $url] = req('act=login-process', ['_csrf' => csrf_token(), 'username' => $u1, 'password' => $pwd]);
check('login: sukses -> dashboard', strpos($url, 'act=dashboard') !== false && strpos($body, 'Selamat datang') !== false);

// Halaman terproteksi tanpa login (jar cookie kosong terisolasi).
$empty_jar = sys_get_temp_dir() . '/catatan_smoke_empty.txt';
@unlink($empty_jar);
[$body, $url] = req('act=dashboard', null, $empty_jar);
if (strpos($url, 'act=login') === false) { echo "  DEBUG guard -> $url\n"; }
check('guard: dashboard tanpa login -> redirect login', strpos($url, 'act=login') !== false);

// ---------- CRUD Kategori ----------
[$body, $url] = req('act=kategori-tambah-proses', ['_csrf' => csrf_token(), 'nama_kategori' => 'Kerja']);
check('kategori: tambah sukses', strpos($url, 'act=kategori') !== false && strpos($body, 'berhasil ditambahkan') !== false);
preg_match('/act=kategori-edit&id=(\d+)/', $body, $m);
$kat_id = (int) ($m[1] ?? 0);
check('kategori: tampil di daftar', $kat_id > 0);

[$body] = req('act=kategori-tambah-proses', ['_csrf' => csrf_token(), 'nama_kategori' => 'Kerja']);
check('kategori: duplikat ditolak', strpos($body, 'sudah ada') !== false);

[$body] = req('act=kategori-tambah-proses', ['_csrf' => csrf_token(), 'nama_kategori' => 'Pribadi']);
preg_match_all('/act=kategori-edit&id=(\d+)/', $body, $mm);
$kat2_id = 0;
foreach ($mm[1] as $kid) { if ((int)$kid !== $kat_id) { $kat2_id = (int)$kid; break; } }

[$body, $url] = req('act=kategori-edit-proses', ['_csrf' => csrf_token(), 'id' => $kat_id, 'nama_kategori' => 'Kerjaan']);
check('kategori: edit sukses', strpos($body, 'berhasil diperbarui') !== false && strpos($body, 'Kerjaan') !== false);

// ---------- CRUD Catatan ----------
[$body, $url] = req('act=catatan-tambah-proses', ['_csrf' => csrf_token(), 'judul' => 'Catatan & Uji <script>', 'isi' => "Isi baris satu\nbaris dua", 'kategori_id' => $kat_id]);
check('catatan: tambah sukses', strpos($url, 'act=catatan') !== false && strpos($body, 'berhasil ditambahkan') !== false);
check('catatan: XSS di-escape saat tampil', strpos($body, '&lt;script&gt;') !== false && strpos($body, '<script>alert') === false);
check('catatan: ampersand tidak double-encoded', strpos($body, 'Catatan &amp; Uji') !== false && strpos($body, '&amp;amp;') === false);
preg_match('/act=catatan-edit&id=(\d+)/', $body, $m);
$note_id = (int) ($m[1] ?? 0);
check('catatan: kartu tampil di daftar', $note_id > 0);

[$body, $url] = req('act=catatan-tambah-proses', ['_csrf' => csrf_token(), 'judul' => '', 'isi' => '', 'kategori_id' => '']);
check('catatan: judul kosong ditolak + balik ke form', strpos($url, 'catatan-tambah') !== false && strpos($body, 'tidak boleh kosong') !== false);

[$body, $url] = req('act=catatan-tambah-proses', ['_csrf' => csrf_token(), 'judul' => 'Tanpa Kategori', 'isi' => 'Bebas tanpa kategori', 'kategori_id' => '']);
check('catatan: tanpa kategori (NULL) sukses', strpos($body, 'berhasil ditambahkan') !== false);
preg_match_all('/act=catatan-edit&id=(\d+)/', $body, $mm);
$note2_id = 0;
foreach ($mm[1] as $nid) { if ((int)$nid !== $note_id) { $note2_id = (int)$nid; break; } }

[$body, $url] = req('act=catatan-edit&id=999999');
check('catatan: edit ID tak ditemukan -> redirect daftar', strpos($url, 'act=catatan') !== false && strpos($body, 'tidak ditemukan') !== false);

[$body, $url] = req('act=catatan-edit&id=' . $note_id);
check('catatan: halaman edit terbuka', strpos($body, 'Edit Catatan') !== false && strpos($body, 'Kerjaan') !== false);

[$body, $url] = req('act=catatan-edit-proses', ['_csrf' => csrf_token(), 'id' => $note_id, 'judul' => 'Judul Baru', 'isi' => 'Isi sudah diubah', 'kategori_id' => $kat2_id]);
check('catatan: edit sukses', strpos($body, 'berhasil diperbarui') !== false && strpos($body, 'Judul Baru') !== false);

// ---------- Pin + filter state ----------
[$body, $url] = req('act=catatan-pin', ['_csrf' => csrf_token(), 'id' => $note2_id]);
check('pin: sematkan sukses', strpos($url, 'act=catatan') !== false && strpos($body, 'pinned') !== false);
check('pin: catatan disematkan naik ke atas', strpos($body, 'Tanpa Kategori') < strpos($body, 'Judul Baru'));

[$body, $url] = req('act=catatan&q=' . urlencode('Judul Baru'));
check('cari: keyword menemukan catatan', substr_count($body, 'note-card') === 1 && strpos($body, 'Judul Baru') !== false);

[$body, $url] = req('act=catatan&kategori_id=' . $kat_id);
check('filter: per kategori', strpos($body, 'Judul Baru') === false);

// ---------- Isolasi data antar akun ----------
// Logout user 1, buat & login user 2.
req('act=logout');
req('act=register-process', ['_csrf' => csrf_token(), 'username' => $u2, 'password' => $pwd, 'confirm_password' => $pwd]);
req('act=login-process', ['_csrf' => csrf_token(), 'username' => $u2, 'password' => $pwd]);

[$body] = req('act=catatan');
check('isolasi: user lain tidak melihat catatan user 1', strpos($body, 'Judul Baru') === false);

[$body, $url] = req('act=catatan-edit&id=' . $note_id);
check('isolasi: edit catatan milik user lain ditolak', strpos($body, 'tidak ditemukan') !== false);

[$body] = req('act=catatan-hapus', ['_csrf' => csrf_token(), 'id' => $note_id]);
check('isolasi: hapus catatan milik user lain ditolak', strpos($body, 'tidak ditemukan') !== false);

// GET ke endpoint POST harus ditolak.
[$body, $url] = req('act=catatan-hapus&id=' . $note2_id);
check('keamanan: hapus via GET ditolak', strpos($url, 'act=catatan') !== false);

// ---------- Hapus kategori: catatan tidak ikut hilang ----------
req('act=logout');
req('act=login-process', ['_csrf' => csrf_token(), 'username' => $u1, 'password' => $pwd]);
[$body] = req('act=kategori-hapus', ['_csrf' => csrf_token(), 'id' => $kat2_id]);
check('kategori: hapus mengumumkan catatan terdampak', strpos($body, 'tanpa kategori') !== false);
[$body] = req('act=catatan');
check('kategori: catatan tetap ada setelah kategorinya dihapus', strpos($body, 'Judul Baru') !== false);

// ---------- Logout ----------
[$body, $url] = req('act=logout');
if (strpos($url, 'act=login') === false || strpos($body, 'telah keluar') === false) { echo "  DEBUG logout -> $url | " . substr(trim($body), 0, 200) . "\n"; }
check('logout: kembali ke login + pesan', strpos($url, 'act=login') !== false && strpos($body, 'telah keluar') !== false);

// ---------- Bersihkan data uji ----------
$db = (new Database())->getConnection();
foreach ([$u1, $u2] as $u) {
    $st = $db->prepare("DELETE FROM admin WHERE username = ?"); // cascade ke kategori & catatan
    $st->execute([$u]);
}
echo "\n----\nHASIL: $pass pass, $fail fail\n";
exit($fail > 0 ? 1 : 0);