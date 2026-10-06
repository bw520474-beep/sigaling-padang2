<?php
// =====================================================
// MANAJEMEN KATEGORI - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

$query = "SELECT k.*, COUNT(l.id) as jumlah_laporan
          FROM kategori k
          LEFT JOIN laporan l ON k.id = l.kategori_id
          GROUP BY k.id
          ORDER BY k.nama_kategori ASC";
$result = mysqli_query($koneksi, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Laporan - Admin SIGALING PADANG</title>

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
                <li><a href="kategori.php" class="active"><i data-lucide="tags" class="lucide-sm"></i> Kategori Laporan</a></li>
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
                    <h1 class="page-title">Master Data Kategori</h1>
                </div>
                <button type="button" class="btn btn-xs btn-primary" onclick="bukaModalTambah()">
                    <i data-lucide="plus" class="lucide-xs"></i> Tambah Kategori
                </button>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <div class="table-clean-wrapper">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th style="width:50px; text-align:center;">Ikon</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Total Laporan</th>
                                <th style="width:90px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-kategori">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr id="row-<?php echo $row['id']; ?>">
                                <td style="text-align:center; font-size:1.1rem;">
                                    <span><?php echo $row['ikon'] ?: '📋'; ?></span>
                                </td>
                                <td>
                                    <strong class="text-dark" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['nama_kategori']); ?></strong>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size:0.78rem;"><?php echo htmlspecialchars($row['deskripsi'] ?: '-'); ?></span>
                                </td>
                                <td>
                                    <span class="badge-status badge-status-terverifikasi"><?php echo (int)$row['jumlah_laporan']; ?> laporan</span>
                                </td>
                                <td style="text-align:center;">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-xs btn-secondary py-1 px-2" onclick='bukaModalEdit(<?php echo json_encode($row); ?>)' title="Edit">
                                            <i data-lucide="edit-2" class="lucide-xs"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger py-1 px-2" onclick="hapusKategori(<?php echo $row['id']; ?>, <?php echo (int)$row['jumlah_laporan']; ?>)" title="Hapus">
                                            <i data-lucide="trash-2" class="lucide-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada kategori permasalahan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT -->
    <div class="modal fade" id="modalKategori" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:var(--radius-md); border:1px solid var(--color-border);">
                <div class="modal-header py-2 px-3">
                    <h6 class="modal-title" id="modal-title" style="font-size:0.85rem; font-weight:700;">Tambah Kategori</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-kategori">
                    <div class="modal-body p-3">
                        <input type="hidden" id="kategori_id" name="id" value="">
                        <input type="hidden" id="aksi" name="aksi" value="tambah">

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Ikon Emoji <span class="required-star">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="ikon" name="ikon" placeholder="Contoh: 🗑️, 🌊, 🌳" required maxlength="10">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" style="font-size:0.75rem;">Nama Kategori <span class="required-star">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="nama_kategori" name="nama_kategori" placeholder="Nama Kategori..." required maxlength="100">
                        </div>

                        <div class="form-group-clean mb-0">
                            <label class="form-label-clean" style="font-size:0.75rem;">Deskripsi</label>
                            <textarea class="form-control form-control-sm" id="deskripsi" name="deskripsi" rows="2" placeholder="Penjelasan singkat..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3">
                        <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-xs" id="btn-save-kat">Simpan</button>
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

        const modalKategoriEl = document.getElementById('modalKategori');
        const modalKategori = new bootstrap.Modal(modalKategoriEl);

        function bukaModalTambah() {
            document.getElementById('form-kategori').reset();
            document.getElementById('kategori_id').value = '';
            document.getElementById('aksi').value = 'tambah';
            document.getElementById('modal-title').innerText = 'Tambah Kategori Baru';
            modalKategori.show();
        }

        function bukaModalEdit(data) {
            document.getElementById('form-kategori').reset();
            document.getElementById('kategori_id').value = data.id;
            document.getElementById('aksi').value = 'edit';
            document.getElementById('ikon').value = data.ikon || '';
            document.getElementById('nama_kategori').value = data.nama_kategori || '';
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('modal-title').innerText = 'Edit Kategori';
            modalKategori.show();
        }

        // AJAX Form Simpan
        document.getElementById('form-kategori').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-kat');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('../proses/kategori-proses.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menyimpan kategori.');
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

        function hapusKategori(id, count) {
            if (count > 0) {
                alert(`Kategori ini tidak dapat dihapus karena telah digunakan oleh ${count} laporan.`);
                return;
            }

            if (confirm('Yakin ingin menghapus kategori ini?')) {
                const formData = new FormData();
                formData.append('aksi', 'hapus');
                formData.append('id', id);

                fetch('../proses/kategori-proses.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus kategori.');
                    }
                })
                .catch(() => alert('Gagal terhubung ke server.'));
            }
        }
    </script>
</body>
</html>
