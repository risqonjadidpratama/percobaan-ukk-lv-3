<?php
require 'auth.php';
if (!empty($_SESSION['login'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
    exit;
}
$pesan = $_GET['pesan'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Portal Tugas</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="tengah">
    <form class="panel kecilbox" method="POST" action="proses_login.php">
        <h2>Login</h2>
        <?php if ($pesan === 'gagal'): ?><div class="pesan err">Username atau password salah.</div><?php endif; ?>
        <?php if ($pesan === 'logout'): ?><div class="pesan ok">Anda telah logout.</div><?php endif; ?>
        <?php if ($pesan === 'daftar'): ?><div class="pesan ok">Akun berhasil dibuat. Silakan login.</div><?php endif; ?>
        <?= csrf_field() ?>
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" style="width:100%">Masuk</button>
        <p class="tautan">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </form>
</div>
</body>
</html>