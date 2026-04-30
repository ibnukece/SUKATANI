<?php
/* ============================================================
   api/koneksi.php — Koneksi TiDB Cloud (Vercel Fix)
   ============================================================ */

$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '2W5BREbefDNV4CZ.root';
$pass = 'IfoGLubKu21sanUc';
$db   = 'sukatani';

// ✅ WAJIB: matikan exception mysqli agar fallback bisa jalan
mysqli_report(MYSQLI_REPORT_OFF);

// === COBA 1: Koneksi dengan SSL ===
$conn = mysqli_init();
if ($conn) {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

    $ssl_flag = MYSQLI_CLIENT_SSL;
    if (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')) {
        $ssl_flag |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
    }

    $connected = @mysqli_real_connect(
        $conn, $host, $user, $pass, $db, $port, NULL, $ssl_flag
    );

    // === COBA 2: Fallback tanpa SSL ===
    if (!$connected) {
        $conn = @mysqli_connect($host, $user, $pass, $db, $port);
    }
}

if (!$conn || mysqli_connect_errno()) {
    $err = mysqli_connect_error() ?: 'Tidak dapat terhubung ke database';
    die("<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Error</title></head>
         <body style='font-family:sans-serif;padding:2rem;'>
         <h3>⚠️ Koneksi Database Gagal</h3><p>{$err}</p>
         <a href='login.php'>← Kembali ke Login</a></body></html>");
}

mysqli_set_charset($conn, 'utf8mb4');
?>