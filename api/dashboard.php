<?php
/* ============================================================
   api/dashboard.php — Halaman Dashboard SUKATANI
   ============================================================ */
session_start();
include 'koneksi.php';          // ✅ was: '../config/koneksi.php'

// Proteksi halaman — wajib login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");  // ✅ was: '../auth/login.php'
    exit;
}

$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];

// ── Panggil api.php — semua variabel BPS siap dipakai ──
include 'api.php';              // ✅ sudah benar

// ── Cek tabel peminjaman ──
$tabel_ada        = mysqli_query($conn, "SHOW TABLES LIKE 'peminjaman'");
$result           = false;
$total_peminjaman = 0;
$total_alat       = 0;
$last_tanggal     = '-';

if ($tabel_ada && mysqli_num_rows($tabel_ada) > 0) {

    $result = mysqli_query($conn, "SELECT * FROM peminjaman ORDER BY tanggal_pinjam DESC");
    $total_peminjaman = ($result) ? mysqli_num_rows($result) : 0;

    $result_alat = mysqli_query($conn, "SELECT COUNT(DISTINCT nama_alat) as total FROM peminjaman");
    if ($result_alat) {
        $row_alat   = mysqli_fetch_assoc($result_alat);
        $total_alat = $row_alat['total'] ?? 0;
    }

    $result_tgl = mysqli_query($conn, "SELECT tanggal_pinjam FROM peminjaman ORDER BY tanggal_pinjam DESC LIMIT 1");
    if ($result_tgl) {
        $row_tgl      = mysqli_fetch_assoc($result_tgl);
        $last_tanggal = $row_tgl ? $row_tgl['tanggal_pinjam'] : '-';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – SUKATANI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css"> <!-- ✅ assets tetap ../ karena beda folder -->
    <style>
        .bps-section  { margin-top: 2rem; }
        .bps-badge    {
            display: inline-block;
            background: #e8f5e9; color: #2e7d32;
            font-size: 0.72rem; font-weight: 600;
            padding: 3px 10px; border-radius: 20px;
            letter-spacing: 0.04em;
        }
        .bps-note  {
            font-size: 0.78rem; color: #999;
            margin-top: 0.5rem; padding: 0 1rem 0.75rem;
        }
        .bps-error {
            padding: 1.5rem 1rem; color: #b0803a;
            background: #fffbf0; border-radius: 8px;
            margin: 1rem; font-size: 0.88rem;
        }
        .bps-error code { word-break: break-all; }
        .table-header .right-meta {
            display: flex; flex-direction: column;
            align-items: flex-end; gap: 4px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">🌾 SUKATANI</div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">📊 Dashboard</a>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="kelola.php" class="nav-item">⚙️ Kelola Data</a>
        <?php else: ?>
            <a href="alat.php" class="nav-item">🚜 Pinjam Alat</a>
        <?php endif; ?>
    </nav>
    <a href="logout.php" class="btn-logout">Keluar →</a>  <!-- ✅ was: '../auth/logout.php' -->
</aside>

<!-- MAIN CONTENT -->
<div class="main-wrap">

    <!-- TOPBAR -->
    <header class="topbar">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">Ringkasan data peminjaman alat pertanian</p>
        </div>
        <div class="user-chip">
            <div class="user-avatar"><?= strtoupper(substr($nama_user, 0, 1)); ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($nama_user); ?></div>
                <div class="user-role"><?= ucfirst($role_user); ?></div>
            </div>
        </div>
    </header>

    <!-- BODY -->
    <main class="main-body">

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <!-- STAT CARDS -->
        <div class="stat-cards">
            <div class="stat-card green">
                <div class="sc-icon">🌾</div>
                <div class="sc-num"><?= $total_alat; ?></div>
                <div class="sc-label">Jenis Alat</div>
            </div>
            <div class="stat-card blue">
                <div class="sc-icon">📋</div>
                <div class="sc-num"><?= $total_peminjaman; ?></div>
                <div class="sc-label">Total Peminjaman</div>
            </div>
            <div class="stat-card gold">
                <div class="sc-icon">📅</div>
                <div class="sc-num" style="font-size:1.4rem;"><?= $last_tanggal; ?></div>
                <div class="sc-label">Tanggal Terakhir</div>
            </div>
        </div>

        <!-- ══ TABEL PEMINJAMAN LOKAL ══ -->
        <div class="table-card">
            <div class="table-header">
                <h2 class="table-title">📋 Data Peminjaman</h2>
                <a href="alat.php" class="btn-tambah">+ Pinjam Alat</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Alat</th>
                            <th>Tanggal Pinjam</th>
                            <th>Lama Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $total_peminjaman > 0):
                            mysqli_data_seek($result, 0);
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                <td><?= htmlspecialchars($row['nama_alat']); ?></td>
                                <td><?= $row['tanggal_pinjam']; ?></td>
                                <td><?= $row['lama_pinjam']; ?> hari</td>
                            </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" class="empty-state">🌱 Belum ada data peminjaman.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ TABEL DATA BPS — dari api.php ══ -->
        <div class="table-card bps-section">
            <div class="table-header">
                <h2 class="table-title">📊 <?= htmlspecialchars($bps_title); ?></h2>
                <div class="right-meta">
                    <span class="bps-badge">📡 Sumber: BPS Indonesia</span>
                    <?php if ($bps_satuan): ?>
                        <span style="font-size:0.75rem;color:#888;">Satuan: <?= htmlspecialchars($bps_satuan); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($bps_ok): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis / Uraian</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($bps_rows as $row): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['label']); ?></td>
                                <td><?= is_numeric($row['nilai'])
                                        ? number_format((float)$row['nilai'], 0, ',', '.')
                                        : htmlspecialchars((string)$row['nilai']); ?>
                                </td>
                                <td><?= htmlspecialchars($row['unit']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($bps_note): ?>
                    <p class="bps-note">📌 <?= nl2br(htmlspecialchars(strip_tags($bps_note))); ?></p>
                <?php endif; ?>

            <?php else: ?>
                <div class="bps-error">  <!-- ✅ FIX: div ini hilang di versi lama -->
                    ⚠️ <strong>Data BPS tidak dapat dimuat.</strong><br>
                    Pastikan server bisa mengakses internet dan API key valid.<br>
                    Cek juga apakah ekstensi <code>curl</code> aktif di PHP.
                </div>
            <?php endif; ?>
            <?php if (!empty($bps_debug_html)) echo $bps_debug_html; ?>
        </div>

    </main>
</div>

</body>
</html>