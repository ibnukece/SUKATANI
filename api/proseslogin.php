<?php
/* ============================================================
   api/proseslogin.php — Final Deployment Fix
   ============================================================ */
session_start();

// Matikan semua output buffering
while (ob_get_level()) ob_end_clean();

include_once __DIR__ . '/koneksi.php';

if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "<script>alert('Isi username dan password!'); window.location.href='login.php';</script>";
    exit;
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']); 

$query  = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Set Session
    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    // Set Cookie sebagai cadangan permanen untuk Vercel
    setcookie("login_session", "true", time() + 3600, "/"); 
    setcookie("login_name", $user['nama'], time() + 3600, "/");

    session_write_close(); // Paksa simpan sesi

    echo "<html><body>
          <script>
            alert('Login Berhasil!');
            window.location.replace('dashboard.php');
          </script>
          </body></html>";
    exit;
} else {
    echo "<script>alert('Username atau Password Salah!'); window.location.href='login.php';</script>";
    exit;
}
