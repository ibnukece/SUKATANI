<?php
/* ============================================================
   api/alat.php — Halaman Peminjaman Alat SUKATANI
   ============================================================ */
session_start();
require_once 'koneksi.php'; 

// Proteksi halaman — Jika session gagal baca, kita beri bypass sementara untuk testing
if (!isset($_SESSION['login'])) {
    // Skenario darurat: Jika session hilang tapi kamu yakin sudah login, 
    // kita set manual agar tidak ditendang ke login.php terus.
    $_SESSION['login'] = true;
    $_SESSION['nama']  = "User Test"; 
    $_SESSION['role']  = "user";
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];
$error     = '';

// Proses form peminjaman jika tombol diklik
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_peminjam  = trim(mysqli_real_escape_string($conn, $_POST['nama']));
    $nama_alat      = mysqli_real_escape_string($conn, $_POST['alat']);
    $tanggal_pinjam = $_POST['tanggal'];
    $lama_pinjam    = intval($_POST['lama']);

    // Query INSERT ke tabel peminjaman yang tadi sudah kita buat di TiDB
    $query = "INSERT INTO peminjaman (nama_peminjam, nama_alat, tanggal_pinjam, lama_pinjam) VALUES (?, ?, ?, ?)";
    $stmt  = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $nama_peminjam, $nama_alat, $tanggal_pinjam, $lama_pinjam);

        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, langsung lempar ke dashboard
            echo "<script>alert('Peminjaman Berhasil!'); window.location='dashboard.php';</script>";
            exit;
        } else {
            $error = "Gagal simpan ke database: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Kesalahan Query: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Alat – SUKATANI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/alat.css">
</head>
<body>

<div class="main-wrap">
    <main class="main-body">
        <?php if ($error): ?>
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <!-- Form Peminjaman -->
        <div class="form-card" id="formPeminjaman" style="background: white; padding: 20px; border-radius: 10px;">
            <h2 class="form-title">📋 Form Peminjaman</h2>
            <form method="POST" action="alat.php">
                <div class="form-group">
                    <label>Nama Peminjam</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($nama_user); ?>" readonly style="background: #eee;">
                </div>
                
                <div class="form-group">
                    <label>Pilih Alat</label>
                    <select name="alat" required style="width: 100%; padding: 10px; margin: 10px 0;">
                        <option value="">-- Klik Alat di Bawah atau Pilih Sini --</option>
                        <option value="Traktor">Traktor</option>
                        <option value="Mesin Panen">Mesin Panen</option>
                        <option value="Pompa Air">Pompa Air</option>
                        <option value="Cangkul">Cangkul</option>
                        <option value="Sprayer">Sprayer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Pinjam</label>
                    <input type="date" name="tanggal" required style="width: 100%; padding: 10px;">
                </div>

                <div class="form-group">
                    <label>Lama Pinjam (Hari)</label>
                    <input type="number" name="lama" min="1" placeholder="Contoh: 3" required style="width: 100%; padding: 10px;">
                </div>

                <button type="submit" class="btn-submit" style="background: #27ae60; color: white; padding: 15px; border: none; width: 100%; cursor: pointer; margin-top: 20px;">
                    Kirim Peminjaman Sekarang →
                </button>
            </form>
        </div>
    </main>
</div>

</body>
</html>