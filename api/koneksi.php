<?php
/* ============================================================
   api/koneksi.php — Koneksi TiDB Cloud (Vercel Fix)
   ============================================================ */

$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '2W5BREbefDNV4CZ.root';
$pass = 'IfoGLubKu21sanUc';
$db   = 'sukatani';

$conn = mysqli_init();

if (!$conn) {
    header("Location: login.php?error=Inisialisasi database gagal");
    exit;
}

// Set cipher TLS 1.2 yang kompatibel dengan TiDB Cloud di Vercel
mysqli_ssl_set(
    $conn,
    NULL,   // key
    NULL,   // cert
    NULL,   // ca
    NULL,   // capath
    'AES128-SHA256:AES256-SHA256:AES128-GCM-SHA256:AES256-GCM-SHA384'
);

// Gabungkan SSL + skip verifikasi sertifikat
$ssl_flag = MYSQLI_CLIENT_SSL;
if (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')) {
    $ssl_flag |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
}

$connected = mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    $db,
    $port,
    NULL,
    $ssl_flag
);

// Fallback: koneksi tanpa SSL jika handshake gagal
if (!$connected) {
    $conn = @mysqli_connect($host, $user, $pass, $db, $port);
    if (!$conn) {
        header("Location: login.php?error=Koneksi database gagal");
        exit;
    }
}

mysqli_set_charset($conn, 'utf8mb4');
?>