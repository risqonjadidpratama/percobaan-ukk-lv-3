<?php
require 'auth.php';
wajib_login();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}
csrf_cek();

$id = (int)($_POST['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT id_user, gambar, video FROM tugas WHERE id_tugas = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$t) { http_response_code(404); exit('Tugas tidak ditemukan.'); }
if (!boleh_ubah($t['id_user'])) { http_response_code(403); exit('Akses Ditolak!'); }

$del = mysqli_prepare($koneksi, "DELETE FROM tugas WHERE id_tugas = ?");
mysqli_stmt_bind_param($del, 'i', $id);
mysqli_stmt_execute($del);
hapus_gambar($t['gambar']);
hapus_video($t['video']);

$peta = ['dashboard' => 'dashboard.php', 'admin' => 'admin.php', 'cari' => 'cari.php'];
$tujuan = $peta[$_POST['kembali'] ?? ''] ?? 'dashboard.php';
header('Location: ' . $tujuan . ($tujuan === 'cari.php' ? '' : '?pesan=hapus'));
exit;