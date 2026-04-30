<?php
/* ============================================================
   api/logout.php — Hapus Session & Redirect ke Landing Page
   ============================================================ */
session_start();

// Bersihkan semua data session
$_SESSION = array();

// Hancurkan session secara total
session_unset();
session_destroy();

// Balik ke halaman utama (index.php) yang ada di luar folder api
// Kita gunakan ../ agar dia keluar dari folder api/ baru mencari index.php
header("Location: ../index.php");
exit;
?>