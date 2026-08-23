<?php
// Komponen pesan flash + pemulihan old input.
// Include komponen ini di atas form agar old() bisa dipakai mengisi ulang form.

// Ambil input lama (kalau ada) sebelum dibersihkan dari session.
$GLOBALS['__old_input'] = $_SESSION['_old_input'] ?? [];
unset($_SESSION['_old_input']);

// Dukung kunci lama (success_msg / sucsess_msg / error_msg) agar tidak ada pesan hilang.
$__success = $_SESSION['flash_success'] ?? $_SESSION['success_msg'] ?? $_SESSION['sucsess_msg'] ?? null;
$__error   = $_SESSION['flash_error']   ?? $_SESSION['error_msg'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['success_msg'], $_SESSION['sucsess_msg'],
      $_SESSION['flash_error'], $_SESSION['error_msg']);
?>
<?php if ($__success): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> <span><?= e($__success) ?></span></div>
<?php endif; ?>

<?php if ($__error): ?>
    <div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> <span><?= e($__error) ?></span></div>
<?php endif; ?>
