<?php
// =====================================================
// DASHBOARD PETUGAS LAPANGAN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_petugas();

$petugas_id = get_user_id();

// 1. Statistik Tugas Petugas
$query_stats = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN p.status = 'Menunggu' THEN 1 END) as menunggu,
                    COUNT(CASE WHEN p.status = 'Dalam Proses' THEN 1 END) as proses,
                    COUNT(CASE WHEN p.status = 'Selesai' THEN 1 END) as selesai
                FROM penanganan p
                WHERE p.petugas_id = ?";
$stmt_stats = mysqli_prepare($koneksi, $query_stats);
mysqli_stmt_bind_param($stmt_stats, "i", $petugas_id);
mysqli_stmt_execute($stmt_stats);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_stats));
mysqli_stmt_close($stmt_stats);

// 2. Tugas Aktif Petugas
$query_tugas_aktif = "SELECT p.id as penanganan_id, p.status as status_penanganan, 
                             l.id as laporan_id, l.kode_laporan, l.judul, l.prioritas, 
                             l.kecamatan, l.alamat, k.nama_kategori, k.ikon
                      FROM penanganan p
                      JOIN laporan l ON p.laporan_id = l.id
                      LEFT JOIN kategori k ON l.kategori_id = k.id
                      WHERE p.petugas_id = ? AND p.status IN ('Menunggu', 'Dalam Proses')
                      ORDER BY l.prioritas DESC, p.tanggal_penanganan DESC";
$stmt_tugas = mysqli_prepare($koneksi, $query_tugas_aktif);
mysqli_stmt_bind_param($stmt_tugas, "i", $petugas_id);
mysqli_stmt_execute($stmt_tugas);
$result_tugas_aktif = mysqli_stmt_get_result($stmt_tugas);

// 3. Tugas Baru Tersedia (Laporan diteruskan ke OPD yang belum diambil)
$query_tersedia = "SELECT lo.id as laporan_opd_id, l.id as laporan_id, l.kode_laporan, 
                          l.judul, l.prioritas, l.kecamatan, l.created_at,
                          k.nama_kategori, k.ikon, o.nama_opd
                   FROM laporan_opd lo
                   JOIN laporan l ON lo.laporan_id = l.id
                   JOIN opd o ON lo.opd_id = o.id
                   LEFT JOIN kategori k ON l.kategori_id = k.id
                   WHERE lo.petugas_id IS NULL AND l.status IN ('Terverifikasi', 'Dalam Penanganan')
                   ORDER BY l.prioritas DESC, l.created_at ASC
                   LIMIT 5";
