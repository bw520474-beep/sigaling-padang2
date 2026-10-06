<?php
// =====================================================
// HALAMAN EDUKASI LINGKUNGAN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edukasi Lingkungan - SIGALING PADANG</title>
    
    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        .page-hero {
            background: linear-gradient(135deg, #0D5C3A 0%, #15803D 100%);
            color: #FFFFFF;
            padding: 2.5rem 0 2rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
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

        .edu-category-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.2s ease;
        }

        .edu-category-card:hover {
            border-color: var(--color-primary);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .edu-icon-badge {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .edu-category-card h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--color-text-main);
            margin-bottom: 0.4rem;
        }

        .edu-category-card p {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            line-height: 1.55;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .edu-list-item {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: var(--color-text-main);
            margin-bottom: 0.4rem;
        }

        .edu-list-item i {
            color: var(--color-primary);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .perda-alert-card {
            background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%);
            border: 1px solid #FCA5A5;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
        }

        .bank-sampah-item {
            padding: 1rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            background: var(--color-surface);
            transition: border-color 0.15s ease;
        }

        .bank-sampah-item:hover {
            border-color: var(--color-primary);
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
                        <a class="nav-link" href="jadwal-sampah.php">
                            <i data-lucide="truck" class="lucide-sm"></i> Jadwal Sampah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="edukasi.php">
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
                <i data-lucide="book-open" class="lucide-sm"></i> PANDUAN MASYARAKAT KOTA PADANG
            </div>
            <h1>Edukasi Lingkungan & Pengelolaan Sampah</h1>
            <p>Tingkatkan kesadaran bersama dalam menjaga kebersihan lingkungan, pilah sampah dari sumbernya, dan cegah banjir di Kota Padang.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="py-5">
        <div class="container">

            <!-- 3 PILAR PEMILAHAN SAMPAH -->
            <div class="mb-5">
                <div class="text-center mb-4">
                    <span class="badge-status badge-status-selesai mb-2">GERAKAN PILAH SAMPAH</span>
                    <h2 style="font-size:1.45rem; font-weight:800; color:var(--color-text-main); margin-bottom:0.35rem;">
                        Pilah Sampah dari Rumah Berdasarkan Jenisnya
                    </h2>
                    <p class="text-muted" style="font-size:0.9rem; max-width:600px; margin:0 auto;">
                        Memilah sampah dari rumah tangga dapat mengurangi hingga 60% timbulan sampah yang masuk ke TPA Aie Dingin.
                    </p>
                </div>

                <div class="row g-4">
                    <!-- Organik -->
                    <div class="col-md-4">
                        <div class="edu-category-card">
                            <div class="edu-icon-badge" style="background:#DCFCE7; color:#166534;">
                                <i data-lucide="apple"></i>
                            </div>
                            <h3>1. Sampah Organik (Mudah Terurai)</h3>
                            <p>
                                Sisa makanan, dedaunan gugur, kulit buah, dan sayuran. Sangat baik diolah menjadi pupuk kompos atau pakan maggot BSF.
                            </p>
                            <div class="border-top pt-3">
                                <div class="edu-list-item">
                                    <i data-lucide="check" class="lucide-xs"></i>
                                    <span>Gunakan wadah tertutup agar tidak menimbulkan bau</span>
                                </div>
                                <div class="edu-list-item">
                                    <i data-lucide="check" class="lucide-xs"></i>
                                    <span>Olah jadi kompos dengan metode biopori di pekarangan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Anorganik -->
                    <div class="col-md-4">
                        <div class="edu-category-card">
                            <div class="edu-icon-badge" style="background:#E0F2FE; color:#0369A1;">
                                <i data-lucide="recycle"></i>
                            </div>
                            <h3>2. Sampah Anorganik (Daur Ulang)</h3>
                            <p>
                                Botol plastik, kardus, kaleng aluminium, botol kaca, dan kertas. Memiliki nilai ekonomis jika disetor ke Bank Sampah Unit.
                            </p>
                            <div class="border-top pt-3">
                                <div class="edu-list-item">
                                    <i data-lucide="check" class="lucide-xs"></i>
                                    <span>Bersihkan dan keringkan sebelum disetor ke Bank Sampah</span>
                                </div>
                                <div class="edu-list-item">
                                    <i data-lucide="check" class="lucide-xs"></i>
                                    <span>Pipihkan kardus dan botol plastik untuk menghemat ruang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B3 / Residu -->
                    <div class="col-md-4">
                        <div class="edu-category-card">
                            <div class="edu-icon-badge" style="background:#FEE2E2; color:#991B1B;">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <h3>3. Sampah B3 & Residu Berbahaya</h3>
                            <p>
                                Baterai bekas, lampu neon, kemasan obat/pestisida, jarum suntik, dan popok sekali pakai. Wajib ditangani secara khusus.
                            </p>
                            <div class="border-top pt-3">
                                <div class="edu-list-item">
                                    <i data-lucide="check" class="lucide-xs"></i>
                                    <span>Pisahkan dalam kantong khusus bertanda B3</span>
                                </div>
                                <div class="edu-list-item">
                                    <i data-lucide="check" class="lucide-xs"></i>
                                    <span>Jangan dibakar atau dibuang ke sungai/drainase</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERDA & SANKSI HUKUM -->
            <div class="perda-alert-card mb-5">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.82rem; font-weight:800; color:#B91C1C;">
                            <i data-lucide="shield-alert" class="lucide-sm"></i> PENEGAKAN HUKUM PERDA NO. 21 TAHUN 2012
                        </div>
                        <h3 style="font-size:1.2rem; font-weight:800; color:#7F1D1D; margin-bottom:0.4rem;">
                            Sanksi Tindak Pidana Ringan (Tipiring) Buang Sampah Sembarangan
                        </h3>
                        <p style="font-size:0.85rem; color:#991B1B; margin:0; line-height:1.5;">
                            Setiap orang yang membuang sampah tidak pada tempatnya atau membuang sampah di luar jam yang ditentukan (05.00 - 18.00 WIB) dapat dikenakan sanksi denda maksimal <strong>Rp 5.000.000,-</strong> atau kurungan badan maksimal <strong>3 bulan</strong> oleh Satpol PP Kota Padang.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="buat-laporan.php?kategori=1" class="btn btn-danger">
                            <i data-lucide="camera" class="lucide-sm"></i> Laporkan Pelanggar
                        </a>
                    </div>
                </div>
            </div>

            <!-- DIREKTORI BANK SAMPAH & TIPS LINGKUNGAN -->
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <div class="card-custom h-100 p-4">
                        <h3 style="font-size:1.15rem; font-weight:800; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                            <i data-lucide="building-2" class="text-primary"></i> Bank Sampah Binaan DLH Kota Padang
                        </h3>
                        <p style="font-size:0.85rem; color:var(--color-text-muted); margin-bottom:1rem;">
                            Tukarkan sampah anorganik Anda menjadi tabungan rupiah atau emas di Bank Sampah terdekat:
                        </p>

                        <div class="d-flex flex-column gap-2">
                            <div class="bank-sampah-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="font-size:0.9rem;">Bank Sampah Induk Pancadaya</strong>
                                    <span class="badge bg-success" style="font-size:0.7rem;">Kuranji</span>
                                </div>
                                <div style="font-size:0.8rem; color:var(--color-text-muted); margin-top:2px;">
                                    Komplek Perumahan Belimbing, Kuranji &bull; Telp: 0812-66xx-xxxx
                                </div>
                            </div>

                            <div class="bank-sampah-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="font-size:0.9rem;">Bank Sampah Berkah Mandiri</strong>
                                    <span class="badge bg-success" style="font-size:0.7rem;">Koto Tangah</span>
                                </div>
                                <div style="font-size:0.8rem; color:var(--color-text-muted); margin-top:2px;">
                                    Jl. Adinegoro, Lubuk Buaya &bull; Buka: Setiap Sabtu & Minggu
                                </div>
                            </div>

                            <div class="bank-sampah-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="font-size:0.9rem;">Bank Sampah Seberang Padang</strong>
                                    <span class="badge bg-success" style="font-size:0.7rem;">Padang Selatan</span>
                                </div>
                                <div style="font-size:0.8rem; color:var(--color-text-muted); margin-top:2px;">
                                    Kelurahan Seberang Padang &bull; Penimbangan: Minggu Pagi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-custom h-100 p-4">
                        <h3 style="font-size:1.15rem; font-weight:800; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                            <i data-lucide="droplets" class="text-info"></i> 5 Langkah Cegah Banjir & Genangan Air
                        </h3>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start gap-3">
                                <span class="step-num" style="flex-shrink:0;">1</span>
                                <div>
                                    <h4 style="font-size:0.92rem; font-weight:700; margin:0 0 2px;">Gotong Royong Bersihkan Got/Drainase</h4>
                                    <p style="font-size:0.8rem; color:var(--color-text-muted); margin:0;">Bersihkan endapan sedimen dan sampah plastik di saluran air depan rumah secara rutin.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <span class="step-num" style="flex-shrink:0;">2</span>
                                <div>
                                    <h4 style="font-size:0.92rem; font-weight:700; margin:0 0 2px;">Jangan Tutup Total Permukaan Saluran</h4>
                                    <p style="font-size:0.8rem; color:var(--color-text-muted); margin:0;">Sediakan bak kontrol (grill besi) agar pembersihan berkala tetap mudah dilakukan.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <span class="step-num" style="flex-shrink:0;">3</span>
                                <div>
                                    <h4 style="font-size:0.92rem; font-weight:700; margin:0 0 2px;">Buat Lubang Resapan Biopori</h4>
                                    <p style="font-size:0.8rem; color:var(--color-text-muted); margin:0;">Menyerap air hujan ke tanah sekaligus menyuburkan tanah pekarangan rumah.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <span class="step-num" style="flex-shrink:0;">4</span>
                                <div>
                                    <h4 style="font-size:0.92rem; font-weight:700; margin:0 0 2px;">Laporkan Pohon Lapuk Rawan Tumbang</h4>
                                    <p style="font-size:0.8rem; color:var(--color-text-muted); margin:0;">Cegah pohon tumbang saat angin kencang dengan melapor ke DLH untuk pemangkasan dahan.</p>
                                </div>
                            </div>
                        </div>
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
    </script>
</body>
</html>
