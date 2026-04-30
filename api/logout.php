<?php
/* ============================================================
   api/logout.php — Logout SUKATANI
   ============================================================ */
session_start();
session_unset();
session_destroy();

// ✅ Hapus semua cookie login
$past = time() - 3600;
setcookie("login_session", "", $past, "/");
setcookie("login_name",    "", $past, "/");
setcookie("login_role",    "", $past, "/");

header("Location: ../index.php");
exit;
?>