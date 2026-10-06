<?php
// =====================================================
// HALAMAN TRACKING LAPORAN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

// 1. Ambil Kode Laporan dari URL
$kode_dicari = isset($_GET['kode']) ? trim($_GET['kode']) : '';

$laporan = null;
$foto_list = [];
$opd_info = null;
$penanganan_list = [];
$error_message = '';

if (!empty($kode_dicari)) {
    $query = "SELECT l.*, k.nama_kategori, k.ikon 
              FROM laporan l 
              LEFT JOIN kategori k ON l.kategori_id = k.id 
              WHERE l.kode_laporan = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $kode_dicari);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $laporan = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        $laporan_id = (int)$laporan['id'];
        
        // Foto Laporan
        $query_foto = "SELECT nama_file, jenis FROM foto_laporan WHERE laporan_id = ? ORDER BY created_at ASC";
        $stmt_foto = mysqli_prepare($koneksi, $query_foto);
        mysqli_stmt_bind_param($stmt_foto, "i", $laporan_id);
        mysqli_stmt_execute($stmt_foto);
        $result_foto = mysqli_stmt_get_result($stmt_foto);
        while ($row = mysqli_fetch_assoc($result_foto)) {
            $foto_list[] = $row;
        }
        mysqli_stmt_close($stmt_foto);
        
        // Info OPD
        $query_opd = "SELECT lo.*, o.nama_opd, o.kontak, u.nama as nama_petugas 
                      FROM laporan_opd lo
                      LEFT JOIN opd o ON lo.opd_id = o.id
                      LEFT JOIN users u ON lo.petugas_id = u.id
                      WHERE lo.laporan_id = ?
                      ORDER BY lo.tanggal_diteruskan DESC LIMIT 1";
        $stmt_opd = mysqli_prepare($koneksi, $query_opd);
        mysqli_stmt_bind_param($stmt_opd, "i", $laporan_id);
        mysqli_stmt_execute($stmt_opd);
        $result_opd = mysqli_stmt_get_result($stmt_opd);
        if (mysqli_num_rows($result_opd) > 0) {
            $opd_info = mysqli_fetch_assoc($result_opd);
        }
        mysqli_stmt_close($stmt_opd);
        
        // Info Penanganan
        $query_penanganan = "SELECT p.*, u.nama as nama_petugas 
                             FROM penanganan p
                             LEFT JOIN users u ON p.petugas_id = u.id
                             WHERE p.laporan_id = ?
                             ORDER BY p.tanggal_penanganan ASC";
        $stmt_penanganan = mysqli_prepare($koneksi, $query_penanganan);
        mysqli_stmt_bind_param($stmt_penanganan, "i", $laporan_id);
        mysqli_stmt_execute($stmt_penanganan);
        $result_penanganan = mysqli_stmt_get_result($stmt_penanganan);
        while ($row = mysqli_fetch_assoc($result_penanganan)) {
            $penanganan_list[] = $row;
        }
        mysqli_stmt_close($stmt_penanganan);
        
    } else {
        $error_message = 'Kode laporan "' . htmlspecialchars($kode_dicari) . '" tidak ditemukan. Mohon pastikan kode sudah sesuai.';
        mysqli_stmt_close($stmt);
    }
}

// Progress Step
function getTimelineStep($status) {
    $steps = [
        'Menunggu Verifikasi' => 1,
        'Terverifikasi' => 2,
        'Dalam Penanganan' => 3,
        'Selesai' => 4,
        'Ditolak' => -1
    ];
    return $steps[$status] ?? 0;
}

