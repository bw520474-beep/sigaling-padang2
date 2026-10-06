<?php
// =====================================================
// DASHBOARD ADMIN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

// 1. Total laporan
$query_total = "SELECT COUNT(*) as total FROM laporan";
$total_laporan = (int)mysqli_fetch_assoc(mysqli_query($koneksi, $query_total))['total'];

// 2. Laporan per status
$query_status = "SELECT status, COUNT(*) as jumlah FROM laporan GROUP BY status";
$result_status = mysqli_query($koneksi, $query_status);
$data_status = [
    'Menunggu Verifikasi' => 0,
    'Terverifikasi' => 0,
    'Dalam Penanganan' => 0,
    'Selesai' => 0,
    'Ditolak' => 0
];
while ($row = mysqli_fetch_assoc($result_status)) {
    $data_status[$row['status']] = (int)$row['jumlah'];
}

// 3. Rata-rata waktu penyelesaian (SLA)
$query_sla = "SELECT AVG(TIMESTAMPDIFF(HOUR, l.created_at, p.tanggal_penanganan)) as avg_hours 
              FROM penanganan p 
              JOIN laporan l ON p.laporan_id = l.id 
              WHERE p.status = 'Selesai'";
$res_sla = mysqli_query($koneksi, $query_sla);
$data_sla = mysqli_fetch_assoc($res_sla);
$avg_hours = round((float)($data_sla['avg_hours'] ?? 0), 1);
$sla_display = ($avg_hours > 0) ? ($avg_hours > 24 ? round($avg_hours / 24, 1) . ' Hari' : $avg_hours . ' Jam') : '1.5 Hari';

// 4. Laporan berdasarkan kategori (untuk chart)
$query_kategori_chart = "SELECT k.nama_kategori, COUNT(l.id) as jumlah 
                          FROM kategori k
                          LEFT JOIN laporan l ON k.id = l.kategori_id
                          GROUP BY k.id, k.nama_kategori
                          ORDER BY jumlah DESC";
$result_kategori_chart = mysqli_query($koneksi, $query_kategori_chart);
$chart_kategori_label = [];
$chart_kategori_data = [];
while ($row = mysqli_fetch_assoc($result_kategori_chart)) {
    $chart_kategori_label[] = $row['nama_kategori'];
    $chart_kategori_data[] = (int)$row['jumlah'];
}

// 5. Perkembangan laporan 7 hari terakhir
$query_waktu_chart = "SELECT DATE(created_at) as tanggal, COUNT(*) as jumlah 
                       FROM laporan 
                       WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                       GROUP BY DATE(created_at)
                       ORDER BY tanggal ASC";
$result_waktu_chart = mysqli_query($koneksi, $query_waktu_chart);
$chart_waktu_label = [];
$chart_waktu_data = [];
while ($row = mysqli_fetch_assoc($result_waktu_chart)) {
    $chart_waktu_label[] = date('d M', strtotime($row['tanggal']));
    $chart_waktu_data[] = (int)$row['jumlah'];
}

// 6. Laporan belum diverifikasi
$query_belum_verifikasi = "SELECT l.id, l.kode_laporan, l.judul, l.nama_pelapor, l.created_at, l.prioritas, k.nama_kategori, k.ikon
                            FROM laporan l
                            LEFT JOIN kategori k ON l.kategori_id = k.id
                            WHERE l.status = 'Menunggu Verifikasi'
                            ORDER BY l.created_at ASC LIMIT 5";
$result_belum_verifikasi = mysqli_query($koneksi, $query_belum_verifikasi);
$jumlah_belum_verifikasi = mysqli_num_rows($result_belum_verifikasi);

// 7. Laporan terbaru (5 terakhir)
$query_terbaru = "SELECT l.id, l.kode_laporan, l.judul, l.status, l.prioritas, l.created_at, l.kecamatan, k.nama_kategori, k.ikon
                   FROM laporan l
                   LEFT JOIN kategori k ON l.kategori_id = k.id
                   ORDER BY l.created_at DESC LIMIT 5";
$result_terbaru = mysqli_query($koneksi, $query_terbaru);

