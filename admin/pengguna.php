<?php
// =====================================================
// MANAJEMEN PENGGUNA - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

$filter_role = $_GET['role'] ?? '';
$search = trim($_GET['search'] ?? '');

$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_role)) {
    $where_clauses[] = "u.role = ?";
    $params[] = $filter_role;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(u.nama LIKE ? OR u.email LIKE ? OR u.no_hp LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$query = "SELECT u.*, COUNT(l.id) as total_laporan
          FROM users u
          LEFT JOIN laporan l ON u.id = l.user_id
          $where_sql
          GROUP BY u.id
          ORDER BY u.created_at DESC";

if (!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, $query);
}

$current_user_id = get_user_id();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin SIGALING PADANG</title>

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
                <li><a href="laporan.php"><i data-lucide="file-text" class="lucide-sm"></i> Semua Laporan</a></li>
                <li><a href="statistik.php"><i data-lucide="bar-chart-2" class="lucide-sm"></i> Statistik Analitik</a></li>

                <div class="admin-sidebar-divider">Master Data</div>
                <li><a href="kategori.php"><i data-lucide="tags" class="lucide-sm"></i> Kategori Laporan</a></li>
                <li><a href="opd.php"><i data-lucide="building-2" class="lucide-sm"></i> Data OPD</a></li>
                <li><a href="petugas.php"><i data-lucide="shield" class="lucide-sm"></i> Akun Petugas</a></li>
                <li><a href="pengguna.php" class="active"><i data-lucide="users" class="lucide-sm"></i> Pengguna</a></li>

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
                    <h1 class="page-title">Manajemen Akun Pengguna</h1>
                </div>
                <button type="button" class="btn btn-xs btn-primary" onclick="bukaModalTambah()">
                    <i data-lucide="user-plus" class="lucide-xs"></i> Tambah Pengguna
                </button>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <!-- FILTER BAR -->
                <div class="card-custom mb-3">
                    <form method="GET" action="" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label-clean" style="font-size:0.75rem;">Pencarian</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama, email, atau no HP..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label-clean" style="font-size:0.75rem;">Role Pengguna</label>
                            <select name="role" class="form-select form-select-sm">
                                <option value="">Semua Role</option>
                                <option value="admin" <?php echo ($filter_role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="petugas" <?php echo ($filter_role === 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                <option value="masyarakat" <?php echo ($filter_role === 'masyarakat') ? 'selected' : ''; ?>>Masyarakat</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i data-lucide="filter" class="lucide-xs"></i> Filter
                            </button>
                            <?php if (!empty($filter_role) || !empty($search)): ?>
                                <a href="pengguna.php" class="btn btn-secondary btn-sm" title="Reset">
                                    <i data-lucide="rotate-ccw" class="lucide-xs"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- TABEL PENGGUNA -->
                <div class="table-clean-wrapper">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Role</th>
                                <th>Total Laporan</th>
                                <th>Terdaftar</th>
                                <th style="width:120px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr id="row-<?php echo $row['id']; ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar-circle" style="width:26px; height:26px; font-size:0.7rem;">
                                            <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                                        </div>
                                        <strong class="text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['nama']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace" style="font-size:0.78rem;"><?php echo htmlspecialchars($row['email']); ?></span>
                                </td>
                                <td>
                                    <span class="font-monospace" style="font-size:0.78rem;"><?php echo htmlspecialchars($row['no_hp'] ?: '-'); ?></span>
                                </td>
                                <td>
                                    <?php if ($row['role'] === 'admin'): ?>
                                        <span class="badge-status badge-status-menunggu">Admin</span>
                                    <?php elseif ($row['role'] === 'petugas'): ?>
                                        <span class="badge-status badge-status-proses">Petugas</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-status-terverifikasi">Masyarakat</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size:0.78rem;"><?php echo (int)$row['total_laporan']; ?> laporan</span>
                                </td>
                                <td class="text-muted" style="font-size:0.75rem;">
                                    <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                </td>
                                <td style="text-align:center;">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-xs btn-secondary py-1 px-2" onclick='bukaModalEdit(<?php echo json_encode($row); ?>)' title="Edit">
                                            <i data-lucide="edit-2" class="lucide-xs"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2" onclick="bukaModalReset(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')" title="Reset Password">
                                            <i data-lucide="key" class="lucide-xs"></i>
                                        </button>
                                        <?php if ($row['id'] != $current_user_id): ?>
                                            <button type="button" class="btn btn-xs btn-danger py-1 px-2" onclick="hapusPengguna(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')" title="Hapus">
                                                <i data-lucide="trash-2" class="lucide-xs"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT -->
    <div class="modal fade" id="modalPengguna" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:var(--radius-md); border:1px solid var(--color-border);">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="modal-title" style="font-size:0.85rem; font-weight:700;">Tambah Pengguna</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-pengguna">
                    <div class="modal-body p-3">
                        <input type="hidden" id="user_id" name="id" value="">
                        <input type="hidden" id="aksi" name="aksi" value="tambah">

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Nama Lengkap <span class="required-star">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="nama" name="nama" required maxlength="255">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Email <span class="required-star">*</span></label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" required maxlength="255">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">No. HP</label>
                            <input type="text" class="form-control form-control-sm" id="no_hp" name="no_hp" maxlength="15">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Role Akun <span class="required-star">*</span></label>
                            <select class="form-select form-select-sm" id="role" name="role" required>
                                <option value="masyarakat">Masyarakat</option>
                                <option value="petugas">Petugas Lapangan</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <div class="form-group-clean mb-0" id="box-password">
                            <label class="form-label-clean" style="font-size:0.75rem;">Kata Sandi <span class="required-star">*</span></label>
                            <input type="password" class="form-control form-control-sm" id="password" name="password" minlength="6" placeholder="Min. 6 Karakter">
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3">
                        <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-xs" id="btn-save-user">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL RESET PASSWORD -->
    <div class="modal fade" id="modalReset" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:var(--radius-md); border:1px solid var(--color-border);">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" style="font-size:0.85rem; font-weight:700;">Reset Password</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-reset">
                    <div class="modal-body p-3">
                        <input type="hidden" id="reset_user_id" name="id" value="">
                        <input type="hidden" name="aksi" value="reset_password">
                        <p style="font-size:0.78rem;" class="text-muted">Password baru untuk <strong id="reset_nama_user" class="text-dark"></strong>:</p>

                        <div class="form-group-clean mb-0">
                            <label class="form-label-clean" style="font-size:0.75rem;">Kata Sandi Baru <span class="required-star">*</span></label>
                            <input type="password" class="form-control form-control-sm" name="new_password" required minlength="6" placeholder="Min. 6 Karakter">
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3">
                        <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-xs" id="btn-save-reset">Ubah Sandi</button>
                    </div>
                </form>
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

        const modalUser = new bootstrap.Modal(document.getElementById('modalPengguna'));
        const modalReset = new bootstrap.Modal(document.getElementById('modalReset'));

        function bukaModalTambah() {
            document.getElementById('form-pengguna').reset();
            document.getElementById('user_id').value = '';
            document.getElementById('aksi').value = 'tambah';
            document.getElementById('box-password').style.display = 'block';
            document.getElementById('password').required = true;
            document.getElementById('modal-title').innerText = 'Tambah Pengguna Baru';
            modalUser.show();
        }

        function bukaModalEdit(data) {
            document.getElementById('form-pengguna').reset();
            document.getElementById('user_id').value = data.id;
            document.getElementById('aksi').value = 'edit';
            document.getElementById('nama').value = data.nama || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('no_hp').value = data.no_hp || '';
            document.getElementById('role').value = data.role || 'masyarakat';
            document.getElementById('box-password').style.display = 'none';
            document.getElementById('password').required = false;
            document.getElementById('modal-title').innerText = 'Edit Pengguna';
            modalUser.show();
        }

        function bukaModalReset(id, nama) {
            document.getElementById('form-reset').reset();
            document.getElementById('reset_user_id').value = id;
            document.getElementById('reset_nama_user').innerText = nama;
            modalReset.show();
        }

        // AJAX Form Simpan
        document.getElementById('form-pengguna').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-user');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('../proses/pengguna-proses.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menyimpan pengguna.');
                    btn.disabled = false;
                    btn.innerText = 'Simpan';
                }
            })
            .catch(() => {
                alert('Gagal terhubung ke server.');
                btn.disabled = false;
                btn.innerText = 'Simpan';
            });
        });

        // AJAX Form Reset
        document.getElementById('form-reset').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-reset');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('../proses/pengguna-proses.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Kata sandi berhasil direset!');
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mereset sandi.');
                    btn.disabled = false;
                    btn.innerText = 'Ubah Sandi';
                }
            })
            .catch(() => {
                alert('Gagal terhubung ke server.');
                btn.disabled = false;
                btn.innerText = 'Ubah Sandi';
            });
        });

        function hapusPengguna(id, nama) {
            if (confirm(`Yakin ingin menghapus akun "${nama}"?`)) {
                const formData = new FormData();
                formData.append('aksi', 'hapus');
                formData.append('id', id);

                fetch('../proses/pengguna-proses.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus pengguna.');
                    }
                })
                .catch(() => alert('Gagal terhubung ke server.'));
            }
        }
    </script>
</body>
</html>
