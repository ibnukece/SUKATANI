<?php
/* ============================================================
   api/kelola.php — Halaman Kelola Data SUKATANI
   ============================================================ */
session_start();
include 'koneksi.php';          // ✅ was: '../config/koneksi.php'

// Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");  // ✅ sudah benar (sama folder)
    exit;
}

$success = "";
if (isset($_GET['msg'])) $success = $_GET['msg'];

// --- LOGIKA CRUD USER ---
if (isset($_POST['simpan_user'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = $_POST['role'];
    $id       = $_POST['id_user'];
    
    if (!empty($id)) {
        mysqli_query($conn, "UPDATE user SET nama='$nama', username='$username', role='$role' WHERE id=$id");
        header("Location: kelola.php?msg=User Diperbarui");
    } else {
        $pass = md5('123456');
        mysqli_query($conn, "INSERT INTO user (nama, username, password, role) VALUES ('$nama', '$username', '$pass', '$role')");
        header("Location: kelola.php?msg=User Ditambah");
    }
    exit;
}

// --- LOGIKA AKSI (HAPUS & STATUS) ---
if (isset($_GET['hapus_user'])) {
    $id = $_GET['hapus_user'];
    mysqli_query($conn, "DELETE FROM user WHERE id=$id AND role != 'admin'");
    header("Location: kelola.php?msg=User Dihapus");
    exit;    // ✅ FIX: exit hilang di versi lama — bisa menyebabkan kode lanjut jalan
}
if (isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    mysqli_query($conn, "UPDATE peminjaman SET status='Kembali' WHERE id=$id");
    header("Location: kelola.php?msg=Alat Kembali");
    exit;    // ✅ FIX: sama, exit perlu ditambah
}

// --- AMBIL DATA STATISTIK ---
$q_u = mysqli_query($conn, "SELECT id FROM user WHERE role='user'");
$count_user = ($q_u) ? mysqli_num_rows($q_u) : 0;

$q_p = mysqli_query($conn, "SELECT id FROM peminjaman WHERE status='Dipinjam' OR status='' OR status IS NULL");
$count_aktif = ($q_p) ? mysqli_num_rows($q_p) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Sistem – SUKATANI</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css"> <!-- ✅ tetap ../ karena assets di luar folder api/ -->
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-box { background: #fff; padding: 20px; border-radius: 15px; border-left: 5px solid var(--green-main); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .stat-box p { font-size: 1.8rem; font-weight: 800; color: var(--green-dark); }
        .admin-form { background: #fff; padding: 20px; border-radius: 15px; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-end; }
        .admin-form input, .admin-form select { padding: 10px; border: 1px solid #ddd; border-radius: 8px; flex: 1; }
        .badge-status { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .bg-wait { background: #fff3cd; color: #856404; }
        .bg-done { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">🌾 SUKATANI ADMIN</div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item">📊 Dashboard</a>
        <a href="kelola.php" class="nav-item active">⚙️ Kelola Data</a>
    </nav>
    <a href="logout.php" class="btn-logout">Keluar →</a>  <!-- ✅ was: '../auth/logout.php' -->
</aside>

<div class="main-wrap">
    <header class="topbar"><h1 class="page-title">Sistem Kontrol Admin</h1></header>

    <main class="main-body">
        <?php if ($success): ?>
            <div style="background:#d4edda; padding:15px; border-radius:10px; margin-bottom:20px;">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>  <!-- ✅ FIX: tambah htmlspecialchars untuk keamanan -->

        <div class="stats-grid">
            <div class="stat-box"><h3>Petani Terdaftar</h3><p><?= $count_user ?></p></div>
            <div class="stat-box"><h3>Peminjaman Aktif</h3><p><?= $count_aktif ?></p></div>
        </div>

        <div class="table-card">
            <div class="table-header"><h2 class="table-title">👥 Manajemen Pengguna</h2></div>
            <form class="admin-form" method="POST" style="margin:20px;">
                <input type="hidden" name="id_user" id="id_user">
                <input type="text" name="nama" id="nama" placeholder="Nama Lengkap" required>
                <input type="text" name="username" id="username" placeholder="Username" required>
                <select name="role"><option value="user">User</option><option value="admin">Admin</option></select>
                <button type="submit" name="simpan_user" class="btn-tambah" style="border:none; cursor:pointer;">Simpan User</button>
            </form>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Nama</th><th>Role</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $us = mysqli_query($conn, "SELECT * FROM user ORDER BY id DESC");
                        while ($u = mysqli_fetch_assoc($us)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($u['nama']) ?></strong></td>
                            <td><?= strtoupper($u['role']) ?></td>
                            <td>
                                <button onclick="isiForm('<?= $u['id'] ?>','<?= htmlspecialchars($u['nama'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>','<?= $u['role'] ?>')"
                                    style="color:blue; background:none; border:none; cursor:pointer; font-weight:bold;">Edit</button> |
                                <a href="?hapus_user=<?= $u['id'] ?>" style="color:red;" onclick="return confirm('Hapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-card" style="margin-top:30px;">
            <div class="table-header"><h2 class="table-title">🚜 Monitoring Peminjaman Alat</h2></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Peminjam</th><th>Alat</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $ps = mysqli_query($conn, "SELECT * FROM peminjaman ORDER BY id DESC");
                        while ($p = mysqli_fetch_assoc($ps)):
                        $status = ($p['status'] == 'Kembali') ? 'Kembali' : 'Dipinjam'; ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nama_peminjam']) ?></td>
                            <td><strong><?= htmlspecialchars($p['nama_alat']) ?></strong></td>
                            <td><span class="badge-status <?= ($status == 'Kembali') ? 'bg-done' : 'bg-wait' ?>"><?= strtoupper($status) ?></span></td>
                            <td>
                                <?php if ($status == 'Dipinjam'): ?>
                                    <a href="?selesai=<?= $p['id'] ?>" style="color:green; font-weight:bold; text-decoration:none;">✔️ Selesaikan</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
function isiForm(id, nama, user, role) {
    document.getElementById('id_user').value = id;
    document.getElementById('nama').value = nama;
    document.getElementById('username').value = user;
    document.querySelector('select[name="role"]').value = role;
}
</script>
</body>
</html>