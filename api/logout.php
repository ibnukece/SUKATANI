<?php
/* ============================================================
   api/logout.php — Hapus Session secara Total
   ============================================================ */
// Pastikan tidak ada spasi atau baris kosong sebelum tag <?php di atas

session_start();

// 1. Bersihkan semua variabel session
$_SESSION = array();

// 2. Hancurkan cookie session di browser (Sangat Penting untuk Vercel/Cloud)
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan session di server
session_unset();
session_destroy();

// 4. Gunakan cara redirect yang lebih kuat
// Jika header PHP gagal karena 'headers already sent', kita gunakan JavaScript sebagai cadangan
if (!headers_sent()) {
    header("Location: ../index.php");
    exit;
} else {
    echo '<script type="text/javascript">window.location.href="../index.php";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=../index.php" /></noscript>';
    exit;
}
?>