<?php
/* ============================================================
   api/alat.php — Halaman Peminjaman Alat SUKATANI
   ============================================================ */
session_start();
require_once 'koneksi.php'; 

/**
 * LOGIKA ANTI-TENDANG VERCEL
 * Jika session hilang/terhapus server, kita buatkan session baru 
 * supaya proses testing tidak terganggu halaman login.
 */
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    $_SESSION['login'] = true;
    $_SESSION['nama']  = "User Test"; 
    $_SESSION['role']  = "user";
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];
$error     = '';

// PROSES SIMPAN DATA KE TiDB
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pastikan koneksi aman
    $nama_peminjam  = trim(mysqli_real_escape_string($conn, $_POST['nama']));
    $nama_alat      = mysqli_real_escape_string($conn, $_POST['alat']);
    $tanggal_pinjam = $_POST['tanggal'];
    $lama_pinjam    = intval($_POST['lama']);

    // Query INSERT (Pastikan tabel sudah di-DROP & CREATE ulang dengan AUTO_INCREMENT)
    $query = "INSERT INTO peminjaman (nama_peminjam, nama_alat, tanggal_pinjam, lama_pinjam) VALUES (?, ?, ?, ?)";
    $stmt  = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $nama_peminjam, $nama_alat, $tanggal_pinjam, $lama_pinjam);

        if (mysqli_stmt_execute($stmt)) {
            // Berhasil simpan, langsung ke dashboard
            echo "<script>alert('Peminjaman Berhasil Disimpan!'); window.location='dashboard.php';</script>";
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/alat.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">🌾 SUKATANI</div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item">
            <span class="nav-icon">📊</span> Dashboard
        </a>
        <a href="alat.php" class="nav-item active">
            <span class="nav-icon">🚜</span> Pinjam Alat
        </a>
        <?php if ($role_user === 'admin'): ?>
        <a href="kelola.php" class="nav-item">
            <span class="nav-icon">⚙️</span> Kelola Data
        </a>
        <?php endif; ?>
    </nav>
    <a href="logout.php" class="btn-logout">Keluar →</a>
</aside>

<!-- MAIN CONTENT -->
<div class="main-wrap">

    <!-- TOPBAR -->
    <header class="topbar">
        <div>
            <h1 class="page-title">Pinjam Alat</h1>
            <p class="page-sub">Pilih alat dan isi form peminjaman di bawah</p>
        </div>
        <div class="user-chip">
            <div class="user-avatar"><?= strtoupper(substr($nama_user, 0, 1)); ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($nama_user); ?></div>
                <div class="user-role"><?= ucfirst($role_user); ?></div>
            </div>
        </div>
    </header>

    <main class="main-body">

        <?php if ($error): ?>
            <div class="alert alert-danger" style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- KARTU ALAT (Sama seperti di Localhost Gambar 2) -->
        <h2 class="section-heading">Daftar Alat Tersedia</h2>
        <div class="alat-grid">
            <div class="alat-card" onclick="pilihAlat('Traktor')">
                <div class="alat-emoji">🚜</div>
                <h3>Traktor</h3>
                <p>Alat untuk membajak sawah dengan cepat dan efisien.</p>
                <span class="btn-pilih">Pilih Alat</span>
            </div>

            <div class="alat-card" onclick="pilihAlat('Mesin Panen')">
                <div class="alat-emoji">🌾</div>
                <h3>Mesin Panen</h3>
                <p>Mempermudah proses panen padi secara otomatis.</p>
                <span class="btn-pilih">Pilih Alat</span>
            </div>

            <div class="alat-card" onclick="pilihAlat('Pompa Air')">
                <div class="alat-emoji">💧</div>
                <h3>Pompa Air</h3>
                <p>Digunakan untuk mengairi sawah dengan efisien.</p>
                <span class="btn-pilih">Pilih Alat</span>
            </div>

            <div class="alat-card" onclick="pilihAlat('Cangkul')">
                <div class="alat-emoji">⛏️</div>
                <h3>Cangkul</h3>
                <p>Alat manual untuk mengolah tanah ladang dan sawah.</p>
                <span class="btn-pilih">Pilih Alat</span>
            </div>

            <div class="alat-card" onclick="pilihAlat('Sprayer')">
                <div class="alat-emoji">🪣</div>
                <h3>Sprayer</h3>
                <p>Alat semprot untuk pestisida dan pupuk cair.</p>
                <span class="btn-pilih">Pilih Alat</span>
            </div>

            <div class="alat-card" onclick="pilihAlat('Bajak Sawah')">
                <div class="alat-emoji">🌱</div>
                <h3>Bajak Sawah</h3>
                <p>Bajak tradisional untuk mengolah lahan persawahan.</p>
                <span class="btn-pilih">Pilih Alat</span>
            </div>
        </div>

        <!-- FORM PEMINJAMAN (Sesuai Gambar 1 dengan Fungsi TiDB) -->
        <div class="form-card" id="formPeminjaman">
            <h2 class="form-title">📋 Form Peminjaman</h2>
            <p class="form-sub">Klik salah satu alat di atas atau pilih langsung di bawah.</p>

            <form method="POST" action="alat.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Peminjam</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama_user); ?>" readonly style="background: #f3f4f6; cursor: not-allowed;">
                    </div>
                    <div class="form-group">
                        <label for="selectAlat">Alat yang Dipinjam</label>
                        <select id="selectAlat" name="alat" required>
                            <option value="">-- Pilih Alat --</option>
                            <option value="Traktor">Traktor</option>
                            <option value="Mesin Panen">Mesin Panen</option>
                            <option value="Pompa Air">Pompa Air</option>
                            <option value="Cangkul">Cangkul</option>
                            <option value="Sprayer">Sprayer</option>
                            <option value="Bajak Sawah">Bajak Sawah</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Pinjam</label>
                        <input type="date" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="lama">Lama Pinjam (hari)</label>
                        <input type="number" id="lama" name="lama" min="1" placeholder="Contoh: 3" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Kirim Peminjaman Sekarang →</button>
            </form>
        </div>
    </main>
</div>

<script>
// Otomatis set tanggal hari ini
document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("tanggal").setAttribute("min", today);
    document.getElementById("tanggal").value = today;
});

// Fungsi agar kartu alat bisa diklik dan otomatis mengisi form
function pilihAlat(namaAlat) {
    document.getElementById("selectAlat").value = namaAlat;
    
    // Hapus highlight dari kartu lain
    document.querySelectorAll('.alat-card').forEach(c => c.style.border = "none");
    
    // Tambah highlight ke kartu yang dipilih (opsional)
    event.currentTarget.style.border = "2px solid #27ae60";
    
    // Scroll otomatis ke form
    document.getElementById("formPeminjaman").scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

</body>
</html>