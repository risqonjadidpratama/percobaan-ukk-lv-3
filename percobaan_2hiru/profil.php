<?php
require 'auth.php';
wajib_login();
include 'koneksi.php';
include 'layout.php';

// Data pribadi: selalu dari id di session, tidak ada parameter id di URL,
// jadi user lain tidak mungkin membuka profil orang lain.
$stmt = mysqli_prepare($koneksi, "SELECT nama_lengkap, username, role, created_at,
    (SELECT COUNT(*) FROM tugas WHERE tugas.id_user = users.id_user) AS jumlah
    FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_user']);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

layout_atas('Profil');
?>
<div class="panel" style="max-width:520px">
    <h2>Profil Saya</h2>
    <table>
        <tr><th>Nama Lengkap</th><td><?= e($u['nama_lengkap']) ?></td></tr>
        <tr><th>Username</th><td><?= e($u['username']) ?></td></tr>
        <tr><th>Role</th><td><?= e($u['role']) ?></td></tr>
        <tr><th>Jumlah Tugas</th><td><?= (int)$u['jumlah'] ?></td></tr>
        <tr><th>Bergabung</th><td><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td></tr>
    </table>
</div>
<?php layout_bawah(); ?>