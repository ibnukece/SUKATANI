<?php
/* ============================================================
   api/prosesregister.php — Proses Pendaftaran Akun Baru
   ============================================================ */
include 'koneksi.php';  // ✅ was: '../config/koneksi.php'

// Validasi input kosong
if (empty($_POST['nama']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm'])) {
    header("Location: Register.php?error=Semua field wajib diisi");  // ✅ was: register.php
    exit;
}

$nama     = mysqli_real_escape_string($conn, $_POST['nama']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];
$confirm  = $_POST['confirm'];

// Cek password cocok
if ($password !== $confirm) {
    header("Location: Register.php?error=Password dan konfirmasi password tidak sama");  // ✅ was: register.php
    exit;
}

// Cek panjang password minimal 6 karakter
if (strlen($password) < 6) {
    header("Location: Register.php?error=Password minimal 6 karakter");  // ✅ was: register.php
    exit;
}

// Cek username sudah dipakai
$cekUser = mysqli_query($conn, "SELECT id FROM user WHERE username='$username' LIMIT 1");
if (mysqli_num_rows($cekUser) > 0) {
    header("Location: Register.php?error=Username sudah digunakan, coba yang lain");  // ✅ was: register.php
    exit;
}

// Enkripsi password
$passwordHash = md5($password);

// Simpan ke database
$insert = mysqli_query($conn, "INSERT INTO user (nama, username, password, role)
                                VALUES ('$nama', '$username', '$passwordHash', 'user')");

if ($insert) {
    header("Location: login.php?success=Akun berhasil dibuat! Silakan login.");  // ✅ sudah benar
    exit;
} else {
    header("Location: Register.php?error=Gagal mendaftar, coba lagi");  // ✅ was: register.php
    exit;
}
?>