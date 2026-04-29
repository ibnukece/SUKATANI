<?php

session_start();

// Aktifkan error reporting agar jika ada masalah koneksi langsung terlihat
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php'; 

// 1. Pastikan data tidak kosong
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=Username dan password wajib diisi");
    exit;
}

// 2. Ambil data dari form (Sesuaikan dengan atribut 'name' di login.php)
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']); // Sesuaikan jika database menggunakan hash lain

// 3. Query ke Database
$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    // Jika query gagal, tampilkan errornya
    die("Query ke TiDB Cloud Gagal: " . mysqli_error($conn));
}

$cek = mysqli_num_rows($result);

if ($cek > 0) {
    $user = mysqli_fetch_assoc($result);

    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    // 4. Arahkan ke Dashboard
    header("Location: dashboard.php");
    exit;
} else {
    // Jika user tidak ditemukan
    header("Location: login.php?error=Username atau password salah");
    exit;
}
?>