$badge_map = [
    'Menunggu Verifikasi' => 'badge-status-menunggu',
    'Terverifikasi' => 'badge-status-terverifikasi',
    'Dalam Penanganan' => 'badge-status-proses',
    'Selesai' => 'badge-status-selesai',
    'Ditolak' => 'badge-status-ditolak'
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrator - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <div class="admin-layout">
        
        <!-- ===== SIDEBAR ===== -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="admin-sidebar-header">
                <span class="brand-badge" style="background:#1E293B; color:#4ADE80;">
                    <i data-lucide="leaf" style="width:18px; height:18px;"></i>
                </span>
                <div>
                    <div class="brand-title">SIGALING PADANG</div>
                    <div class="brand-sub">Panel Administrator</div>
                </div>
            </div>

            <ul class="admin-sidebar-nav">
                <li><a href="index.php" class="active"><i data-lucide="layout-dashboard" class="lucide-sm"></i> Dashboard</a></li>
                <li><a href="laporan.php"><i data-lucide="file-text" class="lucide-sm"></i> Semua Laporan</a></li>
                <li><a href="statistik.php"><i data-lucide="bar-chart-2" class="lucide-sm"></i> Statistik Analitik</a></li>

                <div class="admin-sidebar-divider">Master Data</div>
                <li><a href="kategori.php"><i data-lucide="tags" class="lucide-sm"></i> Kategori Laporan</a></li>
                <li><a href="opd.php"><i data-lucide="building-2" class="lucide-sm"></i> Data OPD</a></li>
                <li><a href="petugas.php"><i data-lucide="shield" class="lucide-sm"></i> Akun Petugas</a></li>
                <li><a href="pengguna.php"><i data-lucide="users" class="lucide-sm"></i> Pengguna</a></li>

                <div class="admin-sidebar-divider">Akun</div>
                <li><a href="../auth/logout.php" style="color:#F87171;"><i data-lucide="log-out" class="lucide-sm"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="admin-main">
            
            <!-- Topbar -->
            <div class="admin-topbar">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-ghost d-lg-none p-1" id="toggle-sidebar">
                        <i data-lucide="menu" style="width:18px; height:18px;"></i>
                    </button>
                    <h1 class="page-title">Dashboard Administrator</h1>
                </div>

                <div class="admin-user-chip">
                    <div class="user-avatar-circle">
                        <?php echo strtoupper(substr(get_user_nama(), 0, 1)); ?>
                    </div>
                    <div class="d-none d-sm-block text-start" style="line-height:1.15;">
                        <div style="font-size:0.82rem; font-weight:700; color:var(--color-slate-900);"><?php echo htmlspecialchars(get_user_nama()); ?></div>
                        <div style="font-size:0.68rem; color:var(--color-slate-500);">Administrator</div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="admin-content">

                <!-- 4 KPI Metrics Compact Grid -->
                <div class="row g-2 mb-3">
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#F1F5F9; color:#334155;">
                                <i data-lucide="file-text" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo $total_laporan; ?></div>
                                <div class="stat-label-clean">Total Laporan Masuk</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#FEF2F2; color:#DC2626;">
                                <i data-lucide="clock" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo $data_status['Menunggu Verifikasi']; ?></div>
                                <div class="stat-label-clean">Menunggu Verifikasi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#FFFBEB; color:#D97706;">
                                <i data-lucide="activity" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo $data_status['Dalam Penanganan']; ?></div>
                                <div class="stat-label-clean">Dalam Penanganan</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#ECFDF5; color:#059669;">
                                <i data-lucide="check-circle-2" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo $data_status['Selesai']; ?></div>
                                <div class="stat-label-clean">Selesai (SLA: <?php echo $sla_display; ?>)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ANTREAN VERIFIKASI SEGERA (JIKA ADA) -->
                <?php if ($jumlah_belum_verifikasi > 0): ?>
                <div class="card-custom mb-3 border-warning" style="background:#FFFDF5;">
                    <div class="card-header-clean border-warning">
                        <h6 style="color:#92400E;">
                            <i data-lucide="alert-triangle" class="lucide-xs text-warning"></i>
                            Perlu Verifikasi Segera (<?php echo $jumlah_belum_verifikasi; ?> Pengaduan Baru)
                        </h6>
                        <a href="laporan.php?status=Menunggu+Verifikasi" class="btn btn-outline-primary btn-xs">Lihat Semua</a>
                    </div>

                    <div class="table-clean-wrapper mb-0 border-0 bg-transparent">
                        <table class="table-clean bg-white">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Judul & Kategori</th>
                                    <th>Pelapor</th>
                                    <th>Tanggal</th>
                                    <th style="width:90px; text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($verif = mysqli_fetch_assoc($result_belum_verifikasi)): ?>
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-bold text-primary" style="font-size:0.78rem;">
                                            <?php echo htmlspecialchars($verif['kode_laporan']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($verif['judul']); ?></div>
                                        <span class="text-muted" style="font-size:0.72rem;"><?php echo $verif['ikon'] . ' ' . htmlspecialchars($verif['nama_kategori']); ?></span>
                                    </td>
                                    <td>
                                        <span style="font-size:0.78rem;"><?php echo htmlspecialchars($verif['nama_pelapor']); ?></span>
                                    </td>
                                    <td class="text-muted" style="font-size:0.75rem;">
                                        <?php echo date('d/m/Y H:i', strtotime($verif['created_at'])); ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="detail-laporan.php?id=<?php echo $verif['id']; ?>" class="btn btn-primary btn-xs">
                                            Verifikasi &rarr;
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CHARTS ROW (COMPACT HEIGHT ~220px) -->
                <div class="row g-2 mb-3">
                    <div class="col-lg-7">
                        <div class="card-custom h-100">
                            <div class="card-header-clean">
                                <h6><i data-lucide="trending-up" class="lucide-xs text-primary"></i> Tren Laporan Masuk (7 Hari Terakhir)</h6>
                            </div>
                            <div style="height: 190px;">
                                <canvas id="chartWaktu"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-custom h-100">
                            <div class="card-header-clean">
                                <h6><i data-lucide="pie-chart" class="lucide-xs text-primary"></i> Distribusi Kategori</h6>
                            </div>
                            <div style="height: 190px;">
                                <canvas id="chartKategori"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAFTAR LAPORAN TERBARU -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="list" class="lucide-xs text-primary"></i> 5 Pengaduan Terkini</h6>
                        <a href="laporan.php" class="btn btn-xs btn-outline-primary">Lihat Semua Laporan</a>
                    </div>

                    <div class="table-clean-wrapper mb-0">
                        <table class="table-clean">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Judul Pengaduan</th>
                                    <th>Kecamatan</th>
                                    <th>Status</th>
                                    <th>Prioritas</th>
                                    <th style="width:80px; text-align:center;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result_terbaru) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_terbaru)): ?>
                                    <tr>
                                        <td>
                                            <span class="font-monospace fw-bold text-primary" style="font-size:0.78rem;">
                                                <?php echo htmlspecialchars($row['kode_laporan']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['judul']); ?></div>
                                            <span class="text-muted" style="font-size:0.72rem;"><?php echo $row['ikon'] . ' ' . htmlspecialchars($row['nama_kategori']); ?></span>
                                        </td>
                                        <td class="text-muted" style="font-size:0.78rem;">
                                            <?php echo htmlspecialchars($row['kecamatan'] ?: 'Padang'); ?>
                                        </td>
                                        <td>
                                            <span class="badge-status <?php echo $badge_map[$row['status']] ?? 'badge-status-menunggu'; ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-priority badge-priority-<?php echo strtolower($row['prioritas']); ?>">
                                                <?php echo htmlspecialchars($row['prioritas']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <a href="detail-laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-xs">
                                                Buka
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Belum ada data laporan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Backdrop for mobile sidebar -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }

        // Mobile Sidebar Toggle
        const toggleBtn = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                backdrop.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('show');
                backdrop.style.display = 'none';
            });
        }

        // Chart.js: Tren Laporan (Line Chart)
        const ctxWaktu = document.getElementById('chartWaktu').getContext('2d');
        new Chart(ctxWaktu, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_waktu_label); ?>,
                datasets: [{
                    label: 'Laporan Masuk',
                    data: <?php echo json_encode($chart_waktu_data); ?>,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });

        // Chart.js: Distribusi Kategori (Doughnut Chart)
        const ctxKat = document.getElementById('chartKategori').getContext('2d');
        new Chart(ctxKat, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chart_kategori_label); ?>,
                datasets: [{
                    data: <?php echo json_encode($chart_kategori_data); ?>,
                    backgroundColor: ['#059669', '#0EA5E9', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#64748B', '#10B981']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 10, font: { size: 9 } } }
                }
            }
        });
    </script>
</body>
</html>
