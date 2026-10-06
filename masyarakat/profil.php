<?php
// =====================================================
// HALAMAN PROFIL & RIWAYAT LAPORAN MASYARAKAT
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_login();

$user_id = get_user_id();

// Ambil Riwayat Laporan User
$query = "SELECT l.id, l.kode_laporan, l.judul, l.status, l.created_at, k.nama_kategori, k.ikon
          FROM laporan l
          LEFT JOIN kategori k ON l.kategori_id = k.id
          WHERE l.user_id = ?
          ORDER BY l.created_at DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-sigaling">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <span class="brand-badge">
                    <i data-lucide="leaf"></i>
                </span>
                <span>SIGALING PADANG</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i data-lucide="menu" style="width:20px; height:20px;"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="../index.php"><i data-lucide="home" class="lucide-sm"></i> Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="peta.php"><i data-lucide="map" class="lucide-sm"></i> Peta Permasalahan</a></li>
                    <li class="nav-item"><a class="nav-link" href="jadwal-sampah.php"><i data-lucide="truck" class="lucide-sm"></i> Jadwal Sampah</a></li>
                    <li class="nav-item"><a class="nav-link" href="edukasi.php"><i data-lucide="book-open" class="lucide-sm"></i> Edukasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="alur.php"><i data-lucide="git-merge" class="lucide-sm"></i> Alur Pelaporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="tracking.php"><i data-lucide="search" class="lucide-sm"></i> Tracking</a></li>
                    <li class="nav-item"><a class="nav-link active" href="profil.php"><i data-lucide="user" class="lucide-sm text-success"></i> <?php echo htmlspecialchars(get_user_nama()); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php" style="color:var(--color-danger) !important;"><i data-lucide="log-out" class="lucide-sm"></i> Keluar</a></li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="buat-laporan.php" class="btn btn-primary btn-sm w-100">
                            <i data-lucide="send" class="lucide-sm"></i> Lapor Sekarang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="py-4">
        <div class="container" style="max-width: 800px;">
            
            <!-- User Profile Card -->
            <div class="card-custom mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px; height:52px; border-radius:50%; background:var(--color-primary-subtle); color:var(--color-primary-hover); display:flex; align-items:center; justify-content:center; font-size:1.35rem; font-weight:800; flex-shrink:0;">
                        <?php echo strtoupper(substr(get_user_nama(), 0, 1)); ?>
                    </div>
                    <div>
                        <h2 style="font-size:1.25rem; margin-bottom:0.15rem; color:var(--color-slate-900);"><?php echo htmlspecialchars(get_user_nama()); ?></h2>
                        <div style="font-size:0.85rem; color:var(--color-slate-500);">
                            <i data-lucide="mail" class="lucide-sm me-1"></i> <?php echo htmlspecialchars(get_user_email()); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Laporan -->
            <div class="card-header-clean">
                <h6><i data-lucide="history" class="lucide-sm text-primary"></i> Riwayat Laporan Saya</h6>
                <a href="buat-laporan.php" class="btn btn-sm btn-primary">
                    <i data-lucide="plus" class="lucide-sm"></i> Laporan Baru
                </a>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                $badge_class = 'badge-status-menunggu';
                if ($row['status'] === 'Terverifikasi') $badge_class = 'badge-status-terverifikasi';
                elseif ($row['status'] === 'Dalam Penanganan') $badge_class = 'badge-status-proses';
                elseif ($row['status'] === 'Selesai') $badge_class = 'badge-status-selesai';
                elseif ($row['status'] === 'Ditolak') $badge_class = 'badge-status-ditolak';
                ?>
                <div class="card-custom mb-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="font-monospace fw-bold text-primary" style="font-size:0.85rem;">
                                    <?php echo htmlspecialchars($row['kode_laporan']); ?>
                                </span>
                                <span class="badge-status <?php echo $badge_class; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </div>
                            <h3 style="font-size:0.95rem; margin-bottom:0.2rem; font-weight:700; color:var(--color-slate-900);">
                                <?php echo $row['ikon'] . ' ' . htmlspecialchars($row['judul']); ?>
                            </h3>
                            <div style="font-size:0.78rem; color:var(--color-slate-500);">
                                <i data-lucide="calendar" class="lucide-sm me-1"></i>
                                <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?> WIB
                            </div>
                        </div>
                        <a href="tracking.php?kode=<?php echo urlencode($row['kode_laporan']); ?>" class="btn btn-sm btn-secondary">
                            <i data-lucide="search" class="lucide-sm"></i> Detail Tracking
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card-custom text-center py-5">
                    <div style="width:48px; height:48px; border-radius:50%; background:var(--color-slate-100); color:var(--color-slate-400); display:inline-flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                        <i data-lucide="inbox" style="width:24px; height:24px;"></i>
                    </div>
                    <h4 style="font-size:1rem; margin-bottom:0.35rem; color:var(--color-slate-900);">Belum Ada Riwayat Laporan</h4>
                    <p class="text-muted" style="font-size:0.85rem; margin-bottom:1rem;">Anda belum pernah mengirimkan laporan permasalahan lingkungan.</p>
                    <a href="buat-laporan.php" class="btn btn-primary btn-sm">
                        <i data-lucide="plus-circle" class="lucide-sm"></i> Buat Laporan Pertama
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="site-footer">
        <div class="container text-center">
            <p class="m-0">&copy; 2026 <strong>SIGALING PADANG</strong>. Sistem Informasi & Pengaduan Lingkungan Hidup.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
        window.addEventListener('load', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
