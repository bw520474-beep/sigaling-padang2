<?php
// =====================================================
// HALAMAN ALUR PENANGANAN PENGADUAN - SIGALING PADANG
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
    <title>Alur Penanganan Pengaduan - SIGALING PADANG</title>
    
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

        .alur-step-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-xs);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.2s ease;
        }

        .alur-step-card:hover {
            border-color: var(--color-primary);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .alur-badge-num {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--color-primary);
            color: #FFFFFF;
            font-weight: 800;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .alur-step-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-text-main);
            margin-bottom: 0.5rem;
        }

        .alur-step-card p {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            line-height: 1.5;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .alur-sla-box {
            background-color: var(--color-surface-subtle);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 0.5rem 0.75rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .faq-item {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 0.85rem;
        }

        .faq-item h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--color-text-main);
            margin-bottom: 0.35rem;
        }

        .faq-item p {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            margin: 0;
            line-height: 1.5;
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
                        <a class="nav-link" href="edukasi.php">
                            <i data-lucide="book-open" class="lucide-sm"></i> Edukasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="alur.php">
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
                <i data-lucide="shield-check" class="lucide-sm"></i> STANDAR OPERASIONAL PROSEDUR RESMI
            </div>
            <h1>Alur Penanganan Pengaduan Lingkungan</h1>
            <p>Mekanisme terstruktur, transparan, dan akuntabel dari pelaporan warga hingga tuntas ditangani petugas lapangan Dinas Lingkungan Hidup Kota Padang.</p>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="py-5">
        <div class="container">

            <!-- 4 TAHAP UTAMA -->
            <div class="mb-5">
                <div class="text-center mb-4">
                    <span class="badge-status badge-status-terverifikasi mb-2">4 TAHAPAN KERJA</span>
                    <h2 style="font-size:1.45rem; font-weight:800; color:var(--color-text-main); margin-bottom:0.35rem;">
                        Tahapan Proses dari Laporan hingga Penanganan Selesai
                    </h2>
                    <p class="text-muted" style="font-size:0.9rem; max-width:600px; margin:0 auto;">
                        Setiap laporan memiliki nomor tiket unik untuk pelacakan publik secara real-time.
                    </p>
                </div>

                <div class="row g-3">
                    <!-- Tahap 1 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="alur-step-card">
                            <div class="alur-badge-num">1</div>
                            <h3>Input Laporan & Bukti</h3>
                            <p>
                                Warga mengisi formulir pengaduan, memilih kategori permasalahan, mengunci titik koordinat GPS lokasi, serta melampirkan foto bukti kondisi terkini di lapangan.
                            </p>
                            <div class="alur-sla-box">
                                <i data-lucide="zap" class="lucide-sm"></i>
                                <span>Waktu: Seketika (Online)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tahap 2 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="alur-step-card">
                            <div class="alur-badge-num">2</div>
                            <h3>Verifikasi Administrator</h3>
                            <p>
                                Admin verifikator DLH Kota Padang memeriksa keabsahan data, kejelasan foto bukti, dan menentukan tingkat prioritas penanganan (Rendah, Sedang, atau Tinggi).
                            </p>
                            <div class="alur-sla-box">
                                <i data-lucide="clock" class="lucide-sm"></i>
                                <span>SLA: Maksimal 1x24 Jam</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tahap 3 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="alur-step-card">
                            <div class="alur-badge-num">3</div>
                            <h3>Disposisi OPD Teknis</h3>
                            <p>
                                Laporan yang valid didistribusikan ke dinas atau unit teknis terkait (DLH, Dinas PUPR, Satpol PP, atau Satgas Kecamatan) dan ditugaskan ke personil lapangan.
                            </p>
                            <div class="alur-sla-box">
                                <i data-lucide="send" class="lucide-sm"></i>
                                <span>SLA: Maksimal 1x24 Jam</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tahap 4 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="alur-step-card">
                            <div class="alur-badge-num" style="background:#16A34A;">4</div>
                            <h3>Penanganan & Bukti Tuntas</h3>
                            <p>
                                Petugas lapangan diterjunkan ke lokasi untuk membersihkan sampah/memperbaiki saluran, lalu mengunggah foto bukti penanganan selesai sebagai berita acara resmi.
                            </p>
                            <div class="alur-sla-box" style="color:#16A34A; background:#DCFCE7; border-color:rgba(22,163,74,0.2);">
                                <i data-lucide="check-circle" class="lucide-sm"></i>
                                <span>Status: Tuntas Teratasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TINGKAT PRIORITAS & SLA -->
            <div class="card-custom mb-5 p-4">
                <h3 style="font-size:1.15rem; font-weight:800; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                    <i data-lucide="alert-triangle" class="text-warning"></i> Standar Waktu Penanganan Berdasarkan Prioritas
                </h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded" style="background:#FEF2F2; border-color:#FECACA !important;">
                            <span class="badge-priority badge-priority-tinggi mb-2">PRIORITAS TINGGI</span>
                            <h4 style="font-size:0.95rem; font-weight:700; color:#991B1B; margin-bottom:0.3rem;">Bencana & Darurat Lingkungan</h4>
                            <p style="font-size:0.82rem; color:#7F1D1D; margin-bottom:0.5rem;">Pohon tumbang menghalangi jalan utama, banjir bandang akibat tanggul sampah, limbah berbahaya.</p>
                            <div style="font-size:0.8rem; font-weight:700; color:#B91C1C;">Target Respon: &le; 6 - 12 Jam</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded" style="background:#FFFBEB; border-color:#FDE68A !important;">
                            <span class="badge-priority badge-priority-sedang mb-2">PRIORITAS SEDANG</span>
                            <h4 style="font-size:0.95rem; font-weight:700; color:#92400E; margin-bottom:0.3rem;">Tumpukan Sampah & Drainase</h4>
                            <p style="font-size:0.82rem; color:#78350F; margin-bottom:0.5rem;">Sampah liar di fasilitas umum, drainase tersumbat di pemukiman warga, pencemaran sungai lokal.</p>
                            <div style="font-size:0.8rem; font-weight:700; color:#B45309;">Target Respon: &le; 1 - 2 Hari Kerja</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded" style="background:#F0FDF4; border-color:#BBF7D0 !important;">
                            <span class="badge-priority badge-priority-rendah mb-2">PRIORITAS RENDAH</span>
                            <h4 style="font-size:0.95rem; font-weight:700; color:#166534; margin-bottom:0.3rem;">Pemeliharaan & Kebersihan Rutin</h4>
                            <p style="font-size:0.82rem; color:#14532D; margin-bottom:0.5rem;">Permintaan jadwal pengangkutan sampah perumahan, usulan penambahan kontainer sampah.</p>
                            <div style="font-size:0.8rem; font-weight:700; color:#15803D;">Target Respon: &le; 3 Hari Kerja</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ SEPUTAR PELAPORAN -->
            <div class="mb-5">
                <h3 style="font-size:1.15rem; font-weight:800; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                    <i data-lucide="help-circle" class="text-primary"></i> Pertanyaan yang Sering Diajukan (FAQ)
                </h3>
                
                <div class="faq-item">
                    <h4>Apakah masyarakat harus mendaftar akun terlebih dahulu untuk melapor?</h4>
                    <p>Tidak wajib. Masyarakat dapat langsung membuat laporan publik dengan mengisi nama dan kontak aktif. Akun terdaftar hanya dibutuhkan jika Anda ingin menyimpan arsip riwayat laporan Anda.</p>
                </div>

                <div class="faq-item">
                    <h4>Bagaimana cara mengetahui bahwa laporan saya sudah ditangani?</h4>
                    <p>Gunakan menu <strong>Tracking</strong> lalu masukkan kode tiket laporan Anda (contoh: <code>LAP-202608-0001</code>). Anda dapat melihat linimasa proses dan foto bukti penyelesaian dari petugas.</p>
                </div>

                <div class="faq-item">
                    <h4>Mengapa titik koordinat lokasi sangat penting?</h4>
                    <p>Titik koordinat peta membantu armada truk sampah dan satgas kebersihan DLH menemukan lokasi kejadian dengan akurat tanpa tersesat, mempercepat waktu tanggap di lapangan.</p>
                </div>
            </div>

            <!-- CALL TO ACTION -->
            <div class="text-center p-4 border rounded" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);">
                <h3 style="font-size:1.25rem; font-weight:800; color:#14532D; margin-bottom:0.4rem;">
                    Temukan Masalah Lingkungan di Sekitar Anda?
                </h3>
                <p style="font-size:0.88rem; color:#166534; max-width:550px; margin:0 auto 1rem;">
                    Jangan biarkan sampah dan saluran tersumbat merusak lingkungan Kota Padang. Laporkan sekarang juga!
                </p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="buat-laporan.php" class="btn btn-primary">
                        <i data-lucide="send" class="lucide-sm"></i> Buat Laporan Sekarang
                    </a>
                    <a href="peta.php" class="btn btn-secondary">
                        <i data-lucide="map" class="lucide-sm"></i> Lihat Peta Permasalahan
                    </a>
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
