<?php
// ============================================================================
// Kumpulan fungsi bantu: keamanan (CSRF, auth guard), alur (flash, redirect,
// old input), dan tampilan (excerpt, waktu, warna tag).
// ============================================================================

/* ---------- Escape output (wajib dipakai di semua view) ---------- */
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

/* ---------- Status login ---------- */
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['admin_id']);
    }
}

/* ---------- Penjaga halaman: wajib login, kalau belum lempar ke login ---------- */
if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            flash_set('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
            redirect('index.php?act=login');
        }
    }
}

/* ---------- Flash message (hanya tampil sekali) ---------- */
if (!function_exists('flash_set')) {
    function flash_set($type, $message) {
        $_SESSION['flash_' . $type] = $message;
    }
}

/* ---------- Redirect lalu hentikan eksekusi ---------- */
if (!function_exists('redirect')) {
    function redirect($location) {
        header('Location: ' . $location);
        exit;
    }
}

/* ---------- Proteksi CSRF ---------- */
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
    }
}

// Panggil di awal setiap endpoint POST. Token tidak valid -> kembali ke halaman asal.
if (!function_exists('verify_csrf')) {
    function verify_csrf() {
        $sent = $_POST['_csrf'] ?? '';
        if (!is_string($sent) || $sent === '' || !hash_equals(csrf_token(), $sent)) {
            flash_set('error', 'Permintaan ditolak: sesi formulir tidak valid. Silakan coba lagi.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
        }
    }
}

/* ---------- Old input: kembalikan isian form setelah gagal validasi ---------- */
if (!function_exists('keep_old_input')) {
    function keep_old_input(array $keys) {
        $old = [];
        foreach ($keys as $key) {
            $old[$key] = $_POST[$key] ?? '';
        }
        $_SESSION['_old_input'] = $old;
    }
}

// Dipakai di view untuk mengisi ulang form, mis. value diisi e(old('judul')).
// Nilai old diambil oleh components/flash.php sebelum form dirender.
if (!function_exists('old')) {
    function old($key, $default = '') {
        return $GLOBALS['__old_input'][$key] ?? $default;
    }
}

/* ---------- Bantu tampilan ---------- */
if (!function_exists('kategori_tag_class')) {
    function kategori_tag_class($seed) {
        $n = is_numeric($seed) ? (int) $seed : crc32((string) $seed);
        return 'tag-' . ($n % 6);
    }
}

if (!function_exists('excerpt')) {
    function excerpt($text, $len = 120) {
        $text = trim(strip_tags($text));
        if (function_exists('mb_strlen')) {
            if (mb_strlen($text) <= $len) return $text;
            return mb_substr($text, 0, $len) . '…';
        }
        if (strlen($text) <= $len) return $text;
        return substr($text, 0, $len) . '…';
    }
}

if (!function_exists('waktu_indo')) {
    function waktu_indo($datetime) {
        if (empty($datetime)) return '-';
        $ts = strtotime($datetime);
        if (!$ts) return '-';
        $bulan = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
}
