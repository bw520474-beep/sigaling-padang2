<?php
// =====================================================
// DETAIL & VERIFIKASI LAPORAN - ADMIN SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_admin();

$laporan_id = intval($_GET['id'] ?? 0);

if ($laporan_id <= 0) {
    header('Location: laporan.php');
    exit;
}

// 1. Ambil Data Laporan
$query = "SELECT l.*, k.nama_kategori, k.ikon
          FROM laporan l
          LEFT JOIN kategori k ON l.kategori_id = k.id
          WHERE l.id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $laporan_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: laporan.php');
    exit;
}

$laporan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// 2. Ambil Foto
$query_foto = "SELECT nama_file, jenis FROM foto_laporan WHERE laporan_id = ? AND jenis = 'bukti_laporan'";
$stmt_foto = mysqli_prepare($koneksi, $query_foto);
mysqli_stmt_bind_param($stmt_foto, "i", $laporan_id);
mysqli_stmt_execute($stmt_foto);
$result_foto = mysqli_stmt_get_result($stmt_foto);
$foto_list = [];
while ($row = mysqli_fetch_assoc($result_foto)) {
    $foto_list[] = $row;
}
mysqli_stmt_close($stmt_foto);

// 3. Ambil Daftar OPD untuk dropdown teruskan
$result_opd = mysqli_query($koneksi, "SELECT id, nama_opd FROM opd ORDER BY nama_opd ASC");

// 4. Cek apakah sudah diteruskan ke OPD
$query_opd_info = "SELECT lo.*, o.nama_opd FROM laporan_opd lo 
                    LEFT JOIN opd o ON lo.opd_id = o.id 
                    WHERE lo.laporan_id = ? ORDER BY lo.tanggal_diteruskan DESC LIMIT 1";
$stmt_opd_info = mysqli_prepare($koneksi, $query_opd_info);
mysqli_stmt_bind_param($stmt_opd_info, "i", $laporan_id);
mysqli_stmt_execute($stmt_opd_info);
$result_opd_info = mysqli_stmt_get_result($stmt_opd_info);
$opd_info = mysqli_num_rows($result_opd_info) > 0 ? mysqli_fetch_assoc($result_opd_info) : null;
mysqli_stmt_close($stmt_opd_info);

