<?php
/* ============================================================
   api/prosesregister.php — Proses Pendaftaran Akun Baru
   ============================================================ */

// 1. Gunakan path absolut dengan __DIR__ agar Vercel tidak tersesat mencari file
require_once __DIR__ . '/koneksi.php';

/**
 * PERBAIKAN KRUSIAL:
 * Kita cek apakah $koneksi (dari koneksi.php) tersedia. 
 * Jika tidak, kita coba ambil dari $conn.
 */
$db_conn = null;

if (isset($koneksi) && $koneksi instanceof mysqli) {
    $db_conn = $koneksi;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $db_conn = $conn;
}

// Jika masih null, berarti koneksi.php gagal total dimuat
if (!$db_conn) {
    die("Error: Koneksi database tidak tersedia. Pastikan api/koneksi.php benar.");
}

// Validasi input kosong
if (
    empty($_POST['nama']) ||
    empty($_POST['username']) ||
    empty($_POST['password']) ||
    empty($_POST['confirm'])
) {
    header("Location: Register.php?error=Semua field wajib diisi");
    exit;
}

$nama     = trim($_POST['nama']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm  = $_POST['confirm'];

// Cek password cocok
if ($password !== $confirm) {
    header("Location: Register.php?error=Password dan konfirmasi password tidak sama");
    exit;
}

// Cek panjang password minimal 6 karakter
if (strlen($password) < 6) {
    header("Location: Register.php?error=Password minimal 6 karakter");
    exit;
}

// Cek username sudah dipakai — menggunakan variabel $db_conn yang sudah dipastikan ada
$stmt = mysqli_prepare($db_conn, "SELECT id FROM user WHERE username = ? LIMIT 1");
if (!$stmt) {
    header("Location: Register.php?error=Terjadi kesalahan sistem (prepare check)");
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    header("Location: Register.php?error=Username sudah digunakan, coba yang lain");
    exit;
}
mysqli_stmt_close($stmt);

// Hash password dengan bcrypt
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Simpan ke database
$role = 'user';
$insert = mysqli_prepare($db_conn, "INSERT INTO user (nama, username, password, role) VALUES (?, ?, ?, ?)");
if (!$insert) {
    header("Location: Register.php?error=Terjadi kesalahan sistem (prepare insert)");
    exit;
}
mysqli_stmt_bind_param($insert, 'ssss', $nama, $username, $passwordHash, $role);
$result = mysqli_stmt_execute($insert);
mysqli_stmt_close($insert);

if ($result) {
    header("Location: login.php?success=Akun berhasil dibuat! Silakan login.");
    exit;
} else {
    header("Location: Register.php?error=Gagal mendaftar, coba lagi");
    exit;
}
?>