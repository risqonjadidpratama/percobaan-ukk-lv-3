<?php
require 'auth.php';
wajib_login();
include 'koneksi.php';
include 'layout.php';

$q = trim($_GET['q'] ?? '');
$like = '%' . addcslashes($q, '%_\\') . '%';

// Semua siswa bisa MELIHAT tugas orang lain; ubah/hapus hanya untuk pemilik atau admin
$stmt = mysqli_prepare($koneksi, "SELECT t.*, u.nama_lengkap, " . sql_like_cols() . " FROM tugas t JOIN users u ON u.id_user = t.id_user
    WHERE t.judul LIKE ? OR t.deskripsi LIKE ? OR u.nama_lengkap LIKE ?
    ORDER BY t.created_at DESC LIMIT 60");
mysqli_stmt_bind_param($stmt, 'sss', $like, $like, $like);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

layout_atas('Cari');
?>
<div class="panel">
    <h2>Cari Tugas & Proyek</h2>
    <form class="cari" method="GET">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Judul, deskripsi, atau nama siswa..." autofocus>
        <button type="submit">Cari</button>
    </form>
</div>

<div class="grid">
<?php
$ada = false;
while ($t = mysqli_fetch_assoc($hasil)) { $ada = true; kartu_tugas($t, 'cari'); }
?>
</div>
<?php if (!$ada): ?><p class="kosong">Tidak ada hasil<?= $q !== '' ? ' untuk "' . e($q) . '"' : '' ?>.</p><?php endif; ?>
<?php layout_bawah(); ?>