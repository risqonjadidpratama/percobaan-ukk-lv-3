<?php
// Koneksi ke database (pastikan nama DB sama dengan di phpMyAdmin)
$koneksi = mysqli_connect('localhost', 'root', '', 'percobaan2');
if (!$koneksi) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
mysqli_set_charset($koneksi, 'utf8mb4');