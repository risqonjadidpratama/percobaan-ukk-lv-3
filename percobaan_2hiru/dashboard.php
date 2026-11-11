<?php
require 'auth.php';
wajib_login();
include 'koneksi.php';
include 'layout.php';

// Hanya mengambil tugas milik user yang sedang login (id dari session, bukan dari URL)
$stmt = mysqli_prepare($koneksi, "SELECT t.*, u.nama_lengkap, " . sql_like_cols() . " FROM tugas t JOIN users u ON u.id_user = t.id_user WHERE t.id_user = ? ORDER BY t.created_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_user']);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$pesan = $_GET['pesan'] ?? '';

layout_atas('Tugas Saya');
?>
<div class="panel">
    <h2>Halo, <?= e($_SESSION['nama_lengkap']) ?>!</h2>
    <form class="cari" method="GET" action="cari.php">
        <input type="search" name="q" placeholder="Cari tugas atau proyek siswa lain...">
        <button type="submit">Cari</button>
    </form>
</div>

<?php if ($pesan === 'tambah'): ?><div class="pesan ok">Tugas berhasil ditambahkan.</div><?php endif; ?>
<?php if ($pesan === 'edit'): ?><div class="pesan ok">Tugas berhasil diperbarui.</div><?php endif; ?>
<?php if ($pesan === 'hapus'): ?><div class="pesan ok">Tugas berhasil dihapus.</div><?php endif; ?>

<p><a class="btn" href="tugas_tambah.php">+ Tambah Tugas / Proyek</a></p>

<div class="grid">
<?php
$ada = false;
while ($t = mysqli_fetch_assoc($hasil)) { $ada = true; kartu_tugas($t, 'dashboard'); }
?>
</div>
<?php if (!$ada): ?><p class="kosong">Belum ada tugas. Klik "Tambah Tugas / Proyek" untuk mulai.</p><?php endif; ?>
<?php layout_bawah(); ?>