<?php
/* ============================================================
   api/logout.php — Hapus Session & Redirect ke Landing Page
   ============================================================ */
session_start();
session_unset();
session_destroy();

header("Location: ../index.php");  // ✅ tetap ../ karena index.php ada di root
exit;
?>