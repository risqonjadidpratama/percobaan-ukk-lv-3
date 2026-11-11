<?php
require 'auth.php';
wajib_login();
include 'koneksi.php';
include 'layout.php';

$error = '';
$judul = '';
$deskripsi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jika file melebihi post_max_size, PHP mengosongkan $_POST dan $_FILES
    if (empty($_POST) && empty($_FILES)) {
        $error = 'Ukuran file melebihi batas server. Gunakan video maksimal 20 MB.';
    } else {
        csrf_cek();
        $judul     = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if ($judul === '' || mb_strlen($judul) > 100) {
            $error = 'Judul wajib diisi (maksimal 100 karakter).';
        } elseif (mb_strlen($deskripsi) > 2000) {
            $error = 'Deskripsi maksimal 2000 karakter.';
        } else {
            $errG = $errV = '';
            $gambar = simpan_gambar($_FILES['gambar'] ?? null, $errG);
            $video  = $errG === '' ? simpan_video($_FILES['video'] ?? null, $errV) : null;
            $error  = $errG !== '' ? $errG : $errV;

            if ($error === '') {
                // id_user selalu diambil dari session, tidak bisa dipalsukan lewat form
                $stmt = mysqli_prepare($koneksi, "INSERT INTO tugas (id_user, judul, deskripsi, gambar, video) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'issss', $_SESSION['id_user'], $judul, $deskripsi, $gambar, $video);
                mysqli_stmt_execute($stmt);
                header('Location: dashboard.php?pesan=tambah');
                exit;
            }
            hapus_gambar($gambar);
            hapus_video($video);
        }
    }
}

layout_atas('Tambah Tugas');
?>
<div class="panel" style="max-width:560px">
    <h2>Tambah Tugas / Proyek</h2>
    <?php if ($error): ?><div class="pesan err"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label>Judul</label>
        <input type="text" name="judul" value="<?= e($judul) ?>" required maxlength="100">
        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="5" maxlength="2000"><?= e($deskripsi) ?></textarea>
        <label>Gambar (JPG/PNG/WEBP/GIF, maks. 2 MB) - opsional</label>
        <input type="file" name="gambar" accept="image/*">
        <label>Video (MP4/WEBM/OGG, maks. 20 MB) - opsional</label>
        <input type="file" name="video" accept="video/mp4,video/webm,video/ogg">
        <button type="submit">Simpan</button>
        <a class="btn abu" href="dashboard.php">Batal</a>
    </form>
</div>
<?php layout_bawah(); ?>