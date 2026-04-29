<?php
session_start();
include 'koneksi.php';

// Validasi input kosong
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=Username dan password wajib diisi");
    exit;
}

// ✅ Gunakan $koneksi (bukan $conn)
if (!$koneksi) {
    header("Location: login.php?error=Koneksi database gagal");
    exit;
}

// ✅ Ganti $conn → $koneksi
$username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
$password = md5($_POST['password']);

$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($koneksi, $query); // ✅

if (!$result) {
    header("Location: login.php?error=Terjadi kesalahan sistem");
    exit;
}

$cek = mysqli_num_rows($result);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($result);

    $_SESSION['id']       = $data['id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role']     = $data['role'] ?? 'user';

    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php?error=Username atau password salah");
    exit;
}
?>