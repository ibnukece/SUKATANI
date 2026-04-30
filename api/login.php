<?php
/* ============================================================
   api/login.php — Halaman Login SUKATANI
   ============================================================ */
session_start();

// Kalau sudah login, redirect ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");  // ✅ was: '../dashboard/dashboard.php'
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SUKATANI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth.css">  <!-- ✅ tetap ../ karena assets di luar api/ -->
</head>
<body>

<div class="auth-wrapper">

    <!-- Panel Kiri -->
    <div class="auth-panel-left">
        <a href="../index.php" class="brand">🌾 SUKATANI</a>  <!-- ✅ tetap ../ karena index.php di root -->
        <div class="panel-content">
            <h2>Selamat Datang<br>Kembali!</h2>
            <p>Masuk untuk mengelola peminjaman alat pertanian Anda dengan mudah dan cepat.</p>
        </div>
        <div class="panel-deco">🚜</div>
    </div>

    <!-- Panel Kanan (Form) -->
    <div class="auth-panel-right">
        <div class="auth-box">
            <h3>Masuk ke Akun</h3>
            <p class="auth-sub">Belum punya akun? <a href="Register.php">Daftar di sini</a></p>
            <!-- ✅ was: 'register.php' — sesuaikan huruf kapital dengan nama file Register.php -->

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <form method="POST" action="proseslogin.php">  <!-- ✅ sudah benar, sama folder -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-auth">Masuk →</button>
            </form>

            <a href="../index.php" class="back-home">← Kembali ke Beranda</a>  <!-- ✅ tetap ../ -->
        </div>
    </div>

</div>

</body>
</html>