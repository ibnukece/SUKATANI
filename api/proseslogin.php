<?php
/* ============================================================
   api/proseslogin.php — Proses Autentikasi Login
   ============================================================ */
session_start();
include 'koneksi.php';  // ✅ was: '../config/koneksi.php'

// Validasi input kosong
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=Username dan password wajib diisi");
    exit;
}

$username = mysqli_real_escape_string($conn, $_POST['2W5BREbefDNV4CZ.root']);
$password = md5($_POST['password']);

$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);
$cek    = mysqli_num_rows($result);

if ($cek > 0) {
    $user = mysqli_fetch_assoc($result);

    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    header("Location: dashboard.php");  // ✅ was: logika $base yang rumit & tidak perlu
    exit;
} else {
    header("Location: login.php?error=Username atau password salah");
    exit;
}
// ✅ DIHAPUS: 2 baris dead code di bawah — tidak akan pernah dieksekusi
// header("Location: ../dashboard/dashboard.php");
// exit;
?>
