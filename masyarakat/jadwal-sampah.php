<?php
// =====================================================
// HALAMAN JADWAL ANGKUTAN SAMPAH - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

// Data Jadwal Operasional Armada 11 Kecamatan di Kota Padang
$jadwal_wilayah = [
    [
        'kecamatan' => 'Koto Tangah',
        'wilayah' => 'Padang Bagian Utara',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '05.30 - 09.30 WIB & 16.00 - 18.00 WIB',
        'armada' => '6 Unit Dump Truck & 2 Arm Roll',
        'posko' => 'Posko DLH Lubuk Buaya',
        'tps_utama' => 'TPS Lubuk Buaya, TPS Tabing, TPS Pasie Nan Tigo',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Padang Barat',
        'wilayah' => 'Pusat Kota & Kawasan Wisata Pantai',
        'jadwal_rutin' => 'Setiap Hari (2x Sehari)',
        'jam_angkut' => '05.00 - 08.30 WIB & 16.30 - 20.00 WIB',
        'armada' => '5 Unit Compactor Truck & 4 Becak Motor',
        'posko' => 'Posko DLH Padang Barat (Purus)',
        'tps_utama' => 'TPS Purus, TPS Olo Ladang, TPS Pasar Raya Padang',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Padang Timur',
        'wilayah' => 'Kawasan Perdagangan & Pemukiman',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 10.00 WIB & 16.00 - 18.30 WIB',
        'armada' => '4 Unit Dump Truck & 3 Arm Roll',
        'posko' => 'Posko DLH Sawahan',
        'tps_utama' => 'TPS Sawahan, TPS Jati, TPS Simpang Haru',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Padang Utara',
        'wilayah' => 'Kawasan Pendidikan & Perkantoran',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 09.30 WIB',
        'armada' => '4 Unit Dump Truck',
        'posko' => 'Posko DLH Ulak Karang',
        'tps_utama' => 'TPS Ulak Karang, TPS Air Tawar, TPS Lolong Belanti',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Padang Selatan',
        'wilayah' => 'Kawasan Pelabuhan & Bukit Lampu',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 10.00 WIB',
        'armada' => '4 Unit Dump Truck & 2 Arm Roll',
        'posko' => 'Posko DLH Seberang Padang',
        'tps_utama' => 'TPS Seberang Padang, TPS Rawang, TPS Batang Arau',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Kuranji',
        'wilayah' => 'Pemukiman Padat Penduduk',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 11.00 WIB & 16.00 - 18.30 WIB',
        'armada' => '7 Unit Dump Truck & 3 Arm Roll',
        'posko' => 'Posko DLH Belimbing & Pasar Ambacang',
        'tps_utama' => 'TPS Belimbing, TPS Korong Gadang, TPS Gunung Sarik',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Nanggalo',
        'wilayah' => 'Kawasan Pemukiman & Kampus',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 10.00 WIB',
        'armada' => '4 Unit Dump Truck',
        'posko' => 'Posko DLH Siteba',
        'tps_utama' => 'TPS Siteba, TPS Surau Gadang, TPS Kurao Pagang',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Pauh',
        'wilayah' => 'Kawasan Kampus Unand & Limau Manis',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.30 - 10.30 WIB',
        'armada' => '4 Unit Dump Truck & 2 Arm Roll',
        'posko' => 'Posko DLH Pasar Baru Pauh',
        'tps_utama' => 'TPS Pasar Baru Pauh, TPS Limau Manis, TPS Cupak Tangah',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Lubuk Begalung',
        'wilayah' => 'Kawasan Industri & Perumahan',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 11.00 WIB',
        'armada' => '5 Unit Dump Truck & 2 Arm Roll',
        'posko' => 'Posko DLH Pegambiran',
        'tps_utama' => 'TPS Pegambiran, TPS Banuaran, TPS Tanjung Aur',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Lubuk Kilangan',
        'wilayah' => 'Kawasan Bandar Buat & Indarung',
        'jadwal_rutin' => 'Setiap Hari (Senin - Minggu)',
        'jam_angkut' => '06.00 - 10.30 WIB',
        'armada' => '4 Unit Dump Truck',
        'posko' => 'Posko DLH Bandar Buat',
        'tps_utama' => 'TPS Bandar Buat, TPS Indarung, TPS Batu Gadang',
        'status' => 'Aktif Beroperasi'
    ],
    [
        'kecamatan' => 'Bungus Teluk Kabung',
        'wilayah' => 'Kawasan Pesisir Selatan Padang',
        'jadwal_rutin' => 'Senin, Rabu, Jumat, Minggu',
        'jam_angkut' => '07.00 - 11.30 WIB',
        'armada' => '3 Unit Dump Truck',
        'posko' => 'Posko DLH Pasar Labuhan',
        'tps_utama' => 'TPS Teluk Kabung, TPS Pasar Labuhan, TPS Bungus Barat',
        'status' => 'Aktif Beroperasi'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Angkutan Sampah - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .page-hero {
            background: linear-gradient(135deg, #0D5C3A 0%, #15803D 100%);
            color: #FFFFFF;
            padding: 2.5rem 0 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .page-hero h1 {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
        }

        .page-hero p {
            font-size: 0.92rem;
            color: #E2E8F0;
            max-width: 650px;
            margin: 0;
        }

        .rule-box {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            border: 1px solid #F59E0B;
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .rule-box-icon {
            width: 48px;
            height: 48px;
            background: #D97706;
            color: #FFFFFF;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rule-box-content h4 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #78350F;
            margin-bottom: 0.2rem;
        }

        .rule-box-content p {
            font-size: 0.85rem;
            color: #92400E;
            margin: 0;
        }

        .schedule-table-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-xs);
        }

        .schedule-table-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        .schedule-table-card th {
            background-color: var(--color-surface-subtle);
            padding: 0.85rem 1rem;
            font-weight: 700;
            color: var(--color-text-main);
            border-bottom: 1px solid var(--color-border);
            text-align: left;
        }

        .schedule-table-card td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--color-border);
            vertical-align: middle;
        }

        .schedule-table-card tr:last-child td {
            border-bottom: none;
        }

        .schedule-table-card tr:hover td {
            background-color: #F8FAFC;
        }

        .search-schedule-box {
            position: relative;
            max-width: 320px;
        }

        .search-schedule-box input {
            padding-left: 2.25rem;
            font-size: 0.88rem;
        }

        .search-schedule-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-subtle);
            pointer-events: none;
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
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i data-lucide="home" class="lucide-sm"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peta.php">
                            <i data-lucide="map" class="lucide-sm"></i> Peta Permasalahan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="jadwal-sampah.php">
                            <i data-lucide="truck" class="lucide-sm"></i> Jadwal Sampah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="edukasi.php">
                            <i data-lucide="book-open" class="lucide-sm"></i> Edukasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="alur.php">
                            <i data-lucide="git-merge" class="lucide-sm"></i> Alur Pelaporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tracking.php">
                            <i data-lucide="search" class="lucide-sm"></i> Tracking
                        </a>
                    </li>
                    <?php if (is_logged_in()): ?>
                        <?php
                        $role = get_user_role();
                        $dash_link = ($role === 'admin') ? '../admin/index.php' : (($role === 'petugas') ? '../petugas/index.php' : 'profil.php');
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $dash_link; ?>">
                                <i data-lucide="user" class="lucide-sm"></i> <?php echo htmlspecialchars(get_user_nama()); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/logout.php" style="color:var(--color-danger) !important;">
                                <i data-lucide="log-out" class="lucide-sm"></i> Keluar
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/login.php">
                                <i data-lucide="log-in" class="lucide-sm"></i> Masuk
                            </a>
                        </li>
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

    <!-- ===== HEADER HERO ===== -->
    <header class="page-hero">
        <div class="container">
            <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.82rem; font-weight:700; color:#86EFAC;">
                <i data-lucide="truck" class="lucide-sm"></i> DINAS LINGKUNGAN HIDUP KOTA PADANG
            </div>
            <h1>Jadwal Operasional Angkutan Sampah</h1>
            <p>Informasi resmi rute ritase truk pengangkut sampah, waktu operasional, dan lokasi TPS resmi di 11 Kecamatan Kota Padang.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="py-5">
        <div class="container">

            <!-- ATURAN PERDA JAM BUANG SAMPAH -->
            <div class="rule-box">
                <div class="rule-box-icon">
                    <i data-lucide="clock" style="width:26px; height:26px;"></i>
                </div>
                <div class="rule-box-content">
                    <h4>Jam Resmi Membuang Sampah ke TPS: Pukul 18.00 - 05.00 WIB</h4>
                    <p>
                        Sesuai <strong>Peraturan Daerah Kota Padang No. 21 Tahun 2012</strong>, warga dilarang membuang sampah ke TPS pada siang hari (pukul 05.00 - 18.00 WIB) guna menjaga kebersihan dan kenyamanan kota.
                    </p>
                </div>
            </div>

            <!-- JADWAL ANGKUTAN KECAMATAN -->
            <div class="card-custom p-0 overflow-hidden mb-5">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 style="font-size:1.15rem; font-weight:800; margin:0; color:var(--color-text-main);">
                            Jadwal Truk Armada 11 Kecamatan
                        </h2>
                        <span class="text-muted" style="font-size:0.82rem;">Waktu armada mengangkut sampah dari TPS menuju TPA Aie Dingin</span>
                    </div>

                    <div class="search-schedule-box">
                        <i data-lucide="search" class="lucide-sm"></i>
                        <input type="text" id="filter-kecamatan" class="form-control form-control-sm" placeholder="Cari kecamatan / TPS...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-clean m-0" id="schedule-table">
                        <thead>
                            <tr>
                                <th>Kecamatan & Wilayah</th>
                                <th>Hari Pengangkutan</th>
                                <th>Jam Operasional Armada</th>
                                <th>Armada Ditugaskan</th>
                                <th>TPS & Kontainer Utama</th>
                                <th>Status Operasional</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwal_wilayah as $jdw): ?>
                                <tr class="schedule-row">
                                    <td>
                                        <strong style="color:var(--color-text-main); font-size:0.92rem;"><?php echo htmlspecialchars($jdw['kecamatan']); ?></strong>
                                        <div style="font-size:0.78rem; color:var(--color-text-muted);"><?php echo htmlspecialchars($jdw['wilayah']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-weight:600; font-size:0.85rem;"><?php echo htmlspecialchars($jdw['jadwal_rutin']); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-primary fw-semibold" style="font-size:0.85rem;">
                                            <i data-lucide="clock" class="lucide-xs"></i>
                                            <?php echo htmlspecialchars($jdw['jam_angkut']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size:0.83rem; color:var(--color-text-muted);"><?php echo htmlspecialchars($jdw['armada']); ?></span>
                                        <div style="font-size:0.75rem; color:var(--color-text-subtle);"><?php echo htmlspecialchars($jdw['posko']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size:0.83rem; color:var(--color-text-main);"><?php echo htmlspecialchars($jdw['tps_utama']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-status-selesai"><?php echo htmlspecialchars($jdw['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- INFORMASI TPA AIE DINGIN -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card-custom h-100 p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="brand-badge" style="background:#E0F2FE; color:#0284C7;">
                                <i data-lucide="map-pin"></i>
                            </span>
                            <div>
                                <h3 style="font-size:1.1rem; font-weight:800; margin:0;">TPA Sampah Regional Aie Dingin</h3>
                                <span class="text-muted" style="font-size:0.8rem;">Pusat Pemrosesan Akhir Sampah Kota Padang</span>
                            </div>
                        </div>
                        <p style="font-size:0.85rem; color:var(--color-text-muted); line-height:1.6; margin-bottom:1rem;">
                            Tempat Pemrosesan Akhir (TPA) Aie Dingin berlokasi di Kelurahan Balai Gadang, Kecamatan Koto Tangah, Kota Padang. Seluruh sampah dari 11 kecamatan diproses dengan sistem sanitary landfill ramah lingkungan.
                        </p>
                        <div class="d-flex flex-column gap-2" style="font-size:0.82rem;">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="check-circle" class="text-success lucide-xs"></i>
                                <span>Kapasitas Harian: &plusmn; 450 - 500 Ton Sampah/Hari</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="check-circle" class="text-success lucide-xs"></i>
                                <span>Fasilitas: Unit Pemilahan, Penimbangan Otomatis & Pengolahan Gas Metana</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-custom h-100 p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="brand-badge" style="background:#FEF3C7; color:#D97706;">
                                <i data-lucide="alert-octagon"></i>
                            </span>
                            <div>
                                <h3 style="font-size:1.1rem; font-weight:800; margin:0;">Sampah Belum Terangkut?</h3>
                                <span class="text-muted" style="font-size:0.8rem;">Laporkan langsung agar armada cadangan diterjunkan</span>
                            </div>
                        </div>
                        <p style="font-size:0.85rem; color:var(--color-text-muted); line-height:1.6; margin-bottom:1rem;">
                            Jika kontainer di TPS lingkungan Anda telah melebihi kapasitas atau ada tumpukan sampah liar di jalan umum yang belum diangkut, segera buat laporan pengaduan melalui SIGALING PADANG.
                        </p>
                        <a href="buat-laporan.php?kategori=1" class="btn btn-primary w-100">
                            <i data-lucide="send" class="lucide-sm"></i> Laporkan Tumpukan Sampah
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="footer-sigaling">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-5">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="brand-badge" style="background:#1E293B; color:#4ADE80;">
                            <i data-lucide="leaf"></i>
                        </span>
                        <h5 class="m-0">SIGALING PADANG</h5>
                    </div>
                    <p style="font-size:0.85rem; color:#94A3B8;">
                        Sistem Informasi Pelaporan dan Pemetaan Permasalahan Lingkungan Hidup Kota Padang, Sumatera Barat.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <h6>Menu Layanan</h6>
                    <ul>
                        <li><a href="../index.php">Beranda</a></li>
                        <li><a href="buat-laporan.php">Buat Laporan</a></li>
                        <li><a href="peta.php">Peta Permasalahan</a></li>
                        <li><a href="jadwal-sampah.php">Jadwal Sampah</a></li>
                        <li><a href="edukasi.php">Edukasi Lingkungan</a></li>
                        <li><a href="tracking.php">Tracking Status</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <h6>Kontak & Informasi</h6>
                    <p style="font-size:0.85rem; margin-bottom:0.25rem;">Dinas Lingkungan Hidup Kota Padang</p>
                    <p style="font-size:0.85rem; color:#64748B;">Jl. Bagindo Aziz Chan No. 1, Aie Pacah, Kota Padang</p>
                    <p style="font-size:0.85rem; color:#64748B;">Email: dlh@padang.go.id &bull; Call Center: 112</p>
                </div>
            </div>

            <div class="footer-bottom-bar">
                &copy; 2026 Pemerintah Kota Padang &bull; SIGALING PADANG. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        lucide.createIcons();

        // Search Filter for Schedule Table
        document.getElementById('filter-kecamatan').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.schedule-row');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    </script>
</body>

</html>