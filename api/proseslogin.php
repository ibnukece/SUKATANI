<?php
/* ============================================================
   api/proseslogin.php — Proses Autentikasi Login (Vercel Fix)
   ============================================================ */
session_start();

// Aktifkan error reporting hanya untuk menangkap masalah koneksi
ini_set('display_errors', 0); // Matikan display untuk menghindari output sebelum header
error_reporting(E_ALL);

// 1. Pastikan file koneksi tersedia
$path_koneksi = __DIR__ . '/koneksi.php';
if (!file_exists($path_koneksi)) {
    header("Location: login.php?error=Sistem koneksi hilang");
    exit;
}

include_once $path_koneksi;

// 2. Cek apakah variabel koneksi ada
if (!isset($conn)) {
    header("Location: login.php?error=Gagal menghubungkan database");
    exit;
}

// 3. Validasi input POST
// Pastikan index 'username' dan 'password' sesuai dengan atribut 'name' di login.php
$user_input = isset($_POST['username']) ? $_POST['username'] : '';
$pass_input = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($user_input) || empty($pass_input)) {
    header("Location: login.php?error=Username dan password wajib diisi");
    exit;
}

// 4. Keamanan Input & Hash Password (MD5 sesuai database Anda)
$username = mysqli_real_escape_string($conn, $user_input);
$password = md5($pass_input); 

// 5. Query ke Database TiDB Cloud
$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    header("Location: login.php?error=Database query error");
    exit;
}

$cek = mysqli_num_rows($result);

if ($cek > 0) {
    $user = mysqli_fetch_assoc($result);

    // 6. Set Session Data
    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    setcookie("user_login", "true", time() + 3600, "/");
    setcookie("user_nama", $user['nama'], time() + 3600, "/");

    session_write_close();
    echo "<script>
        alert('Login Berhasil!');
        window.location.href = 'dashboard.php';
    </script>";
    exit;
}
} else {
    // Jika user tidak ditemukan, arahkan kembali ke login dengan pesan error
    header("Location: login.php?error=Username atau password salah");
    exit;
}
