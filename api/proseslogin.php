<?php
session_start();

// Matikan redirect otomatis agar kita bisa baca error
// error_reporting diaktifkan penuh
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Memulai pengecekan...<br>";

if (!file_exists('koneksi.php')) {
    die("Error: File koneksi.php tidak ditemukan di folder api/");
}

include 'koneksi.php';

if (isset($conn)) {
    echo "Variabel koneksi (\$conn) berhasil ditemukan.<br>";
} else {
    die("Error: Variabel \$conn tetap NULL. Periksa isi koneksi.php");
}

echo "Data POST Username: " . ($_POST['username'] ?? 'KOSONG') . "<br>";

// Hentikan proses di sini agar tidak reload
die("Selesai mengecek. Jika Anda melihat ini, berarti PHP berjalan. Jika halaman langsung putih/reload, ada error di konfigurasi Vercel.");
