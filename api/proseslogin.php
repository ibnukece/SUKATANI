<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan path ke koneksi.php benar
include_once __DIR__ . '/koneksi.php';

if (!isset($conn)) {
    die("❌ Error: Koneksi database belum terdefinisi.");
}

// Menggunakan path absolut agar aman di Vercel
include_once __DIR__ . '/koneksi.php';

// 1. Validasi input
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=Username dan password wajib diisi");
    exit;
}

// 2. Keamanan Input & Hash Password
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']); 

// 3. Query Database
$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    // Jika terjadi error pada query (opsional: aktifkan hanya saat debug)
    header("Location: login.php?error=Terjadi kesalahan pada sistem.");
    exit;
}

$cek = mysqli_num_rows($result);

if ($cek > 0) {
    $user = mysqli_fetch_assoc($result);

    // 4. Set Session
    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    // 5. Redirect ke Dashboard
    header("Location: dashboard.php");
    exit;
} else {
    // Jika user tidak ditemukan
    header("Location: login.php?error=Username atau password salah");
    exit;
}
