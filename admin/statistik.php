<?php
// =====================================================
// STATISTIK & ANALITIK - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

// Filter rentang tanggal
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// 1. KPI Total & Selesai dalam rentang
$query_kpi = "SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'Selesai' THEN 1 END) as selesai,
                COUNT(CASE WHEN status = 'Dalam Penanganan' THEN 1 END) as proses,
                COUNT(CASE WHEN status = 'Terverifikasi' THEN 1 END) as terverifikasi,
                COUNT(CASE WHEN status = 'Menunggu Verifikasi' THEN 1 END) as menunggu,
                COUNT(CASE WHEN status = 'Ditolak' THEN 1 END) as ditolak
              FROM laporan 
              WHERE DATE(created_at) BETWEEN ? AND ?";
$stmt_kpi = mysqli_prepare($koneksi, $query_kpi);
mysqli_stmt_bind_param($stmt_kpi, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_kpi);
$data_kpi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_kpi));
mysqli_stmt_close($stmt_kpi);

$total = (int)($data_kpi['total'] ?? 0);
$selesai = (int)($data_kpi['selesai'] ?? 0);
$persentase_selesai = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

// 2. Data Tren Harian
$query_tren = "SELECT DATE(created_at) as tgl, COUNT(*) as jumlah 
               FROM laporan 
               WHERE DATE(created_at) BETWEEN ? AND ?
               GROUP BY DATE(created_at)
               ORDER BY tgl ASC";
$stmt_tren = mysqli_prepare($koneksi, $query_tren);
mysqli_stmt_bind_param($stmt_tren, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_tren);
$result_tren = mysqli_stmt_get_result($stmt_tren);
$tren_labels = [];
$tren_data = [];
while ($row = mysqli_fetch_assoc($result_tren)) {
    $tren_labels[] = date('d M', strtotime($row['tgl']));
    $tren_data[] = (int)$row['jumlah'];
}
mysqli_stmt_close($stmt_tren);

// 3. Performa OPD
$query_opd = "SELECT o.nama_opd, COUNT(lo.id) as total_ditugaskan,
                     COUNT(CASE WHEN l.status = 'Selesai' THEN 1 END) as tuntas
              FROM opd o
              LEFT JOIN laporan_opd lo ON o.id = lo.opd_id
              LEFT JOIN laporan l ON lo.laporan_id = l.id
              GROUP BY o.id
              ORDER BY total_ditugaskan DESC";
$result_opd = mysqli_query($koneksi, $query_opd);
$opd_labels = [];
$opd_ditugaskan = [];
$opd_tuntas = [];
$opd_rows = [];
while ($row = mysqli_fetch_assoc($result_opd)) {
    $opd_labels[] = $row['nama_opd'];
    $opd_ditugaskan[] = (int)$row['total_ditugaskan'];
    $opd_tuntas[] = (int)$row['tuntas'];
    $opd_rows[] = $row;
}

// 4. Kategori Masalah
$query_kat = "SELECT k.nama_kategori, COUNT(l.id) as jumlah 
              FROM kategori k
              LEFT JOIN laporan l ON k.id = l.kategori_id AND DATE(l.created_at) BETWEEN ? AND ?
              GROUP BY k.id
              ORDER BY jumlah DESC";
