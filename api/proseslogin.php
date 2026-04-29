<?php
session_start();
include 'koneksi.php';

// CEK 1: Apa isi data yang dikirim dari form?
echo "Data yang diterima:<pre>";
print_r($_POST);
echo "</pre>";

if (empty($_POST['username']) || empty($_POST['password'])) {
    // Ganti header() dengan die() untuk melihat pesan errornya
    die("Error: Form mengirim data kosong. Periksa atribut 'name' pada input HTML Anda.");
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']);

$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);

// CEK 2: Apakah query berhasil atau error?
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$cek = mysqli_num_rows($result);
echo "Jumlah user ditemukan: " . $cek . "<br>";

if ($cek > 0) {
    echo "Login Berhasil! Data user ada.";
    // header("Location: dashboard.php"); // Matikan sementara
} else {
    die("Login Gagal: Username atau Password salah di database.");
}
exit;
