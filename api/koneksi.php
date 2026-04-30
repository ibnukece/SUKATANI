<?php
/* ============================================================
   api/koneksi.php — Koneksi TiDB Cloud
   ============================================================ */

$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = '2W5BREbefDNV4CZ.root';
$pass = 'IfoGLubKu21sanUc';
$db   = 'sukatani';

$conn = mysqli_init();

if (!$conn) {
    die(json_encode(['error' => 'mysqli_init gagal']));
}

// SSL wajib untuk TiDB Cloud
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, 'DHE-RSA-AES256-SHA:AES128-SHA');

$connected = mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    $db,
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
);

if (!$connected) {
    die(json_encode(['error' => 'Koneksi TiDB gagal: ' . mysqli_connect_error()]));
}

mysqli_set_charset($conn, 'utf8mb4');
?>