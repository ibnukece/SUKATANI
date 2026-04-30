<?php
/* ============================================================
   api/proseslogin.php — Proses Login SUKATANI
   ============================================================ */
session_start();
while (ob_get_level()) ob_end_clean();

include_once __DIR__ . '/koneksi.php';

if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "<script>alert('Isi username dan password!'); window.location.href='login.php';</script>";
    exit;
}

$username      = trim($_POST['username']);
$password_input = $_POST['password'];

// Pakai prepared statement — aman dari SQL Injection
$stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE username = ? LIMIT 1");
if (!$stmt) {
    echo "<script>alert('Terjadi kesalahan sistem!'); window.location.href='login.php';</script>";
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

// Cek password: support bcrypt (baru) DAN md5 (lama)
$password_valid = false;
if ($user) {
    if (password_verify($password_input, $user['password'])) {
        // Password bcrypt (user baru)
        $password_valid = true;
    } elseif ($user['password'] === md5($password_input)) {
        // Password md5 (user lama) — upgrade otomatis ke bcrypt
        $password_valid = true;
        $new_hash = password_hash($password_input, PASSWORD_BCRYPT);
        $upd = mysqli_prepare($conn, "UPDATE user SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($upd, 'si', $new_hash, $user['id']);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);
    }
}

if ($password_valid && $user) {
    // Set Session
    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['nama'];
    $_SESSION['role']  = $user['role'];

    // ✅ Simpan role ke cookie sebagai cadangan (penting untuk Vercel)
    $expire = time() + 3600;
    setcookie("login_session", "true",        $expire, "/");
    setcookie("login_name",    $user['nama'], $expire, "/");
    setcookie("login_role",    $user['role'], $expire, "/"); // ← ini yang kurang sebelumnya

    session_write_close();

    echo "<html><body>
          <script>
            window.location.replace('dashboard.php');
          </script>
          </body></html>";
    exit;
} else {
    echo "<script>alert('Username atau Password Salah!'); window.location.href='login.php';</script>";
    exit;
}
?>