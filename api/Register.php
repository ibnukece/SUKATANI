<?php
/* ============================================================
   api/Register.php — Halaman Register SUKATANI
   ============================================================ */

// Cek login via cookie (session tidak bekerja di Vercel serverless)
if (isset($_COOKIE['user_logged_in']) && $_COOKIE['user_logged_in'] === 'true') {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar – SUKATANI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-wrapper">

    <!-- Panel Kiri -->
    <div class="auth-panel-left">
        <a href="../index.php" class="brand">🌾 SUKATANI</a>
        <div class="panel-content">
            <h2>Bergabung<br>Sekarang!</h2>
            <p>Daftar gratis dan mulai kelola peminjaman alat pertanian Anda secara digital.</p>
        </div>
        <div class="panel-deco">🌱</div>
    </div>

    <!-- Panel Kanan (Form) -->
    <div class="auth-panel-right">
        <div class="auth-box">
            <h3>Buat Akun Baru</h3>
            <p class="auth-sub">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <form method="POST" action="prosesregister.php">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Buat username unik" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Buat password" required>
                </div>
                <div class="form-group">
                    <label for="confirm">Konfirmasi Password</label>
                    <input type="password" id="confirm" name="confirm" placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn-auth">Daftar Sekarang →</button>
            </form>

            <a href="../index.php" class="back-home">← Kembali ke Beranda</a>
        </div>
    </div>

</div>

</body>
</html>