<?php
// =====================================================
// MANAJEMEN PETUGAS LAPANGAN - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

$query = "SELECT u.*, 
                 COUNT(p.id) as total_penanganan,
                 COUNT(CASE WHEN p.status = 'Selesai' THEN 1 END) as selesai
          FROM users u
          LEFT JOIN penanganan p ON u.id = p.petugas_id
          WHERE u.role = 'petugas'
          GROUP BY u.id
          ORDER BY u.nama ASC";
$result = mysqli_query($koneksi, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Petugas - Admin SIGALING PADANG</title>

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
                <li><a href="petugas.php" class="active"><i data-lucide="shield" class="lucide-sm"></i> Akun Petugas</a></li>
                <li><a href="pengguna.php"><i data-lucide="users" class="lucide-sm"></i> Pengguna</a></li>

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
                    <h1 class="page-title">Manajemen Akun Petugas Lapangan</h1>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="bukaModalTambah()">
                    <i data-lucide="user-plus" class="lucide-sm"></i> Tambah Petugas
                </button>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <div class="table-clean-wrapper">
                    <table class="table-clean table-responsive-cards">
                        <thead>
                            <tr>
                                <th>Petugas</th>
                                <th>Email Akun</th>
                                <th>No. HP / Kontak</th>
                                <th>Tugas Ditangani</th>
                                <th>Tugas Selesai</th>
                                <th style="width:160px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr id="row-<?php echo $row['id']; ?>">
                                <td data-label="Petugas">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar-circle" style="width:30px; height:30px; font-size:0.75rem;">
                                            <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                                        </div>
                                        <strong class="text-dark"><?php echo htmlspecialchars($row['nama']); ?></strong>
                                    </div>
                                </td>
                                <td data-label="Email">
                                    <span style="font-size:0.85rem;"><?php echo htmlspecialchars($row['email']); ?></span>
                                </td>
                                <td data-label="Kontak">
                                    <span style="font-size:0.85rem;"><?php echo htmlspecialchars($row['no_hp'] ?: '-'); ?></span>
                                </td>
                                <td data-label="Total Tugas">
                                    <span class="badge-status badge-status-proses"><?php echo (int)$row['total_penanganan']; ?> penanganan</span>
                                </td>
                                <td data-label="Selesai">
                                    <span class="badge-status badge-status-selesai"><?php echo (int)$row['selesai']; ?> selesai</span>
                                </td>
                                <td data-label="Aksi" style="text-align:center;">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-secondary py-1 px-2" onclick='bukaModalEdit(<?php echo json_encode($row); ?>)' title="Edit Petugas">
                                            <i data-lucide="edit-2" class="lucide-sm"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" onclick="bukaModalReset(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')" title="Reset Password">
                                            <i data-lucide="key" class="lucide-sm"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger py-1 px-2" onclick="hapusPetugas(<?php echo $row['id']; ?>, <?php echo (int)$row['total_penanganan']; ?>)" title="Hapus Akun">
                                            <i data-lucide="trash-2" class="lucide-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada akun petugas lapangan</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT PETUGAS -->
    <div class="modal fade" id="modalPetugas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-lg); border:1px solid var(--color-border);">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fs-6 fw-bold" id="modal-title">Tambah Petugas Lapangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-petugas">
                        <input type="hidden" id="petugas_id">
                        
                        <div class="form-group-clean">
                            <label class="form-label-clean" for="nama">
                                <span>Nama Lengkap</span>
                                <span class="required-star">*</span>
                            </label>
                            <input type="text" id="nama" class="form-control form-control-sm" required placeholder="Nama lengkap petugas">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" for="email">
                                <span>Email Akun</span>
                                <span class="required-star">*</span>
                            </label>
                            <input type="email" id="email" class="form-control form-control-sm" required placeholder="petugas@sigaling.local">
                        </div>

                        <div class="form-group-clean">
                            <label class="form-label-clean" for="no_hp">
                                <span>Nomor HP / WhatsApp</span>
                            </label>
                            <input type="text" id="no_hp" class="form-control form-control-sm" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="form-group-clean mb-0" id="group-password">
                            <label class="form-label-clean" for="password">
                                <span>Password Default</span>
                                <span class="required-star">*</span>
                            </label>
                            <input type="password" id="password" class="form-control form-control-sm" minlength="6" placeholder="Minimal 6 karakter">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light" style="border-radius:0 0 var(--radius-lg) var(--radius-lg);">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="simpanPetugas()">
                        <i data-lucide="check" class="lucide-sm"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL RESET PASSWORD -->
    <div class="modal fade" id="modalReset" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-lg); border:1px solid var(--color-border);">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fs-6 fw-bold">Reset Password Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p style="font-size:0.85rem;" id="reset-petugas-nama"></p>
                    <input type="hidden" id="reset_petugas_id">
                    
                    <div class="form-group-clean mb-0">
                        <label class="form-label-clean" for="password_baru">
                            <span>Password Baru</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="password" id="password_baru" class="form-control form-control-sm" minlength="6" placeholder="Minimal 6 karakter baru">
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light" style="border-radius:0 0 var(--radius-lg) var(--radius-lg);">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="simpanResetPassword()">
                        <i data-lucide="key" class="lucide-sm"></i> Update Password
                    </button>
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

        const modalPetugas = new bootstrap.Modal(document.getElementById('modalPetugas'));
        const modalReset = new bootstrap.Modal(document.getElementById('modalReset'));

        function bukaModalTambah() {
            document.getElementById('modal-title').textContent = 'Tambah Petugas Baru';
            document.getElementById('form-petugas').reset();
            document.getElementById('petugas_id').value = '';
            document.getElementById('group-password').style.display = 'block';
            document.getElementById('password').required = true;
            modalPetugas.show();
            setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
        }

        function bukaModalEdit(data) {
            document.getElementById('modal-title').textContent = 'Edit Data Petugas';
            document.getElementById('petugas_id').value = data.id;
            document.getElementById('nama').value = data.nama;
            document.getElementById('email').value = data.email;
            document.getElementById('no_hp').value = data.no_hp || '';
            document.getElementById('group-password').style.display = 'none';
            document.getElementById('password').required = false;
            modalPetugas.show();
        }

        function bukaModalReset(id, nama) {
            document.getElementById('reset_petugas_id').value = id;
            document.getElementById('reset-petugas-nama').innerHTML = 'Mengatur ulang kata sandi untuk akun petugas: <strong>' + nama + '</strong>';
            document.getElementById('password_baru').value = '';
            modalReset.show();
        }

        function simpanPetugas() {
            const id = document.getElementById('petugas_id').value;
            const nama = document.getElementById('nama').value.trim();
            const email = document.getElementById('email').value.trim();
            const no_hp = document.getElementById('no_hp').value.trim();
            const password = document.getElementById('password').value;

            if (!nama || !email) {
                showToast('Nama dan Email wajib diisi', 'warning');
                return;
            }

            const aksi = id ? 'edit' : 'tambah';
            if (aksi === 'tambah' && (!password || password.length < 6)) {
                showToast('Password minimal 6 karakter', 'warning');
                return;
            }

            fetch('../proses/petugas-proses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `aksi=${aksi}&id=${id}&nama=${encodeURIComponent(nama)}&email=${encodeURIComponent(email)}&no_hp=${encodeURIComponent(no_hp)}&password=${encodeURIComponent(password)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    modalPetugas.hide();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Gagal menyimpan petugas', 'danger');
                }
            })
            .catch(err => showToast('Terjadi kesalahan: ' + err.message, 'danger'));
        }

        function simpanResetPassword() {
            const id = document.getElementById('reset_petugas_id').value;
            const password = document.getElementById('password_baru').value;

            if (!password || password.length < 6) {
                showToast('Password baru minimal 6 karakter', 'warning');
                return;
            }

            fetch('../proses/petugas-proses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `aksi=reset_password&id=${id}&password=${encodeURIComponent(password)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    modalReset.hide();
                } else {
                    showToast(data.message || 'Gagal reset password', 'danger');
                }
            })
            .catch(err => showToast('Terjadi kesalahan: ' + err.message, 'danger'));
        }

        function hapusPetugas(id, totalPenanganan) {
            if (totalPenanganan > 0) {
                showToast('Petugas tidak dapat dihapus karena memiliki riwayat penanganan tugas', 'warning');
                return;
            }

            if (!confirm('Yakin ingin menghapus akun petugas ini?')) return;

            fetch('../proses/petugas-proses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `aksi=hapus&id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    const row = document.getElementById('row-' + id);
                    if (row) row.remove();
                } else {
                    showToast(data.message || 'Gagal menghapus petugas', 'danger');
                }
            })
            .catch(err => showToast('Terjadi kesalahan: ' + err.message, 'danger'));
        }
    </script>
</body>
</html>
