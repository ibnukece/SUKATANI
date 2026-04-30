<?php
/* ============================================================
   api/login.php — Halaman Login SUKATANI
   ============================================================ */
if (isset($_COOKIE['login_session']) && $_COOKIE['login_session'] === 'true') {
    header("Location: dashboard.php");
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
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-panel-left">
        <a href="../index.php" class="brand">🌾 SUKATANI</a>
        <div class="panel-content">
            <h2>Selamat Datang<br>Kembali!</h2>
            <p>Masuk untuk mengelola peminjaman alat pertanian Anda dengan mudah dan cepat.</p>
        </div>
        <div class="panel-deco">🚜</div>
    </div>
    <div class="auth-panel-right">
        <div class="auth-box">
            <h3>Masuk ke Akun</h3>
            <p class="auth-sub">Belum punya akun? <a href="Register.php">Daftar di sini</a></p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <form method="POST" action="proseslogin.php">
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
            <a href="../index.php" class="back-home">← Kembali ke Beranda</a>
        </div>
    </div>
</div>
</body>
</html>