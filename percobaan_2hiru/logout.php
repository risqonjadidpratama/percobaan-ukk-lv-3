<?php
session_start();       // panggil kembali sesi yang sedang aktif
session_unset();       // kosongkan semua data di variabel sesi
session_destroy();     // hancurkan sesi sepenuhnya dari server
header('Location: index.php?pesan=logout');
exit;