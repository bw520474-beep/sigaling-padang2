<?php
// =====================================================
// DETAIL TUGAS & UPDATE PENANGANAN - PETUGAS SIGALING
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

require_petugas();

$petugas_id = get_user_id();
$penanganan_id = intval($_GET['id'] ?? 0);

if ($penanganan_id <= 0) {
    header('Location: tugas.php');
    exit;
}

// 1. Ambil Data Penanganan & Laporan
$query = "SELECT p.*, 
                 l.id as laporan_id, l.kode_laporan, l.judul, l.deskripsi, l.nama_pelapor, 
                 l.kontak_pelapor, l.latitude, l.longitude, l.alamat, l.kecamatan, 
                 l.prioritas, l.status as status_laporan, l.tanggal_laporan,
                 k.nama_kategori, k.ikon
          FROM penanganan p
          JOIN laporan l ON p.laporan_id = l.id
          LEFT JOIN kategori k ON l.kategori_id = k.id
          WHERE p.id = ? AND p.petugas_id = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ii", $penanganan_id, $petugas_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: tugas.php');
    exit;
}

$tugas = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$laporan_id = (int)$tugas['laporan_id'];

// 2. Ambil Foto Bukti Laporan Awal
$query_foto = "SELECT nama_file, jenis FROM foto_laporan WHERE laporan_id = ?";
$stmt_foto = mysqli_prepare($koneksi, $query_foto);
mysqli_stmt_bind_param($stmt_foto, "i", $laporan_id);
mysqli_stmt_execute($stmt_foto);
$result_foto = mysqli_stmt_get_result($stmt_foto);
$foto_laporan = [];
while ($row = mysqli_fetch_assoc($result_foto)) {
    $foto_laporan[] = $row;
}
mysqli_stmt_close($stmt_foto);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penanganan Tugas #<?php echo htmlspecialchars($tugas['kode_laporan']); ?> - Petugas SIGALING</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        #map-tugas {
            height: 220px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            width: 100%;
        }

        .foto-grid-petugas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
        }

        .foto-grid-petugas img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .foto-grid-petugas img:hover {
            transform: scale(1.03);
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
                    <h1 class="page-title">Progres Penanganan Tugas</h1>
                </div>
                <a href="tugas.php" class="btn btn-sm btn-secondary">
                    <i data-lucide="arrow-left" class="lucide-sm"></i> Kembali
                </a>
            </div>

            <div class="admin-content">
                <div id="alert-container"></div>

                <div class="row g-3">
                    
                    <!-- INFORMASI LAPORAN -->
                    <div class="col-lg-7">
                        <div class="card-custom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="font-monospace fw-bold text-primary" style="font-size:1.1rem;">
                                        <?php echo htmlspecialchars($tugas['kode_laporan']); ?>
                                    </span>
                                    <span class="badge-status <?php echo ($tugas['status'] === 'Selesai') ? 'badge-status-selesai' : 'badge-status-proses'; ?>">
                                        <?php echo htmlspecialchars($tugas['status']); ?>
                                    </span>
                                </div>
                                <span class="badge-priority <?php echo ($tugas['prioritas'] === 'Tinggi') ? 'badge-priority-tinggi' : 'badge-priority-sedang'; ?>">
                                    Prioritas <?php echo htmlspecialchars($tugas['prioritas']); ?>
                                </span>
                            </div>

                            <h2 style="font-size:1.2rem; margin-bottom:0.6rem;">
                                <?php echo $tugas['ikon'] . ' ' . htmlspecialchars($tugas['judul']); ?>
                            </h2>
                            <p style="font-size:0.9rem; color:var(--color-text-muted); margin-bottom:1.25rem; line-height:1.55;">
                                <?php echo nl2br(htmlspecialchars($tugas['deskripsi'])); ?>
                            </p>

                            <div style="font-size:0.85rem; display:flex; flex-direction:column; gap:0.4rem; padding-top:0.75rem; border-top:1px solid var(--color-border);">
                                <div><strong class="text-dark">Pelapor:</strong> <?php echo htmlspecialchars($tugas['nama_pelapor']); ?> (<?php echo htmlspecialchars($tugas['kontak_pelapor'] ?: '-'); ?>)</div>
                                <div><strong class="text-dark">Lokasi:</strong> <?php echo htmlspecialchars($tugas['alamat'] ?: 'Kota Padang'); ?></div>
                            </div>
                        </div>

                        <!-- Foto Awal -->
                        <div class="card-custom">
                            <div class="card-header-clean">
                                <h6><i data-lucide="image" class="lucide-sm text-primary"></i> Foto Laporan Awal Masyarakat</h6>
                            </div>
                            <?php if (!empty($foto_laporan)): ?>
                            <div class="foto-grid-petugas">
                                <?php foreach ($foto_laporan as $f): ?>
                                    <?php if ($f['jenis'] === 'bukti_laporan'): ?>
                                    <img src="../uploads/laporan/<?php echo htmlspecialchars($f['nama_file']); ?>" 
                                         alt="Bukti Laporan" onclick="window.open(this.src, '_blank')">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted m-0" style="font-size:0.85rem;">Tidak ada foto bukti</p>
                            <?php endif; ?>
                        </div>

                        <!-- Peta -->
                        <div class="card-custom">
                            <div class="card-header-clean">
                                <h6><i data-lucide="map-pin" class="lucide-sm text-primary"></i> Panduan Rute & Titik Lokasi</h6>
                            </div>
                            <div id="map-tugas"></div>
                            <?php if ($tugas['latitude'] && $tugas['longitude']): ?>
                            <div class="mt-2 text-end">
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $tugas['latitude']; ?>,<?php echo $tugas['longitude']; ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3" style="font-size:0.8rem;">
                                    <i data-lucide="navigation" class="lucide-sm"></i> Navigasi Google Maps
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- FORM UPDATE STATUS PENANGANAN -->
                    <div class="col-lg-5">
                        <div class="card-custom">
                            <div class="card-header-clean">
                                <h6><i data-lucide="check-circle" class="lucide-sm text-primary"></i> Update Status Penanganan</h6>
                            </div>

                            <form id="form-update-penanganan" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="penanganan_id" value="<?php echo $penanganan_id; ?>">
                                <input type="hidden" name="laporan_id" value="<?php echo $laporan_id; ?>">

                                <div class="form-group-clean">
                                    <label class="form-label-clean" for="status">
                                        <span>Status Pengerjaan</span>
                                        <span class="required-star">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-select form-select-sm" required>
                                        <option value="Menunggu" <?php echo ($tugas['status'] === 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                        <option value="Dalam Proses" <?php echo ($tugas['status'] === 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses Penanganan</option>
                                        <option value="Selesai" <?php echo ($tugas['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai Ditangani (Tuntas)</option>
                                    </select>
                                </div>

                                <div class="form-group-clean">
                                    <label class="form-label-clean" for="keterangan">
                                        <span>Catatan / Keterangan Penanganan</span>
                                    </label>
                                    <textarea name="keterangan" id="keterangan" class="form-control form-control-sm" rows="4" 
                                              placeholder="Tuliskan tindakan yang telah dilakukan (misal: pengangkutan 2 truk sampah, pembersihan sedimen parit)..."><?php echo htmlspecialchars($tugas['keterangan'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group-clean">
                                    <label class="form-label-clean" for="foto">
                                        <span>Upload Foto Bukti Hasil Pengerjaan</span>
                                    </label>
                                    <input type="file" name="foto" id="foto" class="form-control form-control-sm" accept="image/*">
                                    <span class="form-hint-clean">Lampirkan foto lokasi setelah ditangani (Maks. 5MB)</span>

                                    <?php if ($tugas['foto']): ?>
                                    <div class="mt-2">
                                        <div style="font-size:0.75rem; color:var(--color-text-muted); margin-bottom:4px;">Foto Bukti Tersimpan:</div>
                                        <img src="../uploads/penanganan/<?php echo htmlspecialchars($tugas['foto']); ?>" 
                                             alt="Bukti Selesai" style="max-height:100px; border-radius:var(--radius-sm); border:1px solid var(--color-border); cursor:pointer;"
                                             onclick="window.open(this.src, '_blank')">
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-2" id="btn-submit-update">
                                    <i data-lucide="save" class="lucide-sm"></i> Simpan Pembaharuan
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
        window.addEventListener('load', () => {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
            <?php if ($tugas['latitude'] && $tugas['longitude']): ?>
            const lat = <?php echo (float)$tugas['latitude']; ?>;
            const lng = <?php echo (float)$tugas['longitude']; ?>;

            const map = L.map('map-tugas').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const pin = L.divIcon({
                html: `<div class="custom-pin" style="background-color:#15803D; width:26px; height:26px;">
                         <i data-lucide="map-pin" style="width:14px; height:14px;"></i>
                       </div>`,
                className: '',
                iconSize: [26, 26],
                iconAnchor: [13, 26]
            });
            L.marker([lat, lng], { icon: pin }).addTo(map);
            initLucideIcons();
            <?php else: ?>
            document.getElementById('map-tugas').innerHTML = '<div class="text-center py-4 text-muted" style="font-size:0.85rem;">Koordinat titik lokasi tidak tersedia</div>';
            <?php endif; ?>
        });

        document.getElementById('form-update-penanganan').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('btn-submit-update');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            const formData = new FormData(this);

            fetch('../proses/update-penanganan.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Gagal memperbarui progres', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = '<i data-lucide="save" class="lucide-sm"></i> Simpan Pembaharuan';
                    initLucideIcons();
                }
            })
            .catch(err => {
                showToast('Terjadi kesalahan: ' + err.message, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" class="lucide-sm"></i> Simpan Pembaharuan';
                initLucideIcons();
            });
        });
    </script>
</body>
</html>
