<?php
require 'auth.php'; // memanggil session_start() di baris paling atas
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_cek();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($koneksi, "SELECT id_user, nama_lengkap, username, password, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($data && password_verify($password, $data['password'])) {
    session_regenerate_id(true);
    $_SESSION['login']        = true;
    $_SESSION['id_user']      = (int)$data['id_user'];
    $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
    $_SESSION['username']     = $data['username'];
    $_SESSION['role']         = $data['role'];

    header('Location: ' . ($data['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
    exit;
}

header('Location: index.php?pesan=gagal');
exit;