$stmt_kat = mysqli_prepare($koneksi, $query_kat);
mysqli_stmt_bind_param($stmt_kat, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_kat);
$result_kat = mysqli_stmt_get_result($stmt_kat);
$kat_labels = [];
$kat_data = [];
while ($row = mysqli_fetch_assoc($result_kat)) {
    $kat_labels[] = $row['nama_kategori'];
    $kat_data[] = (int)$row['jumlah'];
}
mysqli_stmt_close($stmt_kat);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik & Analitik Pengaduan - Admin SIGALING</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <div class="admin-layout">
        
        <!-- SIDEBAR -->
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
                <li><a href="index.php"><i data-lucide="layout-dashboard" class="lucide-sm"></i> Dashboard</a></li>
                <li><a href="laporan.php"><i data-lucide="file-text" class="lucide-sm"></i> Semua Laporan</a></li>
                <li><a href="statistik.php" class="active"><i data-lucide="bar-chart-2" class="lucide-sm"></i> Statistik Analitik</a></li>

                <div class="admin-sidebar-divider">Master Data</div>
                <li><a href="kategori.php"><i data-lucide="tags" class="lucide-sm"></i> Kategori Laporan</a></li>
                <li><a href="opd.php"><i data-lucide="building-2" class="lucide-sm"></i> Data OPD</a></li>
                <li><a href="petugas.php"><i data-lucide="shield" class="lucide-sm"></i> Akun Petugas</a></li>
                <li><a href="pengguna.php"><i data-lucide="users" class="lucide-sm"></i> Pengguna</a></li>

                <div class="admin-sidebar-divider">Akun</div>
                <li><a href="../auth/logout.php" style="color:#F87171;"><i data-lucide="log-out" class="lucide-sm"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- MAIN -->
        <div class="admin-main">
            
            <div class="admin-topbar">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-ghost d-lg-none p-1" id="toggle-sidebar">
                        <i data-lucide="menu" style="width:18px; height:18px;"></i>
                    </button>
                    <h1 class="page-title">Statistik & Analitik Kinerja</h1>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-secondary btn-xs" onclick="window.print()">
                        <i data-lucide="printer" class="lucide-xs"></i> Cetak Rekap Laporan
                    </button>
                </div>
            </div>

            <div class="admin-content">

                <!-- FILTER RENTANG TANGGAL -->
                <div class="card-custom mb-3 no-print">
                    <form action="statistik.php" method="GET" class="row g-2 align-items-end">
                        <div class="col-6 col-md-4">
                            <label class="form-label-clean" style="font-size:0.75rem;">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label-clean" style="font-size:0.75rem;">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="col-12 col-md-4 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i data-lucide="filter" class="lucide-xs"></i> Terapkan Periode
                            </button>
                        </div>
                    </form>
                </div>

                <!-- KOP SURAT FORMAL UNTUK CETAK (PRINT ONLY) -->
                <div class="d-none d-print-block text-center mb-4 pb-2 border-bottom">
                    <h4 class="mb-0 fw-bold">PEMERINTAH KOTA PADANG</h4>
                    <h5 class="mb-1 fw-bold">DINAS LINGKUNGAN HIDUP</h5>
                    <p class="mb-0" style="font-size:9pt;">LAPORAN REKAPITULASI PENGADUAN LINGKUNGAN HIDUP (SIGALING PADANG)</p>
                    <p class="mb-0 text-muted" style="font-size:8pt;">Periode: <?php echo date('d M Y', strtotime($start_date)); ?> s/d <?php echo date('d M Y', strtotime($end_date)); ?></p>
                </div>

                <!-- 4 KPI Metrics Compact Grid -->
                <div class="row g-2 mb-3">
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#F1F5F9; color:#334155;">
                                <i data-lucide="file-text" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo $total; ?></div>
                                <div class="stat-label-clean">Total Periode Ini</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#ECFDF5; color:#059669;">
                                <i data-lucide="check-circle-2" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo $selesai; ?></div>
                                <div class="stat-label-clean">Tuntas Selesai (<?php echo $persentase_selesai; ?>%)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#FFFBEB; color:#D97706;">
                                <i data-lucide="activity" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo (int)$data_kpi['proses']; ?></div>
                                <div class="stat-label-clean">Dalam Penanganan</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card-clean">
                            <div class="stat-icon-clean" style="background:#FEF2F2; color:#DC2626;">
                                <i data-lucide="clock" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="stat-number-clean"><?php echo (int)$data_kpi['menunggu']; ?></div>
                                <div class="stat-label-clean">Menunggu Verifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHARTS ROW (COMPACT HEIGHT ~220px) -->
                <div class="row g-2 mb-3 no-print">
                    <div class="col-lg-7">
                        <div class="card-custom h-100">
                            <div class="card-header-clean">
                                <h6><i data-lucide="trending-up" class="lucide-xs text-primary"></i> Tren Frekuensi Harian</h6>
                            </div>
                            <div style="height: 190px;">
                                <canvas id="chartTren"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card-custom h-100">
                            <div class="card-header-clean">
                                <h6><i data-lucide="pie-chart" class="lucide-xs text-primary"></i> Laporan Berdasarkan Kategori</h6>
                            </div>
                            <div style="height: 190px;">
                                <canvas id="chartKat"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PERFORMA PENYELESAIAN OPD -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="building-2" class="lucide-xs text-primary"></i> Rekapitulasi Kinerja Organisasi Perangkat Daerah (OPD)</h6>
                    </div>

                    <div class="table-clean-wrapper mb-0">
                        <table class="table-clean">
                            <thead>
                                <tr>
                                    <th>Nama Instansi / OPD</th>
                                    <th>Total Disposisi</th>
                                    <th>Tuntas Selesai</th>
                                    <th>Tingkat Penyelesaian (%)</th>
                                    <th>Status Kinerja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($opd_rows)): ?>
                                    <?php foreach ($opd_rows as $opd): ?>
                                    <?php
                                    $tot = (int)$opd['total_ditugaskan'];
                                    $tunt = (int)$opd['tuntas'];
                                    $pct = $tot > 0 ? round(($tunt / $tot) * 100) : 100;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong class="text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($opd['nama_opd']); ?></strong>
                                        </td>
                                        <td class="font-monospace" style="font-size:0.82rem;"><?php echo $tot; ?> tugas</td>
                                        <td class="font-monospace text-success fw-bold" style="font-size:0.82rem;"><?php echo $tunt; ?> selesai</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2" style="width:140px;">
                                                <div class="progress flex-grow-1" style="height:6px; background:#E2E8F0;">
                                                    <div class="progress-bar bg-success" style="width: <?php echo $pct; ?>%;"></div>
                                                </div>
                                                <span style="font-size:0.75rem; font-weight:700;"><?php echo $pct; ?>%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($pct >= 80): ?>
                                                <span class="badge-status badge-status-selesai">Sangat Baik</span>
                                            <?php elseif ($pct >= 50): ?>
                                                <span class="badge-status badge-status-proses">Cukup</span>
                                            <?php else: ?>
                                                <span class="badge-status badge-status-menunggu">Perlu Ditingkatkan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada data disposisi OPD.</td>
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

        // Chart Tren
        const ctxTren = document.getElementById('chartTren').getContext('2d');
        new Chart(ctxTren, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($tren_labels); ?>,
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: <?php echo json_encode($tren_data); ?>,
                    backgroundColor: '#059669',
                    borderRadius: 4
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

        // Chart Kategori
        const ctxKat = document.getElementById('chartKat').getContext('2d');
        new Chart(ctxKat, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($kat_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($kat_data); ?>,
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