$current_step = $laporan ? getTimelineStep($laporan['status']) : 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Laporan - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .search-banner {
            background-color: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            padding: 2rem 0;
            margin-bottom: 1.5rem;
        }

        .search-form-clean {
            display: flex;
            max-width: 520px;
            margin: 0 auto;
            gap: 0.5rem;
        }

        .search-form-clean input {
            flex: 1;
            font-size: 0.95rem;
            font-family: monospace;
            letter-spacing: 0.03em;
        }

        .photo-gallery-clean {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .photo-gallery-clean img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .photo-gallery-clean img:hover {
            transform: scale(1.03);
        }

        .penanganan-box-item {
            border-left: 3px solid var(--color-primary);
            background-color: var(--color-surface-subtle);
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            padding: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .penanganan-box-item:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 575.98px) {
            .search-form-clean {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-sigaling">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <span class="brand-badge">
                    <i data-lucide="leaf"></i>
                </span>
                <span>SIGALING PADANG</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i data-lucide="menu"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="../index.php"><i data-lucide="home" class="lucide-sm"></i> Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="peta.php"><i data-lucide="map" class="lucide-sm"></i> Peta Permasalahan</a></li>
                    <li class="nav-item"><a class="nav-link" href="jadwal-sampah.php"><i data-lucide="truck" class="lucide-sm"></i> Jadwal Sampah</a></li>
                    <li class="nav-item"><a class="nav-link" href="edukasi.php"><i data-lucide="book-open" class="lucide-sm"></i> Edukasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="alur.php"><i data-lucide="git-merge" class="lucide-sm"></i> Alur Pelaporan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="tracking.php"><i data-lucide="search" class="lucide-sm"></i> Tracking</a></li>
                    <?php if (is_logged_in()): ?>
                        <?php 
                        $role = get_user_role();
                        $dash_link = ($role === 'admin') ? '../admin/index.php' : (($role === 'petugas') ? '../petugas/index.php' : 'profil.php');
                        ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $dash_link; ?>"><i data-lucide="user" class="lucide-sm"></i> <?php echo htmlspecialchars(get_user_nama()); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php" style="color:var(--color-danger) !important;"><i data-lucide="log-out" class="lucide-sm"></i> Keluar</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../auth/login.php"><i data-lucide="log-in" class="lucide-sm"></i> Masuk</a></li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="buat-laporan.php" class="btn btn-primary btn-sm w-100">
                            <i data-lucide="send" class="lucide-sm"></i> Lapor Sekarang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ===== SEARCH HEADER ===== -->
    <div class="search-banner text-center">
        <div class="container">
            <h1 style="font-size:1.55rem; font-weight:800; color:var(--color-slate-900); margin-bottom:0.35rem;">
                Lacak Status Pengaduan
            </h1>
            <p class="text-muted" style="font-size:0.88rem; max-width:520px; margin:0 auto 1.25rem;">
                Masukkan Kode Tiket Laporan Anda untuk melihat tahapan verifikasi dan penanganan di lapangan.
            </p>

            <form method="GET" action="" class="search-form-clean">
                <input type="text" name="kode" class="form-control text-uppercase" placeholder="Contoh: SIGA-20260824-001" 
                       value="<?php echo htmlspecialchars($kode_dicari); ?>" required>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="search" class="lucide-sm"></i> Lacak
                </button>
            </form>
        </div>
    </div>

    <!-- ===== RESULT CONTAINER ===== -->
    <main>
        <div class="container pb-5" style="max-width: 860px;">

            <?php if (!empty($kode_dicari) && !$laporan): ?>
                <!-- Not Found Box -->
                <div class="card-custom text-center py-5">
                    <div style="width:48px; height:48px; border-radius:50%; background:var(--color-danger-bg); color:var(--color-danger); display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                        <i data-lucide="alert-circle" style="width:22px; height:22px;"></i>
                    </div>
                    <h3 style="font-size:1.15rem; margin-bottom:0.4rem;">Laporan Tidak Ditemukan</h3>
                    <p class="text-muted" style="max-width:420px; margin:0 auto; font-size:0.88rem;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </p>
                </div>

            <?php elseif ($laporan): ?>
                <!-- Header Laporan -->
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="font-monospace fw-bold text-primary" style="font-size:1.05rem;">
                                <?php echo htmlspecialchars($laporan['kode_laporan']); ?>
                            </span>
                            <button type="button" class="btn btn-sm btn-ghost p-1" data-copy="<?php echo htmlspecialchars($laporan['kode_laporan']); ?>" title="Salin Kode Laporan">
                                <i data-lucide="copy" style="width:14px; height:14px;"></i>
                            </button>
                            <span class="badge-status <?php 
                                echo ($laporan['status'] === 'Terverifikasi') ? 'badge-status-terverifikasi' : 
                                    (($laporan['status'] === 'Dalam Penanganan') ? 'badge-status-proses' : 
                                    (($laporan['status'] === 'Selesai') ? 'badge-status-selesai' : 
                                    (($laporan['status'] === 'Ditolak') ? 'badge-status-ditolak' : 'badge-status-menunggu'))); 
                            ?>">
                                <?php echo htmlspecialchars($laporan['status']); ?>
                            </span>
                        </div>
                        <span style="font-size:0.8rem; color:var(--color-slate-500);">
                            <i data-lucide="calendar" class="lucide-sm me-1"></i>
                            <?php echo date('d M Y, H:i', strtotime($laporan['tanggal_laporan'])); ?> WIB
                        </span>
                    </div>

                    <h2 style="font-size:1.25rem; margin-bottom:0.5rem; color:var(--color-slate-900);"><?php echo htmlspecialchars($laporan['judul']); ?></h2>
                    <p style="font-size:0.9rem; color:var(--color-slate-600); margin-bottom:1rem; line-height:1.5;">
                        <?php echo nl2br(htmlspecialchars($laporan['deskripsi'])); ?>
                    </p>

                    <div class="d-flex flex-wrap gap-3 pt-2 border-top" style="font-size:0.82rem; color:var(--color-slate-600);">
                        <div>
                            <strong class="text-dark"><i data-lucide="tag" class="lucide-sm me-1"></i>Kategori:</strong>
                            <span><?php echo htmlspecialchars($laporan['nama_kategori'] ?: '-'); ?></span>
                        </div>
                        <div>
                            <strong class="text-dark"><i data-lucide="map-pin" class="lucide-sm me-1"></i>Wilayah:</strong>
                            <span><?php echo htmlspecialchars($laporan['kecamatan'] ?: $laporan['alamat'] ?: '-'); ?></span>
                        </div>
                        <div>
                            <strong class="text-dark"><i data-lucide="user" class="lucide-sm me-1"></i>Pelapor:</strong>
                            <span><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Timeline Progres -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="git-commit" class="lucide-sm text-primary"></i> Status & Alur Penanganan</h6>
                    </div>

                    <?php if ($laporan['status'] === 'Ditolak'): ?>
                        <div class="alert-clean alert-clean-danger m-0">
                            <i data-lucide="x-circle" class="lucide-sm"></i>
                            <div>
                                <strong>Laporan Ditolak</strong><br>
                                Mohon maaf, laporan ini tidak dapat diproses lebih lanjut setelah proses verifikasi oleh tim penanganan.
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="tracking-timeline">
                            <div class="timeline-track-v">
                                <!-- Node 1 -->
                                <div class="timeline-node <?php echo ($current_step >= 1) ? ($current_step == 1 ? 'current' : 'completed') : ''; ?>">
                                    <div class="timeline-dot"><i data-lucide="file-text" class="lucide-sm"></i></div>
                                    <div class="timeline-content-box">
                                        <div class="title">1. Laporan Masuk</div>
                                        <p class="desc">Laporan telah diterima sistem dan masuk antrean verifikasi.</p>
                                    </div>
                                </div>

                                <!-- Node 2 -->
                                <div class="timeline-node <?php echo ($current_step >= 2) ? ($current_step == 2 ? 'current' : 'completed') : ''; ?>">
                                    <div class="timeline-dot"><i data-lucide="check" class="lucide-sm"></i></div>
                                    <div class="timeline-content-box">
                                        <div class="title">2. Terverifikasi</div>
                                        <p class="desc">Laporan telah divalidasi dan diteruskan ke tim penanganan terkait.</p>
                                    </div>
                                </div>

                                <!-- Node 3 -->
                                <div class="timeline-node <?php echo ($current_step >= 3) ? ($current_step == 3 ? 'current' : 'completed') : ''; ?>">
                                    <div class="timeline-dot"><i data-lucide="wrench" class="lucide-sm"></i></div>
                                    <div class="timeline-content-box">
                                        <div class="title">3. Dalam Penanganan</div>
                                        <p class="desc">Petugas lapangan sedang memproses penanganan permasalahan.</p>
                                    </div>
                                </div>

                                <!-- Node 4 -->
                                <div class="timeline-node <?php echo ($current_step >= 4) ? 'completed' : ''; ?>">
                                    <div class="timeline-dot"><i data-lucide="check-circle" class="lucide-sm"></i></div>
                                    <div class="timeline-content-box">
                                        <div class="title">4. Selesai Ditangani</div>
                                        <p class="desc">Permasalahan tuntas dikerjakan dan diverifikasi selesai.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info OPD & Penanganan -->
                <div class="row g-3">
                    <?php if ($opd_info): ?>
                    <div class="col-md-6">
                        <div class="card-custom h-100 mb-0">
                            <div class="card-header-clean">
                                <h6><i data-lucide="building" class="lucide-sm text-primary"></i> Instansi Penanggung Jawab</h6>
                            </div>
                            <div style="font-size:0.88rem;">
                                <div class="fw-bold mb-1"><?php echo htmlspecialchars($opd_info['nama_opd']); ?></div>
                                <?php if ($opd_info['nama_petugas']): ?>
                                    <div class="text-muted mb-1"><i data-lucide="user-check" class="lucide-sm me-1"></i>Petugas: <?php echo htmlspecialchars($opd_info['nama_petugas']); ?></div>
                                <?php endif; ?>
                                <div class="text-muted" style="font-size:0.78rem;">
                                    Diteruskan: <?php echo date('d M Y, H:i', strtotime($opd_info['tanggal_diteruskan'])); ?> WIB
                                </div>
                                <?php if ($opd_info['catatan']): ?>
                                    <div class="mt-2 p-2 rounded bg-light" style="font-size:0.82rem; font-style:italic;">
                                        "<?php echo htmlspecialchars($opd_info['catatan']); ?>"
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($penanganan_list)): ?>
                    <div class="col-md-6">
                        <div class="card-custom h-100 mb-0">
                            <div class="card-header-clean">
                                <h6><i data-lucide="list-checks" class="lucide-sm text-primary"></i> Log Penanganan Petugas</h6>
                            </div>
                            <?php foreach ($penanganan_list as $p): ?>
                            <div class="penanganan-box-item">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong style="font-size:0.85rem;"><?php echo htmlspecialchars($p['status']); ?></strong>
                                    <span style="font-size:0.72rem; color:var(--color-slate-500);"><?php echo date('d M, H:i', strtotime($p['tanggal_penanganan'])); ?></span>
                                </div>
                                <?php if ($p['keterangan']): ?>
                                    <p style="font-size:0.82rem; margin-bottom:0.25rem;"><?php echo htmlspecialchars($p['keterangan']); ?></p>
                                <?php endif; ?>
                                <?php if ($p['nama_petugas']): ?>
                                    <small class="text-muted d-block" style="font-size:0.75rem;">Oleh: <?php echo htmlspecialchars($p['nama_petugas']); ?></small>
                                <?php endif; ?>
                                <?php if ($p['foto']): ?>
                                    <div class="mt-2">
                                        <img src="../uploads/penanganan/<?php echo htmlspecialchars($p['foto']); ?>" 
                                             alt="Bukti Penanganan" style="max-height:80px; border-radius:var(--radius-sm); cursor:pointer;"
                                             onclick="window.open(this.src, '_blank')">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Foto Bukti Awal & Lokasi Peta -->
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <div class="card-custom h-100 mb-0">
                            <div class="card-header-clean">
                                <h6><i data-lucide="image" class="lucide-sm text-primary"></i> Foto Bukti Laporan</h6>
                            </div>
                            <?php if (!empty($foto_list)): ?>
                            <div class="photo-gallery-clean">
                                <?php foreach ($foto_list as $foto): ?>
                                    <?php if ($foto['jenis'] === 'bukti_laporan'): ?>
                                    <img src="../uploads/laporan/<?php echo htmlspecialchars($foto['nama_file']); ?>" 
                                         alt="Bukti Laporan" onclick="window.open(this.src, '_blank')">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                                <p class="text-muted" style="font-size:0.85rem;">Tidak ada foto bukti terlampir</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card-custom h-100 mb-0">
                            <div class="card-header-clean">
                                <h6><i data-lucide="map" class="lucide-sm text-primary"></i> Koordinat Lokasi</h6>
                            </div>
                            <?php if ($laporan['latitude'] && $laporan['longitude']): ?>
                            <div style="font-size:0.85rem;">
                                <p class="text-muted mb-2">
                                    <?php echo htmlspecialchars($laporan['alamat'] ?: 'Lat: ' . $laporan['latitude'] . ', Long: ' . $laporan['longitude']); ?>
                                </p>
                                <a href="https://www.google.com/maps?q=<?php echo $laporan['latitude']; ?>,<?php echo $laporan['longitude']; ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i data-lucide="external-link" class="lucide-sm"></i> Buka di Google Maps
                                </a>
                            </div>
                            <?php else: ?>
                                <p class="text-muted" style="font-size:0.85rem;">Koordinat lokasi tidak tercatat</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Belum ada pencarian / Default state -->
                <div class="card-custom text-center py-5">
                    <div style="width:48px; height:48px; border-radius:50%; background:var(--color-primary-subtle); color:var(--color-primary-hover); display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                        <i data-lucide="search" style="width:22px; height:22px;"></i>
                    </div>
                    <h3 style="font-size:1.1rem; margin-bottom:0.35rem;">Silakan Masukkan Kode Laporan</h3>
                    <p class="text-muted" style="max-width:380px; margin:0 auto; font-size:0.85rem;">
                        Kode laporan didapatkan sesaat setelah Anda mengirimkan laporan (contoh: <code>SIGA-20260824-001</code>).
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="site-footer">
        <div class="container text-center">
            <p class="m-0">&copy; 2026 <strong>SIGALING PADANG</strong>. Sistem Informasi & Pengaduan Lingkungan Hidup.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
        window.addEventListener('load', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
