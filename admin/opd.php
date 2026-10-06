<?php
// =====================================================
// MANAJEMEN OPD - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

$query = "SELECT o.*, COUNT(lo.id) as total_laporan
          FROM opd o
          LEFT JOIN laporan_opd lo ON o.id = lo.opd_id
          GROUP BY o.id
          ORDER BY o.nama_opd ASC";
$result = mysqli_query($koneksi, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data OPD - Admin SIGALING PADANG</title>

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
                <li><a href="opd.php" class="active"><i data-lucide="building-2" class="lucide-sm"></i> Data OPD</a></li>
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
                    <h1 class="page-title">Organisasi Perangkat Daerah (OPD)</h1>
                </div>
                <button type="button" class="btn btn-xs btn-primary" onclick="bukaModalTambah()">
                    <i data-lucide="plus" class="lucide-xs"></i> Tambah OPD
                </button>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <div class="table-clean-wrapper">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>Nama Instansi / OPD</th>
                                <th>Deskripsi Penanganan</th>
                                <th>Kontak / Telp</th>
                                <th>Total Penugasan</th>
                                <th style="width:90px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr id="row-<?php echo $row['id']; ?>">
                                <td>
                                    <strong class="text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['nama_opd']); ?></strong>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size:0.78rem;"><?php echo htmlspecialchars($row['deskripsi'] ?: '-'); ?></span>
                                </td>
                                <td>
                                    <span class="font-monospace" style="font-size:0.78rem;"><?php echo htmlspecialchars($row['kontak'] ?: '-'); ?></span>
                                </td>
                                <td>
                                    <span class="badge-status badge-status-terverifikasi"><?php echo (int)$row['total_laporan']; ?> laporan</span>
                                </td>
                                <td style="text-align:center;">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-xs btn-secondary py-1 px-2" onclick='bukaModalEdit(<?php echo json_encode($row); ?>)' title="Edit">
                                            <i data-lucide="edit-2" class="lucide-xs"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger py-1 px-2" onclick="hapusOpd(<?php echo $row['id']; ?>, <?php echo (int)$row['total_laporan']; ?>)" title="Hapus">
                                            <i data-lucide="trash-2" class="lucide-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data OPD.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT OPD -->
    <div class="modal fade" id="modalOpd" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:var(--radius-md); border:1px solid var(--color-border);">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="modal-title" style="font-size:0.85rem; font-weight:700;">Tambah OPD</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-opd">
                    <div class="modal-body p-3">
                        <input type="hidden" id="opd_id" name="id" value="">
                        <input type="hidden" id="aksi" name="aksi" value="tambah">

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Nama OPD <span class="required-star">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="nama_opd" name="nama_opd" placeholder="Dinas Lingkungan Hidup..." required maxlength="255">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Kontak / Telp</label>
                            <input type="text" class="form-control form-control-sm" id="kontak" name="kontak" placeholder="0751-xxxx / 08xxxx" maxlength="255">
                        </div>

                        <div class="form-group-clean mb-0">
                            <label class="form-label-clean" style="font-size:0.75rem;">Tupoksi Penanganan</label>
                            <textarea class="form-control form-control-sm" id="deskripsi" name="deskripsi" rows="2" placeholder="Menangani masalah sampah dan limbah..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3">
                        <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-xs" id="btn-save-opd">Simpan</button>
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

        const modalOpdEl = document.getElementById('modalOpd');
        const modalOpd = new bootstrap.Modal(modalOpdEl);

        function bukaModalTambah() {
            document.getElementById('form-opd').reset();
            document.getElementById('opd_id').value = '';
            document.getElementById('aksi').value = 'tambah';
            document.getElementById('modal-title').innerText = 'Tambah OPD Baru';
            modalOpd.show();
        }

        function bukaModalEdit(data) {
            document.getElementById('form-opd').reset();
            document.getElementById('opd_id').value = data.id;
            document.getElementById('aksi').value = 'edit';
            document.getElementById('nama_opd').value = data.nama_opd || '';
            document.getElementById('kontak').value = data.kontak || '';
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('modal-title').innerText = 'Edit Data OPD';
            modalOpd.show();
        }

        // AJAX Form Simpan
        document.getElementById('form-opd').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-opd');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('../proses/opd-proses.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menyimpan data OPD.');
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

        function hapusOpd(id, count) {
            if (count > 0) {
                alert(`OPD ini tidak dapat dihapus karena telah menerima ${count} penugasan laporan.`);
                return;
            }

            if (confirm('Yakin ingin menghapus OPD ini?')) {
                const formData = new FormData();
                formData.append('aksi', 'hapus');
                formData.append('id', id);

                fetch('../proses/opd-proses.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus data OPD.');
                    }
                })
                .catch(() => alert('Gagal terhubung ke server.'));
            }
        }
    </script>
</body>
</html>
