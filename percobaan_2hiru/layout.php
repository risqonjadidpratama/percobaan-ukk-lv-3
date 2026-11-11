<?php
// Tampilan bersama untuk halaman setelah login (include setelah auth.php)
function layout_atas($judul) { ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> - Portal Tugas</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<nav class="nav">
    <a class="brand" href="dashboard.php">Portal Tugas</a>
    <div class="links">
        <a href="dashboard.php">Tugas Saya</a>
        <a href="cari.php">Cari</a>
        <a href="profil.php">Profil</a>
        <?php if ($_SESSION['role'] === 'admin'): ?><a href="admin.php">Admin</a><?php endif; ?>
        <a class="out" href="logout.php">Logout (<?= e($_SESSION['nama_lengkap']) ?>)</a>
    </div>
</nav>
<main class="container">
<?php }

function layout_bawah() { ?>
</main>
</body>
</html>
<?php }

// Kartu tugas. Semua orang bisa like; Edit/Hapus hanya untuk pemilik atau admin.
function kartu_tugas($t, $kembali = 'dashboard') {
    $sudahLike = !empty($t['saya_like']);
    ?>
<article class="kartu" id="tugas-<?= (int)$t['id_tugas'] ?>">
    <?php if (!empty($t['video'])): ?>
        <video controls preload="metadata"<?= !empty($t['gambar']) ? ' poster="uploads/' . e($t['gambar']) . '"' : '' ?>>
            <source src="uploads/<?= e($t['video']) ?>">
            Browser Anda tidak mendukung pemutar video.
        </video>
    <?php elseif (!empty($t['gambar'])): ?>
        <img src="uploads/<?= e($t['gambar']) ?>" alt="<?= e($t['judul']) ?>" loading="lazy">
    <?php else: ?>
        <div class="noimg">Tanpa gambar / video</div>
    <?php endif; ?>
    <div class="isi">
        <h3><?= e($t['judul']) ?></h3>
        <p class="meta">Oleh <strong><?= e($t['nama_lengkap']) ?></strong> &middot; <?= e(date('d/m/Y', strtotime($t['created_at']))) ?></p>
        <p><?= nl2br(e($t['deskripsi'])) ?></p>

        <div class="like">
            <form method="POST" action="tugas_like.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$t['id_tugas'] ?>">
                <input type="hidden" name="kembali" value="<?= e($kembali) ?>">
                <input type="hidden" name="q" value="<?= e($_GET['q'] ?? '') ?>">
                <button type="submit" class="btn-like <?= $sudahLike ? 'aktif' : '' ?>" title="<?= $sudahLike ? 'Batal suka' : 'Suka' ?>">
                    <?= $sudahLike ? '♥' : '♡' ?> <?= (int)($t['jml_like'] ?? 0) ?>
                </button>
            </form>
        </div>

        <?php if (boleh_ubah($t['id_user'])): ?>
        <div class="aksi">
            <a class="btn kecil" href="tugas_edit.php?id=<?= (int)$t['id_tugas'] ?>">Edit</a>
            <form method="POST" action="tugas_hapus.php" onsubmit="return confirm('Hapus tugas ini?')">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$t['id_tugas'] ?>">
                <input type="hidden" name="kembali" value="<?= e($kembali) ?>">
                <button class="btn kecil merah" type="submit">Hapus</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</article>
<?php }