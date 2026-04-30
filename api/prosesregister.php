<?php
/* ============================================================
   api/prosesregister.php — Proses Pendaftaran Akun Baru
   ============================================================ */
include_once __DIR__ . '/koneksi.php';

if (empty($_POST['nama']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm'])) {
    header("Location: Register.php?error=Semua field wajib diisi");
    exit;
}

$nama     = trim($_POST['nama']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm  = $_POST['confirm'];

if ($password !== $confirm) {
    header("Location: Register.php?error=Password dan konfirmasi tidak sama");
    exit;
}
if (strlen($password) < 6) {
    header("Location: Register.php?error=Password minimal 6 karakter");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM user WHERE username = ? LIMIT 1");
if (!$stmt) {
    header("Location: Register.php?error=Terjadi kesalahan sistem");
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$exists = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if ($exists) {
    header("Location: Register.php?error=Username sudah digunakan");
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$role = 'user';

$insert = mysqli_prepare($conn, "INSERT INTO user (nama, username, password, role) VALUES (?, ?, ?, ?)");
if (!$insert) {
    header("Location: Register.php?error=Terjadi kesalahan sistem");
    exit;
}
mysqli_stmt_bind_param($insert, 'ssss', $nama, $username, $passwordHash, $role);
$ok = mysqli_stmt_execute($insert);
mysqli_stmt_close($insert);

if ($ok) {
    header("Location: login.php?success=Akun berhasil dibuat! Silakan login.");
} else {
    header("Location: Register.php?error=Gagal mendaftar, coba lagi");
}
exit;