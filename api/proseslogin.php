<?php
/* ============================================================
   api/proseslogin.php — Proses Login SUKATANI
   ============================================================ */
session_start();
while (ob_get_level()) ob_end_clean();

include_once __DIR__ . '/koneksi.php';

if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=Isi username dan password!");
    exit;
}

$username       = trim($_POST['username']);
$password_input = $_POST['password'];

// Prepared statement — tanpa mysqli_stmt_get_result (lebih kompatibel)
$stmt = mysqli_prepare($conn, "SELECT id, nama, username, password, role FROM user WHERE username = ? LIMIT 1");
if (!$stmt) {
    header("Location: login.php?error=Terjadi kesalahan sistem");
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);

// Bind hasil ke variabel (tidak butuh mysqlnd)
mysqli_stmt_bind_result($stmt, $uid, $nama, $uname, $hashed_pass, $role);
$found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$password_valid = false;
if ($found) {
    if (password_verify($password_input, $hashed_pass)) {
        // bcrypt (user baru)
        $password_valid = true;
    } elseif ($hashed_pass === md5($password_input)) {
        // md5 (user lama) — upgrade otomatis ke bcrypt
        $password_valid = true;
        $new_hash = password_hash($password_input, PASSWORD_BCRYPT);
        $upd = mysqli_prepare($conn, "UPDATE user SET password = ? WHERE id = ?");
        if ($upd) {
            mysqli_stmt_bind_param($upd, 'si', $new_hash, $uid);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);
        }
    }
}

if ($password_valid) {
    // Set Session
    $_SESSION['login'] = true;
    $_SESSION['id']    = $uid;
    $_SESSION['nama']  = $nama;
    $_SESSION['role']  = $role;

    // ✅ Set cookie cadangan untuk Vercel (session tidak persist di serverless)
    $expire = time() + 7200; // 2 jam
    setcookie("login_session", "true",  $expire, "/", "", false, true);
    setcookie("login_name",    $nama,   $expire, "/", "", false, true);
    setcookie("login_role",    $role,   $expire, "/", "", false, true);

    session_write_close();
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php?error=Username atau Password Salah!");
    exit;
}
?>