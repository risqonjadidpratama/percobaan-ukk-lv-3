<?php
// Fungsi keamanan bersama. Di-include di baris paling atas setiap halaman.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function wajib_login() {
    if (empty($_SESSION['login'])) {
        header('Location: index.php');
        exit;
    }
}

function wajib_admin() {
    wajib_login();
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit('Akses Ditolak!');
    }
}

// Hanya pemilik data atau admin yang boleh mengubah/menghapus
function boleh_ubah($id_pemilik) {
    return !empty($_SESSION['login']) &&
           ((int)$_SESSION['id_user'] === (int)$id_pemilik || $_SESSION['role'] === 'admin');
}

// Perlindungan CSRF
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_cek() {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(400);
        exit('Token keamanan tidak valid. Kembali dan coba lagi.');
    }
}

// Upload gambar aman: cek ukuran, tipe asli file (bukan hanya ekstensi), nama acak
function simpan_gambar($f, &$error) {
    $error = '';
    if (!isset($f) || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK) { $error = 'Upload gambar gagal.'; return null; }
    if ($f['size'] > 2 * 1024 * 1024) { $error = 'Ukuran gambar maksimal 2 MB.'; return null; }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $boleh = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($boleh[$mime])) { $error = 'Format gambar harus JPG, PNG, WEBP, atau GIF.'; return null; }

    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $nama = bin2hex(random_bytes(16)) . '.' . $boleh[$mime];
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $nama)) {
        $error = 'Gagal menyimpan gambar.';
        return null;
    }
    return $nama;
}

function hapus_gambar($nama) {
    if ($nama) {
        $path = __DIR__ . '/uploads/' . basename($nama);
        if (is_file($path)) unlink($path);
    }
}

// Kolom tambahan untuk query tugas: jumlah like & apakah user ini sudah like
function sql_like_cols() {
    $id = (int)$_SESSION['id_user'];
    return "(SELECT COUNT(*) FROM likes l WHERE l.id_tugas = t.id_tugas) AS jml_like, " .
           "(SELECT COUNT(*) FROM likes l WHERE l.id_tugas = t.id_tugas AND l.id_user = $id) AS saya_like";
}

// Upload video aman: maks. 20 MB, cek tipe asli file, nama acak
function simpan_video($f, &$error) {
    $error = '';
    if (!isset($f) || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = 'Ukuran video melebihi batas server.'; return null;
    }
    if ($f['error'] !== UPLOAD_ERR_OK) { $error = 'Upload video gagal.'; return null; }
    if ($f['size'] > 20 * 1024 * 1024) { $error = 'Ukuran video maksimal 20 MB.'; return null; }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $boleh = ['video/mp4' => 'mp4', 'video/x-m4v' => 'mp4', 'video/webm' => 'webm', 'video/ogg' => 'ogv'];
    if (!isset($boleh[$mime])) { $error = 'Format video harus MP4, WEBM, atau OGG.'; return null; }

    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $nama = bin2hex(random_bytes(16)) . '.' . $boleh[$mime];
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $nama)) {
        $error = 'Gagal menyimpan video.'; return null;
    }
    return $nama;
}

function hapus_video($nama) {
    hapus_gambar($nama); // sama: hapus file di folder uploads
}