$result_tersedia = mysqli_query($koneksi, $query_tersedia);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <div class="admin-layout">

        <!-- SIDEBAR PETUGAS -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="admin-sidebar-header">
                <span class="brand-badge" style="background:#1E293B; color:#4ADE80;">
                    <i data-lucide="leaf" style="width:18px; height:18px;"></i>
                </span>
                <div>
                    <div class="brand-title">SIGALING PADANG</div>
                    <div class="brand-sub">Petugas Lapangan</div>
                </div>
            </div>

            <ul class="admin-sidebar-nav">
                <li><a href="index.php" class="active"><i data-lucide="layout-dashboard" class="lucide-sm"></i> Dashboard</a></li>
                <li><a href="tugas.php"><i data-lucide="clipboard-check" class="lucide-sm"></i> Daftar Tugas Saya</a></li>

                <div class="admin-sidebar-divider">Akun</div>
                <li><a href="../auth/logout.php" style="color:#F87171;"><i data-lucide="log-out" class="lucide-sm"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- MAIN -->
        <div class="admin-main">

            <div class="admin-topbar">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-ghost d-lg-none p-1" id="toggle-sidebar">
                        <i data-lucide="menu" style="width:20px; height:20px;"></i>
                    </button>
                    <h1 class="page-title">Dashboard Petugas</h1>
                </div>

                <div class="admin-user-chip">
                    <div class="user-avatar-circle" style="background:#DCFCE7; color:#166534;">
                        <?php echo strtoupper(substr(get_user_nama(), 0, 1)); ?>
                    </div>
                    <div class="d-none d-sm-block text-start" style="line-height:1.2;">
                        <div style="font-size:0.85rem; font-weight:700; color:var(--color-text-main);"><?php echo htmlspecialchars(get_user_nama()); ?></div>
                        <div style="font-size:0.72rem; color:var(--color-text-muted);">Petugas Lapangan</div>
                    </div>
                </div>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <!-- 4 STAT CARDS -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card-custom mb-0 text-center py-3">
                            <div style="font-size:1.5rem; font-weight:800; color:var(--color-text-main);"><?php echo (int)($stats['total'] ?? 0); ?></div>
                            <div style="font-size:0.78rem; color:var(--color-text-muted); font-weight:600;">Total Tugas</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-custom mb-0 text-center py-3">
                            <div style="font-size:1.5rem; font-weight:800; color:#D97706;"><?php echo (int)($stats['menunggu'] ?? 0); ?></div>
                            <div style="font-size:0.78rem; color:var(--color-text-muted); font-weight:600;">Menunggu</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-custom mb-0 text-center py-3">
                            <div style="font-size:1.5rem; font-weight:800; color:#0284C7;"><?php echo (int)($stats['proses'] ?? 0); ?></div>
                            <div style="font-size:0.78rem; color:var(--color-text-muted); font-weight:600;">Sedang Diproses</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-custom mb-0 text-center py-3">
                            <div style="font-size:1.5rem; font-weight:800; color:#16A34A;"><?php echo (int)($stats['selesai'] ?? 0); ?></div>
                            <div style="font-size:0.78rem; color:var(--color-text-muted); font-weight:600;">Tuntas Selesai</div>
                        </div>
                    </div>
                </div>

                <!-- DUA KOLOM TUGAS -->
                <div class="row g-3">

                    <!-- 1. Tugas Aktif Saya -->
                    <div class="col-lg-6">
                        <div class="card-custom h-100 mb-0">
                            <div class="card-header-clean">
                                <h6><i data-lucide="wrench" class="lucide-sm text-primary"></i> Tugas Sedang Berjalan</h6>
                                <a href="tugas.php" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:0.75rem;">Semua &rarr;</a>
                            </div>

                            <?php if (mysqli_num_rows($result_tugas_aktif) > 0): ?>
                                <div class="d-flex flex-column gap-2">
                                    <?php while ($t = mysqli_fetch_assoc($result_tugas_aktif)): ?>
                                        <div class="p-3 border rounded bg-white">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                <span class="font-monospace fw-bold text-primary" style="font-size:0.85rem;"><?php echo htmlspecialchars($t['kode_laporan']); ?></span>
                                                <span class="badge-priority <?php echo ($t['prioritas'] === 'Tinggi') ? 'badge-priority-tinggi' : 'badge-priority-sedang'; ?>">
                                                    <?php echo htmlspecialchars($t['prioritas']); ?>
                                                </span>
                                            </div>
                                            <h3 style="font-size:0.95rem; font-weight:700; margin-bottom:0.3rem;">
                                                <?php echo $t['ikon'] . ' ' . htmlspecialchars($t['judul']); ?>
                                            </h3>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="font-size:0.78rem;">
                                                <span class="text-muted"><i data-lucide="map-pin" class="lucide-sm me-1"></i><?php echo htmlspecialchars($t['kecamatan'] ?: 'Kota Padang'); ?></span>
                                                <a href="detail-tugas.php?id=<?php echo $t['penanganan_id']; ?>" class="btn btn-sm btn-primary py-1 px-3" style="font-size:0.78rem;">
                                                    Update Progres &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div style="width:40px; height:40px; border-radius:50%; background:#DCFCE7; color:#166534; display:inline-flex; align-items:center; justify-content:center; margin-bottom:0.5rem;">
                                        <i data-lucide="check-circle" style="width:20px; height:20px;"></i>
                                    </div>
                                    <p class="text-muted m-0" style="font-size:0.85rem;">Tidak ada tugas aktif yang sedang Anda kerjakan saat ini</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 2. Tugas Tersedia Untuk Diambil -->
                    <div class="col-lg-6">
                        <div class="card-custom h-100 mb-0">
                            <div class="card-header-clean">
                                <h6><i data-lucide="inbox" class="lucide-sm text-primary"></i> Tugas Tersedia Untuk Diambil</h6>
                            </div>

                            <?php if (mysqli_num_rows($result_tersedia) > 0): ?>
                                <div class="d-flex flex-column gap-2">
                                    <?php while ($row_tersedia = mysqli_fetch_assoc($result_tersedia)): ?>
                                        <div class="p-3 border rounded bg-white" id="tersedia-<?php echo $row_tersedia['laporan_opd_id']; ?>">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                <span class="font-monospace fw-bold text-primary" style="font-size:0.85rem;"><?php echo htmlspecialchars($row_tersedia['kode_laporan']); ?></span>
                                                <span class="badge-status badge-status-terverifikasi"><?php echo htmlspecialchars($row_tersedia['nama_opd']); ?></span>
                                            </div>
                                            <h3 style="font-size:0.95rem; font-weight:700; margin-bottom:0.3rem;">
                                                <?php echo $row_tersedia['ikon'] . ' ' . htmlspecialchars($row_tersedia['judul']); ?>
                                            </h3>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="font-size:0.78rem;">
                                                <span class="text-muted"><?php echo htmlspecialchars($row_tersedia['kecamatan'] ?: 'Kota Padang'); ?></span>
                                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-3" style="font-size:0.78rem;"
                                                    onclick="ambilTugas(<?php echo $row_tersedia['laporan_opd_id']; ?>, <?php echo $row_tersedia['laporan_id']; ?>)">
                                                    <i data-lucide="hand" class="lucide-sm"></i> Ambil Tugas
                                                </button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <p class="text-muted m-0" style="font-size:0.85rem;">Belum ada tugas baru yang tersedia untuk diambil</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        function ambilTugas(laporanOpdId, laporanId) {
            if (!confirm('Ambil dan tangani laporan ini sebagai tugas Anda?')) return;

            fetch('../proses/ambil-tugas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `laporan_opd_id=${laporanOpdId}&laporan_id=${laporanId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Gagal mengambil tugas', 'danger');
                    }
                })
                .catch(err => showToast('Terjadi kesalahan: ' + err.message, 'danger'));
        }
    </script>
</body>

</html>