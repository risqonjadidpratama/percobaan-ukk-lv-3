<?php
require 'auth.php';
include 'koneksi.php';

if (!empty($_SESSION['login'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$nama = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama     = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirm  = $_POST['konfirmasi'] ?? '';

    if ($nama === '' || mb_strlen($nama) > 100) {
        $error = 'Nama lengkap wajib diisi (maksimal 100 karakter).';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $error = 'Username 3-30 karakter: huruf, angka, atau garis bawah.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, 'user')");
        mysqli_stmt_bind_param($stmt, 'sss', $nama, $username, $hash);
        $berhasil = false;
        try {
            $berhasil = mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $ex) {
            $berhasil = false; // biasanya username sudah dipakai
        }
        if ($berhasil) {
            header('Location: index.php?pesan=daftar');
            exit;
        }
        $error = 'Username sudah dipakai. Pilih username lain.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Portal Tugas</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="tengah">
    <form class="panel kecilbox" method="POST">
        <h2>Buat Akun</h2>
        <?php if ($error): ?><div class="pesan err"><?= e($error) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" value="<?= e($nama) ?>" required>
        <label>Username</label>
        <input type="text" name="username" value="<?= e($username) ?>" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Ulangi Password</label>
        <input type="password" name="konfirmasi" required>
        <button type="submit" style="width:100%">Daftar</button>
        <p class="tautan">Sudah punya akun? <a href="index.php">Login</a></p>
    </form>
</div>
</body>
</html>