<?php
require 'auth.php';
wajib_login();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}
csrf_cek();

$id   = (int)($_POST['id'] ?? 0);
$user = (int)$_SESSION['id_user'];

$cek = mysqli_prepare($koneksi, "SELECT id_tugas FROM tugas WHERE id_tugas = ?");
mysqli_stmt_bind_param($cek, 'i', $id);
mysqli_stmt_execute($cek);
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
    http_response_code(404);
    exit('Tugas tidak ditemukan.');
}

$ins = mysqli_prepare($koneksi, "INSERT IGNORE INTO likes (id_user, id_tugas) VALUES (?, ?)");
mysqli_stmt_bind_param($ins, 'ii', $user, $id);
mysqli_stmt_execute($ins);
if (mysqli_stmt_affected_rows($ins) === 0) {
    $del = mysqli_prepare($koneksi, "DELETE FROM likes WHERE id_user = ? AND id_tugas = ?");
    mysqli_stmt_bind_param($del, 'ii', $user, $id);
    mysqli_stmt_execute($del);
}

$kembali = $_POST['kembali'] ?? '';
if ($kembali === 'cari') {
    $tujuan = 'cari.php?q=' . urlencode(trim($_POST['q'] ?? ''));
} elseif ($kembali === 'admin') {
    $tujuan = 'admin.php';
} else {
    $tujuan = 'dashboard.php';
}
header('Location: ' . $tujuan . '#tugas-' . $id);
exit;