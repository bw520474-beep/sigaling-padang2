<?php
// =====================================================
// SEMUA LAPORAN - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

// Filter & Pencarian
$filter_status = $_GET['status'] ?? '';
$filter_kategori = $_GET['kategori'] ?? '';
$search = trim($_GET['search'] ?? '');
$is_export = isset($_GET['export']) && $_GET['export'] === 'csv';

$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_status)) {
    $where_clauses[] = "l.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($filter_kategori)) {
    $where_clauses[] = "l.kategori_id = ?";
    $params[] = $filter_kategori;
    $types .= 'i';
}

if (!empty($search)) {
    $where_clauses[] = "(l.kode_laporan LIKE ? OR l.judul LIKE ? OR l.nama_pelapor LIKE ? OR l.kecamatan LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// JIKA USER KLIK EKSPOR CSV
if ($is_export) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Rekap_Laporan_SIGALING_' . date('Ymd_His') . '.csv');
    
    $output = fopen('php://output', 'w');
    // UTF-8 BOM untuk Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header Kolom CSV
    fputcsv($output, ['No', 'Kode Laporan', 'Tanggal Lapor', 'Kategori', 'Judul Pengaduan', 'Deskripsi', 'Kecamatan', 'Alamat', 'Nama Pelapor', 'Kontak', 'Status', 'Prioritas']);
    
    $query_export = "SELECT l.*, k.nama_kategori 
                     FROM laporan l 
                     LEFT JOIN kategori k ON l.kategori_id = k.id 
                     $where_sql 
                     ORDER BY l.created_at DESC";
    
    if (!empty($params)) {
        $stmt_exp = mysqli_prepare($koneksi, $query_export);
        mysqli_stmt_bind_param($stmt_exp, $types, ...$params);
        mysqli_stmt_execute($stmt_exp);
        $res_exp = mysqli_stmt_get_result($stmt_exp);
    } else {
        $res_exp = mysqli_query($koneksi, $query_export);
    }
    
    $no = 1;
    while ($r = mysqli_fetch_assoc($res_exp)) {
        fputcsv($output, [
            $no++,
            $r['kode_laporan'],
            date('Y-m-d H:i', strtotime($r['created_at'])),
            $r['nama_kategori'],
            $r['judul'],
            $r['deskripsi'],
            $r['kecamatan'],
            $r['alamat'],
            $r['nama_pelapor'],
            $r['kontak_pelapor'],
            $r['status'],
            $r['prioritas']
        ]);
    }
    fclose($output);
    exit;
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

$query_count = "SELECT COUNT(*) as total FROM laporan l $where_sql";
if (!empty($params)) {
    $stmt_count = mysqli_prepare($koneksi, $query_count);
    mysqli_stmt_bind_param($stmt_count, $types, ...$params);
    mysqli_stmt_execute($stmt_count);
    $total_data = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['total'];
    mysqli_stmt_close($stmt_count);
} else {
    $total_data = (int)mysqli_fetch_assoc(mysqli_query($koneksi, $query_count))['total'];
}
$total_pages = ceil($total_data / $per_page);

// Query Data Laporan
$query = "SELECT l.id, l.kode_laporan, l.judul, l.nama_pelapor, l.status, l.prioritas, 
                 l.kecamatan, l.created_at, k.nama_kategori, k.ikon
          FROM laporan l
          LEFT JOIN kategori k ON l.kategori_id = k.id
          $where_sql
          ORDER BY l.created_at DESC
          LIMIT $per_page OFFSET $offset";

if (!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, $query);
}