$badge_status = [
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
    <title>Detail Laporan #<?php echo htmlspecialchars($laporan['kode_laporan']); ?> - Admin SIGALING</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .info-grid-list {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            font-size: 0.88rem;
        }

        .info-grid-row {
            display: flex;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--color-border);
        }

        .info-grid-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-grid-label {
            width: 140px;
            color: var(--color-text-muted);
            font-weight: 600;
            flex-shrink: 0;
        }

        .info-grid-val {
            color: var(--color-text-main);
            flex: 1;
        }

        .foto-grid-admin {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 0.75rem;
        }

        .foto-grid-admin img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .foto-grid-admin img:hover {
            transform: scale(1.04);
        }

        #map-detail {
            height: 240px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            width: 100%;
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
                    <button type="button" class="btn btn-sm btn-ghost d-lg-none p-1" id="toggle-sidebar">
                        <i data-lucide="menu" style="width:20px; height:20px;"></i>
                    </button>
                    <h1 class="page-title">Detail Laporan</h1>
                </div>
                <a href="laporan.php" class="btn btn-sm btn-secondary">
                    <i data-lucide="arrow-left" class="lucide-sm"></i> Kembali
                </a>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <div class="row g-3">

                    <!-- KOLOM UTAMA (INFO & FOTO) -->
                    <div class="col-lg-8">

                        <!-- Info Card -->
                        <div class="card-custom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="font-monospace fw-bold text-primary" style="font-size:1.1rem;">
                                        <?php echo htmlspecialchars($laporan['kode_laporan']); ?>
                                    </span>
                                    <span class="badge-status <?php echo $badge_status[$laporan['status']] ?? ''; ?>">
                                        <?php echo htmlspecialchars($laporan['status']); ?>
                                    </span>
                                </div>
                                <span class="badge-priority <?php echo ($laporan['prioritas'] === 'Tinggi') ? 'badge-priority-tinggi' : (($laporan['prioritas'] === 'Sedang') ? 'badge-priority-sedang' : 'badge-priority-rendah'); ?>">
                                    Prioritas <?php echo htmlspecialchars($laporan['prioritas'] ?: 'Sedang'); ?>
                                </span>
                            </div>

                            <h2 style="font-size:1.2rem; margin-bottom:0.6rem;"><?php echo htmlspecialchars($laporan['judul']); ?></h2>
                            <p style="font-size:0.9rem; color:var(--color-text-muted); margin-bottom:1.25rem; line-height:1.55;">
                                <?php echo nl2br(htmlspecialchars($laporan['deskripsi'])); ?>
                            </p>

                            <div class="info-grid-list">
                                <div class="info-grid-row">
                                    <div class="info-grid-label">Kategori</div>
                                    <div class="info-grid-val"><?php echo ($laporan['ikon'] ?? '📁') . ' ' . htmlspecialchars($laporan['nama_kategori'] ?? '-'); ?></div>
                                </div>
                                <div class="info-grid-row">
                                    <div class="info-grid-label">Pelapor</div>
                                    <div class="info-grid-val fw-semibold"><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></div>
                                </div>
                                <div class="info-grid-row">
                                    <div class="info-grid-label">Kontak</div>
                                    <div class="info-grid-val"><?php echo htmlspecialchars($laporan['kontak_pelapor'] ?: '-'); ?></div>
                                </div>
                                <div class="info-grid-row">
                                    <div class="info-grid-label">Waktu Masuk</div>
                                    <div class="info-grid-val"><?php echo date('d F Y, H:i', strtotime($laporan['tanggal_laporan'])); ?> WIB</div>
                                </div>
                                <div class="info-grid-row">
                                    <div class="info-grid-label">Alamat / Wilayah</div>
                                    <div class="info-grid-val">
                                        <?php
                                        echo htmlspecialchars($laporan['alamat'] ?: '-');
                                        if ($laporan['kecamatan']) echo ' (' . htmlspecialchars($laporan['kecamatan']) . ')';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Foto Bukti -->
                        <div class="card-custom">
                            <div class="card-header-clean">
                                <h6><i data-lucide="image" class="lucide-sm text-primary"></i> Foto Bukti Laporan</h6>
                            </div>
                            <?php if (!empty($foto_list)): ?>
                                <div class="foto-grid-admin">
                                    <?php foreach ($foto_list as $f): ?>
                                        <img src="../uploads/laporan/<?php echo htmlspecialchars($f['nama_file']); ?>"
                                            alt="Foto Bukti" onclick="bukaModalFoto(this.src)">
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted m-0" style="font-size:0.85rem;">Tidak ada foto bukti</p>
                            <?php endif; ?>
                        </div>

                        <!-- Peta Lokasi -->
                        <div class="card-custom">
                            <div class="card-header-clean">
                                <h6><i data-lucide="map-pin" class="lucide-sm text-primary"></i> Titik Lokasi Peta</h6>
                            </div>
                            <div id="map-detail"></div>
                        </div>

                    </div>

                    <!-- KOLOM AKSI (VERIFIKASI & FORWARD OPD) -->
                    <div class="col-lg-4">

                        <!-- 1. Form Verifikasi (Jika Menunggu Verifikasi) -->
                        <?php if ($laporan['status'] === 'Menunggu Verifikasi'): ?>
                            <div class="card-custom">
                                <div class="card-header-clean">
                                    <h6><i data-lucide="check-square" class="lucide-sm text-primary"></i> Aksi Verifikasi Laporan</h6>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label-clean" style="font-size:0.82rem;">Tentukan Tingkat Prioritas</label>
                                    <select id="prioritas" class="form-select form-select-sm">
                                        <option value="Rendah" <?php echo ($laporan['prioritas'] === 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
                                        <option value="Sedang" <?php echo ($laporan['prioritas'] === 'Sedang' || empty($laporan['prioritas'])) ? 'selected' : ''; ?>>Sedang (Standar)</option>
                                        <option value="Tinggi" <?php echo ($laporan['prioritas'] === 'Tinggi') ? 'selected' : ''; ?>>Tinggi (Urgent)</option>
                                    </select>
                                </div>

                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-primary w-100" onclick="verifikasi('Terverifikasi')">
                                        <i data-lucide="check-circle" class="lucide-sm"></i> Verifikasi Laporan
                                    </button>
                                    <button type="button" class="btn btn-danger w-100" onclick="verifikasi('Ditolak')">
                                        <i data-lucide="x-circle" class="lucide-sm"></i> Tolak Laporan
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 2. Teruskan ke OPD (Jika Terverifikasi dan belum ada OPD) -->
                        <?php if ($laporan['status'] === 'Terverifikasi' && !$opd_info): ?>
                            <div class="card-custom">
                                <div class="card-header-clean">
                                    <h6><i data-lucide="send" class="lucide-sm text-primary"></i> Teruskan ke OPD Teknis</h6>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label-clean" style="font-size:0.82rem;">Pilih OPD Tujuan</label>
                                    <select id="opd_id" class="form-select form-select-sm">
                                        <option value="">-- Pilih Organisasi Perangkat Daerah --</option>
                                        <?php while ($opd = mysqli_fetch_assoc($result_opd)): ?>
                                            <option value="<?php echo $opd['id']; ?>"><?php echo htmlspecialchars($opd['nama_opd']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label-clean" style="font-size:0.82rem;">Catatan Khusus (Opsional)</label>
                                    <textarea id="catatan_opd" class="form-control form-control-sm" rows="3" placeholder="Instruksi tambahan untuk dinas terkait..."></textarea>
                                </div>

                                <button type="button" class="btn btn-primary w-100" onclick="teruskanOPD()">
                                    <i data-lucide="share-2" class="lucide-sm"></i> Teruskan Laporan
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- 3. Status Terusan OPD (Jika sudah diteruskan) -->
                        <?php if ($opd_info): ?>
                            <div class="card-custom">
                                <div class="card-header-clean">
                                    <h6><i data-lucide="building" class="lucide-sm text-primary"></i> Telah Diteruskan</h6>
                                </div>
                                <div style="font-size:0.85rem;">
                                    <strong class="d-block mb-1"><?php echo htmlspecialchars($opd_info['nama_opd']); ?></strong>
                                    <span class="text-muted d-block" style="font-size:0.78rem;">
                                        <i data-lucide="calendar" class="lucide-sm me-1"></i>
                                        <?php echo date('d M Y, H:i', strtotime($opd_info['tanggal_diteruskan'])); ?> WIB
                                    </span>
                                    <?php if ($opd_info['catatan']): ?>
                                        <div class="mt-2 p-2 bg-light rounded" style="font-size:0.8rem; font-style:italic;">
                                            "<?php echo htmlspecialchars($opd_info['catatan']); ?>"
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 4. Laporan Selesai / Ditolak Banner -->
                        <?php if ($laporan['status'] === 'Selesai'): ?>
                            <div class="alert-clean alert-clean-success">
                                <i data-lucide="check-circle" class="lucide-sm"></i>
                                <div><strong>Selesai</strong><br>Laporan ini sudah tuntas ditangani oleh petugas lapangan.</div>
                            </div>
                        <?php elseif ($laporan['status'] === 'Ditolak'): ?>
                            <div class="alert-clean alert-clean-danger">
                                <i data-lucide="x-circle" class="lucide-sm"></i>
                                <div><strong>Laporan Ditolak</strong><br>Laporan tidak memenuhi kriteria verifikasi.</div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Foto Preview -->
    <div class="modal fade" id="modalFoto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 text-center">
                <img id="img-modal-target" src="" alt="Bukti Foto" style="max-width:100%; max-height:85vh; border-radius:var(--radius-md); box-shadow:var(--shadow-lg);">
            </div>
        </div>
    </div>

    <!-- Backdrop for mobile sidebar -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        const laporanId = <?php echo $laporan_id; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            <?php if ($laporan['latitude'] && $laporan['longitude']): ?>
                const lat = <?php echo (float)$laporan['latitude']; ?>;
                const lng = <?php echo (float)$laporan['longitude']; ?>;

                const map = L.map('map-detail', {
                    attributionControl: false
                }).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);

                const pin = L.divIcon({
                    html: `<div class="custom-pin" style="background-color:#15803D; width:26px; height:26px;">
                         <i data-lucide="map-pin" style="width:14px; height:14px;"></i>
                       </div>`,
                    className: '',
                    iconSize: [26, 26],
                    iconAnchor: [13, 26]
                });
                L.marker([lat, lng], {
                    icon: pin
                }).addTo(map).bindPopup('<b>Titik Lokasi Laporan</b><br><?php echo addslashes(htmlspecialchars($laporan["alamat"] ?: "Kota Padang")); ?>');
                lucide.createIcons();
            <?php else: ?>
                document.getElementById('map-detail').innerHTML = '<div class="text-center py-4 text-muted" style="font-size:0.85rem;">Koordinat lokasi tidak tersedia</div>';
            <?php endif; ?>
        });

        function bukaModalFoto(src) {
            document.getElementById('img-modal-target').src = src;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        function verifikasi(status) {
            const prioritas = document.getElementById('prioritas') ? document.getElementById('prioritas').value : 'Sedang';
            const confirmMsg = status === 'Terverifikasi' ? 'Verifikasi laporan ini sebagai VALID?' : 'Yakin ingin MENOLAK laporan ini?';

            if (!confirm(confirmMsg)) return;

            fetch('../proses/verifikasi-laporan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `laporan_id=${laporanId}&status=${encodeURIComponent(status)}&prioritas=${encodeURIComponent(prioritas)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Gagal verifikasi', 'danger');
                    }
                })
                .catch(err => showToast('Terjadi kesalahan: ' + err.message, 'danger'));
        }

        function teruskanOPD() {
            const opdId = document.getElementById('opd_id').value;
            const catatan = document.getElementById('catatan_opd').value;

            if (!opdId) {
                showToast('Silakan pilih OPD tujuan terlebih dahulu', 'warning');
                return;
            }

            fetch('../proses/teruskan-laporan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `laporan_id=${laporanId}&opd_id=${opdId}&catatan=${encodeURIComponent(catatan)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Gagal meneruskan', 'danger');
                    }
                })
                .catch(err => showToast('Terjadi kesalahan: ' + err.message, 'danger'));
        }
    </script>
</body>

</html>