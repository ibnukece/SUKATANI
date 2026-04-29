<?php
session_start();

// Paksa error muncul di layar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debug Mode: Proses Login</h2>";

// Gunakan __DIR__ untuk memastikan PHP mencari di folder yang tepat
if (!file_exists(__DIR__ . '/koneksi.php')) {
    die("❌ Error: File koneksi.php tidak ditemukan di path: " . __DIR__ . '/koneksi.php');
}

include __DIR__ . '/koneksi.php';

// 1. Cek Data POST
echo "<b>1. Data POST yang diterima:</b><pre>";
print_r($_POST);
echo "</pre>";

if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "❌ Masalah: Data username atau password kosong di sistem PHP.<br>";
    echo "Pastikan di login.php, tag input memiliki name='username' dan name='password'.";
    exit;
}

// 2. Cek Koneksi Database
if (!isset($conn)) {
    die("❌ Error: Variabel \$conn tidak terbaca. Pastikan di koneksi.php namanya \$conn, bukan \$koneksi.");
}
echo "✅ Koneksi database terdeteksi.<br>";

// 3. Tes Query
$user_input = mysqli_real_escape_string($conn, $_POST['username']);
$pass_input = md5($_POST['password']);

$query  = "SELECT * FROM user WHERE username='$user_input' AND password='$pass_input' LIMIT 1";
echo "<b>2. Query yang dijalankan:</b> <code>$query</code><br>";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("❌ Query Error: " . mysqli_error($conn));
}

$cek = mysqli_num_rows($result);
echo "<b>3. Hasil:</b> Ditemukan $cek user.<br><br>";

if ($cek > 0) {
    echo "✅ Login BERHASIL. Jika tidak ada debug ini, Anda seharusnya sudah di dashboard.";
} else {
    echo "❌ Login GAGAL: Username atau Password tidak cocok dengan data di TiDB Cloud.";
}

echo "<br><br><a href='dashboard.php'>Kembali ke Login</a>";
exit;