// Dropdown Kategori
$result_kategori_filter = mysqli_query($koneksi, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

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
    <title>Kelola Pengaduan - Admin SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <li><a href="laporan.php" class="active"><i data-lucide="file-text" class="lucide-sm"></i> Semua Laporan</a></li>
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

        <!-- MAIN -->
        <div class="admin-main">
            
            <div class="admin-topbar">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-ghost d-lg-none p-1" id="toggle-sidebar">
                        <i data-lucide="menu" style="width:18px; height:18px;"></i>
                    </button>
                    <h1 class="page-title">Manajemen Data Laporan</h1>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php
                    $export_params = $_GET;
                    $export_params['export'] = 'csv';
                    $export_url = 'laporan.php?' . http_build_query($export_params);
                    ?>
                    <a href="<?php echo $export_url; ?>" class="btn btn-secondary btn-xs">
                        <i data-lucide="download" class="lucide-xs"></i> Ekspor CSV
                    </a>
                </div>
            </div>

            <div class="admin-content">

                <!-- FILTER BAR (COMPACT) -->
                <div class="card-custom mb-3">
                    <form action="laporan.php" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label-clean" style="font-size:0.75rem;">Pencarian Kata Kunci</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Kode tiket, judul, pelapor, kecamatan..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label-clean" style="font-size:0.75rem;">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="Menunggu Verifikasi" <?php echo ($filter_status === 'Menunggu Verifikasi') ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                                <option value="Terverifikasi" <?php echo ($filter_status === 'Terverifikasi') ? 'selected' : ''; ?>>Terverifikasi</option>
                                <option value="Dalam Penanganan" <?php echo ($filter_status === 'Dalam Penanganan') ? 'selected' : ''; ?>>Dalam Penanganan</option>
                                <option value="Selesai" <?php echo ($filter_status === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="Ditolak" <?php echo ($filter_status === 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label-clean" style="font-size:0.75rem;">Kategori</label>
                            <select name="kategori" class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                <?php while ($k = mysqli_fetch_assoc($result_kategori_filter)): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo ($filter_kategori == $k['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($k['nama_kategori']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i data-lucide="filter" class="lucide-xs"></i> Filter
                            </button>
                            <?php if (!empty($filter_status) || !empty($filter_kategori) || !empty($search)): ?>
                                <a href="laporan.php" class="btn btn-secondary btn-sm" title="Reset Filter">
                                    <i data-lucide="rotate-ccw" class="lucide-xs"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- TABEL DATA LAPORAN -->
                <div class="table-clean-wrapper mb-2">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th style="width:110px;">Kode Tiket</th>
                                <th>Pengaduan & Kategori</th>
                                <th>Pelapor</th>
                                <th>Kecamatan</th>
                                <th>Status</th>
                                <th>Prioritas</th>
                                <th>Tanggal</th>
                                <th style="width:70px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-bold text-primary" style="font-size:0.78rem;">
                                            <?php echo htmlspecialchars($row['kode_laporan']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['judul']); ?></strong>
                                        <div class="text-muted" style="font-size:0.72rem;"><?php echo $row['ikon'] . ' ' . htmlspecialchars($row['nama_kategori']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size:0.78rem;"><?php echo htmlspecialchars($row['nama_pelapor']); ?></span>
                                    </td>
                                    <td class="text-muted" style="font-size:0.78rem;">
                                        <?php echo htmlspecialchars($row['kecamatan'] ?: '-'); ?>
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
                                    <td class="text-muted" style="font-size:0.75rem;">
                                        <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="detail-laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-xs py-1 px-2" title="Detail Laporan">
                                            <i data-lucide="eye" class="lucide-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada laporan yang sesuai dengan kriteria filter.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION & TOTAL COUNT -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-1" style="font-size:0.78rem; color:var(--color-slate-500);">
                    <div>Menampilkan total <strong><?php echo $total_data; ?></strong> data pengaduan</div>

                    <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php
                                $page_params = $_GET;
                                $page_params['page'] = $i;
                                $page_url = 'laporan.php?' . http_build_query($page_params);
                                ?>
                                <li class="page-item <?php echo ($page === $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $page_url; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
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
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
        window.addEventListener('load', () => {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });

        const toggleBtn = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                if (backdrop) backdrop.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            });
        }

        if (backdrop && sidebar) {
            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('show');
                backdrop.style.display = 'none';
            });
        }
    </script>
</body>
</html>
