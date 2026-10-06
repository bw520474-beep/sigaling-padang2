<?php
// =====================================================
// DAFTAR TUGAS PETUGAS - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_petugas();

$petugas_id = get_user_id();
$filter_status = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$where_clauses = ["p.petugas_id = ?"];
$params = [$petugas_id];
$types = 'i';

if ($filter_status !== 'all' && in_array($filter_status, ['Menunggu', 'Dalam Proses', 'Selesai'])) {
    $where_clauses[] = "p.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(l.kode_laporan LIKE ? OR l.judul LIKE ? OR l.alamat LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$where_sql = implode(' AND ', $where_clauses);

$query = "SELECT p.id as penanganan_id, p.status as status_penanganan, p.keterangan, p.foto, p.tanggal_penanganan,
                 l.id as laporan_id, l.kode_laporan, l.judul, l.prioritas, l.alamat, l.kecamatan, l.created_at,
                 k.nama_kategori, k.ikon
          FROM penanganan p
          JOIN laporan l ON p.laporan_id = l.id
          LEFT JOIN kategori k ON l.kategori_id = k.id
          WHERE $where_sql
          ORDER BY p.tanggal_penanganan DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas Saya - Petugas SIGALING</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            overflow-x: auto;
        }

        .filter-tab-btn {
            background: none;
            border: 1px solid var(--color-border);
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text-muted);
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .filter-tab-btn:hover {
            color: var(--color-text-main);
            background-color: var(--color-surface-subtle);
        }

        .filter-tab-btn.active {
            background-color: var(--color-primary);
            color: #FFFFFF;
            border-color: var(--color-primary);
        }
    </style>
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
                    <div class="brand-sub">Petugas Lapangan</div>
                </div>
            </div>

            <ul class="admin-sidebar-nav">
                <li><a href="index.php"><i data-lucide="layout-dashboard" class="lucide-sm"></i> Dashboard</a></li>
                <li><a href="tugas.php" class="active"><i data-lucide="clipboard-check" class="lucide-sm"></i> Daftar Tugas Saya</a></li>

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
                    <h1 class="page-title">Daftar Tugas Saya</h1>
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

                <!-- TABS & SEARCH -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div class="filter-tabs m-0 border-0 p-0">
                        <a href="tugas.php?status=all" class="filter-tab-btn <?php echo ($filter_status === 'all') ? 'active' : ''; ?>">Semua Tugas</a>
                        <a href="tugas.php?status=Menunggu" class="filter-tab-btn <?php echo ($filter_status === 'Menunggu') ? 'active' : ''; ?>">Menunggu</a>
                        <a href="tugas.php?status=Dalam+Proses" class="filter-tab-btn <?php echo ($filter_status === 'Dalam Proses') ? 'active' : ''; ?>">Dalam Proses</a>
                        <a href="tugas.php?status=Selesai" class="filter-tab-btn <?php echo ($filter_status === 'Selesai') ? 'active' : ''; ?>">Selesai</a>
                    </div>

                    <form method="GET" action="" style="max-width:280px; width:100%;">
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Cari judul / kode..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary"><i data-lucide="search" class="lucide-sm"></i></button>
                        </div>
                    </form>
                </div>

                <!-- TABLE -->
                <div class="table-clean-wrapper">
                    <table class="table-clean table-responsive-cards">
                        <thead>
                            <tr>
                                <th>Kode Laporan</th>
                                <th>Judul Permasalahan</th>
                                <th>Wilayah</th>
                                <th>Prioritas</th>
                                <th>Status Pengerjaan</th>
                                <th>Tanggal Penugasan</th>
                                <th style="width:130px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $badge_status = [
                                'Menunggu' => 'badge-status-menunggu',
                                'Dalam Proses' => 'badge-status-proses',
                                'Selesai' => 'badge-status-selesai'
                            ];
                            ?>
                            <tr>
                                <td data-label="Kode">
                                    <span class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($row['kode_laporan']); ?></span>
                                </td>
                                <td data-label="Judul">
                                    <strong><?php echo $row['ikon'] . ' ' . htmlspecialchars(mb_substr($row['judul'], 0, 32)); ?></strong>
                                </td>
                                <td data-label="Wilayah">
                                    <span class="text-muted" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['kecamatan'] ?: 'Kota Padang'); ?></span>
                                </td>
                                <td data-label="Prioritas">
                                    <span class="badge-priority <?php echo ($row['prioritas'] === 'Tinggi') ? 'badge-priority-tinggi' : 'badge-priority-sedang'; ?>">
                                        <?php echo htmlspecialchars($row['prioritas']); ?>
                                    </span>
                                </td>
                                <td data-label="Status">
                                    <span class="badge-status <?php echo $badge_status[$row['status_penanganan']] ?? 'badge-status-proses'; ?>">
                                        <?php echo htmlspecialchars($row['status_penanganan']); ?>
                                    </span>
                                </td>
                                <td data-label="Tanggal">
                                    <span class="text-muted" style="font-size:0.8rem;"><?php echo date('d M Y', strtotime($row['tanggal_penanganan'])); ?></span>
                                </td>
                                <td data-label="Aksi" style="text-align:center;">
                                    <a href="detail-tugas.php?id=<?php echo $row['penanganan_id']; ?>" class="btn btn-sm btn-primary py-1 px-3" style="font-size:0.78rem;">
                                        <i data-lucide="edit" class="lucide-sm"></i> Tangani
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">Tidak ada tugas penanganan ditemukan</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
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
    </script>
</body>
</html>
