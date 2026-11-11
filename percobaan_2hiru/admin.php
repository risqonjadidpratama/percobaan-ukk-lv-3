<?php
require 'auth.php';
wajib_admin(); // cek login + cek role admin
include 'koneksi.php';
include 'layout.php';

$users = mysqli_query($koneksi, "SELECT u.id_user, u.nama_lengkap, u.username, u.role, COUNT(t.id_tugas) AS jumlah
    FROM users u LEFT JOIN tugas t ON t.id_user = u.id_user GROUP BY u.id_user ORDER BY u.id_user");
$tugas = mysqli_query($koneksi, "SELECT t.*, u.nama_lengkap, " . sql_like_cols() . " FROM tugas t JOIN users u ON u.id_user = t.id_user ORDER BY t.created_at DESC");
$pesan = $_GET['pesan'] ?? '';

layout_atas('Halaman Admin');
?>
<div class="panel">
    <h2>Halaman Admin</h2>
    <?php if ($pesan === 'edit'): ?><div class="pesan ok">Tugas berhasil diperbarui.</div><?php endif; ?>
    <?php if ($pesan === 'hapus'): ?><div class="pesan ok">Tugas berhasil dihapus.</div><?php endif; ?>
    <table>
        <tr><th>ID</th><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Jumlah Tugas</th></tr>
        <?php while ($r = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><?= (int)$r['id_user'] ?></td>
            <td><?= e($r['nama_lengkap']) ?></td>
            <td><?= e($r['username']) ?></td>
            <td><?= e($r['role']) ?></td>
            <td><?= (int)$r['jumlah'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<h2>Semua Tugas Siswa</h2>
<div class="grid">
<?php
$ada = false;
while ($t = mysqli_fetch_assoc($tugas)) { $ada = true; kartu_tugas($t, 'admin'); }
?>
</div>
<?php if (!$ada): ?><p class="kosong">Belum ada tugas.</p><?php endif; ?>
<?php layout_bawah(); ?>