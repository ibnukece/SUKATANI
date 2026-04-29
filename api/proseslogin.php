<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=Username dan password wajib diisi");
    exit;
}

// PASTIKAN 'username' dan 'password' sesuai dengan <input name="..."> di form HTML
$username = mysqli_real_escape_string($conn, $_POST['username']); 
$password = md5($_POST['password']); 

$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);

// Tambahkan pengecekan jika query gagal
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$cek = mysqli_num_rows($result);

if ($cek > 0) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php?error=Username atau password salah");
    exit;
}
?>
