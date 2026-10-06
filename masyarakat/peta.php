<?php
// =====================================================
// HALAMAN PETA PERMASALAHAN LINGKUNGAN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

// Ambil Kategori untuk filter
$result_kategori = mysqli_query($koneksi, "SELECT id, nama_kategori, ikon FROM kategori ORDER BY nama_kategori ASC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Permasalahan Lingkungan - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .map-wrapper-clean {
            padding: 1rem 0 2rem;
        }

        .map-layout-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 1.25rem;
            height: calc(100vh - 140px);
            min-height: 540px;
        }

        .map-sidebar-panel {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--shadow-xs);
        }

        .map-sidebar-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--color-border);
            background-color: var(--color-surface-subtle);
            font-size: 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .map-sidebar-filters {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--color-border);
            background-color: var(--color-surface);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .map-sidebar-list {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 0.75rem;
        }

        .map-report-item {
            padding: 0.75rem 0.85rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.15s ease;
            background-color: var(--color-surface);
        }

        .map-report-item:hover {
            border-color: var(--color-primary);
            background-color: var(--color-primary-subtle);
        }

        .map-report-item .item-title {
            font-size: 0.86rem;
            font-weight: 600;
            color: var(--color-text-main);
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .map-report-item .item-sub {
            font-size: 0.76rem;
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .map-canvas-container {
            position: relative;
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-xs);
            height: 100%;
        }

        #map-full {
            width: 100%;
            height: 100%;
        }

        @media (max-width: 991.98px) {
            .map-layout-grid {
                grid-template-columns: 1fr;
                height: auto;
            }

            .map-canvas-container {
                height: 400px;
                order: 1;
            }

            .map-sidebar-panel {
                height: 420px;
                order: 2;
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
                    <li class="nav-item"><a class="nav-link active" href="peta.php"><i data-lucide="map" class="lucide-sm"></i> Peta Permasalahan</a></li>
                    <li class="nav-item"><a class="nav-link" href="jadwal-sampah.php"><i data-lucide="truck" class="lucide-sm"></i> Jadwal Sampah</a></li>
                    <li class="nav-item"><a class="nav-link" href="edukasi.php"><i data-lucide="book-open" class="lucide-sm"></i> Edukasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="alur.php"><i data-lucide="git-merge" class="lucide-sm"></i> Alur Pelaporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="tracking.php"><i data-lucide="search" class="lucide-sm"></i> Tracking</a></li>
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

    <!-- ===== MAP MAIN ===== -->
    <main class="map-wrapper-clean">
        <div class="container-fluid px-lg-4">
            <div class="map-layout-grid">
                
                <!-- SIDEBAR LIST & FILTER -->
                <div class="map-sidebar-panel">
                    <div class="map-sidebar-header">
                        <div class="d-flex align-items-center gap-1">
                            <i data-lucide="sliders-horizontal" class="lucide-sm text-primary"></i>
                            <span>Daftar Laporan</span>
                        </div>
                        <span class="badge rounded-pill bg-light text-dark border font-monospace" id="counter-badge" style="font-size:0.75rem;">
                            0 Laporan
                        </span>
                    </div>

                    <div class="map-sidebar-filters">
                        <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari judul / kode laporan / lokasi...">
                        
                        <div class="row g-1">
                            <div class="col-6">
                                <select id="filter-kategori" class="form-select form-select-sm">
                                    <option value="">Semua Kategori</option>
                                    <?php while ($kat = mysqli_fetch_assoc($result_kategori)): ?>
                                    <option value="<?php echo $kat['id']; ?>"><?php echo $kat['ikon'] . ' ' . htmlspecialchars($kat['nama_kategori']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select id="filter-status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Terverifikasi">Terverifikasi</option>
                                    <option value="Dalam Penanganan">Penanganan</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="map-sidebar-list" id="report-list-container">
                        <div class="text-center py-4 text-muted" style="font-size:0.82rem;">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memuat data peta...
                        </div>
                    </div>
                </div>

                <!-- MAP CANVAS -->
                <div class="map-canvas-container">
                    <div id="map-full"></div>

                    <!-- Layer Switcher -->
                    <div class="map-layer-switch">
                        <button type="button" class="map-layer-btn active" id="btn-layer-osm" onclick="switchMapLayer('osm')">
                            Peta Jalan
                        </button>
                        <button type="button" class="map-layer-btn" id="btn-layer-sat" onclick="switchMapLayer('sat')">
                            Satelit
                        </button>
                    </div>

                    <!-- Legend -->
                    <div class="map-legend-box">
                        <strong style="font-size:0.72rem; color:var(--color-text-main); margin-bottom:2px;">Status Laporan:</strong>
                        <div class="legend-indicator">
                            <span class="legend-dot" style="background-color:#0284C7;"></span>
                            <span>Terverifikasi</span>
                        </div>
                        <div class="legend-indicator">
                            <span class="legend-dot" style="background-color:#D97706;"></span>
                            <span>Dalam Penanganan</span>
                        </div>
                        <div class="legend-indicator">
                            <span class="legend-dot" style="background-color:#16A34A;"></span>
                            <span>Selesai</span>
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
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="brand-badge"><i data-lucide="leaf"></i></span>
                        <span style="font-weight:800; font-size:1.1rem; color:#FFFFFF;">SIGALING PADANG</span>
                    </div>
                    <p style="font-size:0.83rem; color:#94A3B8; line-height:1.6;">
                        Sistem Informasi Pengaduan & Penanganan Lingkungan Hidup Kota Padang terintegrasi.
                    </p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Akses Cepat</h6>
                    <ul>
                        <li><a href="../index.php">Beranda</a></li>
                        <li><a href="peta.php">Peta Masalah</a></li>
                        <li><a href="jadwal-sampah.php">Jadwal Sampah</a></li>
                        <li><a href="edukasi.php">Edukasi</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h6>Layanan Warga</h6>
                    <ul>
                        <li><a href="alur.php">Alur Pelaporan</a></li>
                        <li><a href="tracking.php">Lacak Pengaduan</a></li>
                        <li><a href="buat-laporan.php">Buat Laporan Baru</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6>Kontak & Posko</h6>
                    <p style="font-size:0.83rem; margin-bottom:0.3rem;">DLH Kota Padang: Jl. Rasuna Said No. 77</p>
                    <p style="font-size:0.83rem; margin-bottom:0.3rem;">Call Center: 112 / (0751) 31555</p>
                </div>
            </div>
            <div class="footer-bottom-bar">
                &copy; <?php echo date('Y'); ?> Dinas Lingkungan Hidup Kota Padang. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        let map, markersLayer, tileOsm, tileSat;
        let allReports = [];
        let mapMarkers = {};

        const STATUS_COLORS = {
            'Terverifikasi': '#0284C7',
            'Dalam Penanganan': '#D97706',
            'Selesai': '#16A34A'
        };

        function initMap() {
            map = L.map('map-full', {
                center: [-0.9471, 100.4172], // Kota Padang Center
                zoom: 12,
                zoomControl: true
            });

            tileOsm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            tileSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '&copy; Esri World Imagery',
                maxZoom: 18
            });

            markersLayer = L.layerGroup().addTo(map);

            // Fetch Real Data
            fetch('../proses/ambil-laporan-json.php')
                .then(res => res.json())
                .then(data => {
                    allReports = data;
                    renderReports(allReports);
                })
                .catch(err => {
                    console.error('Error fetching map data:', err);
                    document.getElementById('report-list-container').innerHTML = `
                        <div class="text-center py-4 text-danger" style="font-size:0.85rem;">
                            Gagal memuat data peta
                        </div>
                    `;
                });
        }

        function switchMapLayer(type) {
            if (type === 'sat') {
                if (map.hasLayer(tileOsm)) map.removeLayer(tileOsm);
                if (!map.hasLayer(tileSat)) map.addLayer(tileSat);
                document.getElementById('btn-layer-sat').classList.add('active');
                document.getElementById('btn-layer-osm').classList.remove('active');
            } else {
                if (map.hasLayer(tileSat)) map.removeLayer(tileSat);
                if (!map.hasLayer(tileOsm)) map.addLayer(tileOsm);
                document.getElementById('btn-layer-osm').classList.add('active');
                document.getElementById('btn-layer-sat').classList.remove('active');
            }
        }

        function renderReports(reports) {
            markersLayer.clearLayers();
            mapMarkers = {};
            const listContainer = document.getElementById('report-list-container');
            document.getElementById('counter-badge').textContent = reports.length + ' Laporan';

            if (reports.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-4 text-muted" style="font-size:0.85rem;">
                        Tidak ada laporan yang cocok
                    </div>
                `;
                return;
            }

            let listHtml = '';

            reports.forEach(lap => {
                const color = STATUS_COLORS[lap.status] || '#64748B';

                // Render Map Marker
                if (lap.latitude && lap.longitude) {
                    const pinIcon = L.divIcon({
                        html: `<div class="custom-pin" style="background-color:${color};">
                                 <span>${lap.ikon || '📍'}</span>
                               </div>`,
                        className: '',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14],
                        popupAnchor: [0, -14]
                    });

                    const popupContent = `
                        <div style="min-width:190px; padding:2px;">
                            <div style="font-size:0.73rem; color:${color}; font-weight:700; text-transform:uppercase; margin-bottom:2px;">
                                ${escapeHtml(lap.status)}
                            </div>
                            <h6 style="margin:0 0 4px; font-size:0.88rem; font-weight:700; color:#0F172A; line-height:1.3;">
                                ${escapeHtml(lap.judul)}
                            </h6>
                            <div style="font-size:0.76rem; color:#64748B; margin-bottom:6px;">
                                📍 ${escapeHtml(lap.alamat || lap.kecamatan || 'Kota Padang')}
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.74rem; border-top:1px solid #E2E8F0; padding-top:5px;">
                                <span style="font-family:monospace; font-weight:700; color:#0284C7;">#${escapeHtml(lap.kode_laporan)}</span>
                                <a href="detail-laporan.php?id=${lap.id}" style="font-weight:700; color:var(--color-primary); text-decoration:none;">Detail &rarr;</a>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([lap.latitude, lap.longitude], { icon: pinIcon })
                        .bindPopup(popupContent);

                    markersLayer.addLayer(marker);
                    mapMarkers[lap.id] = marker;
                }

                // Render Sidebar Item
                listHtml += `
                    <div class="map-report-item" onclick="flyToReport(${lap.id}, ${lap.latitude}, ${lap.longitude})">
                        <div class="d-flex align-items-start gap-2 mb-1">
                            <span class="report-category-icon">${lap.ikon || '📋'}</span>
                            <div class="item-title flex-grow-1">${escapeHtml(lap.judul)}</div>
                        </div>
                        <div class="item-sub">
                            <span><span class="legend-dot" style="display:inline-block; vertical-align:middle; width:8px; height:8px; background-color:${color};"></span> ${escapeHtml(lap.status)}</span>
                            <span class="font-monospace" style="color:#0284C7; font-weight:700;">#${escapeHtml(lap.kode_laporan)}</span>
                        </div>
                    </div>
                `;
            });

            listContainer.innerHTML = listHtml;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function flyToReport(id, lat, lng) {
            if (lat && lng && map) {
                map.flyTo([lat, lng], 16, { duration: 1 });
                if (mapMarkers[id]) {
                    setTimeout(() => {
                        mapMarkers[id].openPopup();
                    }, 1000);
                }
            }

            // If mobile, scroll to map
            if (window.innerWidth < 992) {
                window.scrollTo({ top: 120, behavior: 'smooth' });
            }
        }

        function applyFilters() {
            const query = document.getElementById('search-input').value.toLowerCase().trim();
            const kategoriId = document.getElementById('filter-kategori').value;
            const status = document.getElementById('filter-status').value;

            const filtered = allReports.filter(lap => {
                let match = true;
                if (query) {
                    const text = ((lap.judul || '') + ' ' + (lap.kode_laporan || '') + ' ' + (lap.alamat || '') + ' ' + (lap.kecamatan || '')).toLowerCase();
                    match = match && text.includes(query);
                }
                if (kategoriId) {
                    match = match && (lap.kategori_id == kategoriId);
                }
                if (status) {
                    match = match && (lap.status === status);
                }
                return match;
            });

            renderReports(filtered);
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str || '';
            return div.innerHTML;
        }

        document.getElementById('search-input').addEventListener('input', applyFilters);
        document.getElementById('filter-kategori').addEventListener('change', applyFilters);
        document.getElementById('filter-status').addEventListener('change', applyFilters);

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
</body>
</html>
