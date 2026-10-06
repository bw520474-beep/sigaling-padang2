<?php
// =====================================================
// HALAMAN BUAT LAPORAN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

// 1. Ambil Kategori dari Database
$query_kategori = "SELECT id, nama_kategori, ikon FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);

// 2. Kategori Selected dari URL jika ada
$kategori_selected = isset($_GET['kategori']) ? intval($_GET['kategori']) : '';

// 3. User default name & phone jika login
$user_nama = is_logged_in() ? get_user_nama() : '';
$user_email = is_logged_in() ? get_user_email() : '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - SIGALING PADANG</title>

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .page-banner {
            background-color: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            padding: 1.75rem 0;
            margin-bottom: 1.5rem;
        }

        .page-banner h1 {
            font-size: 1.45rem;
            margin-bottom: 0.25rem;
            color: var(--color-slate-900);
        }

        .page-banner p {
            margin: 0;
            font-size: 0.88rem;
            color: var(--color-slate-600);
        }

        #map {
            height: 300px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            margin-bottom: 0.75rem;
            width: 100%;
        }

        .location-badge-box {
            background-color: var(--color-surface-subtle);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 0.65rem 0.85rem;
            font-size: 0.82rem;
            margin-bottom: 0.75rem;
            display: none;
        }

        .location-badge-box.active {
            display: block;
        }

        .photo-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .photo-thumb-item {
            position: relative;
            width: 100%;
            height: 100px;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--color-border);
            background: #F1F5F9;
        }

        .photo-thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-thumb-item .btn-remove-thumb {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 24px;
            height: 24px;
            background: rgba(220, 38, 38, 0.85);
            color: #FFFFFF;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
        }

        .dropzone-label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background-color: var(--color-surface-subtle);
            border: 1.5px dashed var(--color-border-hover);
            color: var(--color-text-main);
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .dropzone-label:hover {
            background-color: var(--color-primary-subtle);
            border-color: var(--color-primary);
            color: var(--color-primary-hover);
        }

        .char-counter {
            font-size: 0.75rem;
            color: var(--color-slate-400);
            text-align: right;
            margin-top: 0.25rem;
        }

        @media (max-width: 767.98px) {
            #map {
                height: 250px;
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

    <!-- ===== HEADER BANNER ===== -->
    <div class="page-banner">
        <div class="container" style="max-width: 800px;">
            <h1>Formulir Pengaduan Lingkungan</h1>
            <p>Sampaikan data laporan dan titik koordinat dengan akurat untuk segera ditangani petugas lapangan.</p>
        </div>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <main>
        <div class="container pb-5" style="max-width: 800px;">
            <div id="alert-container"></div>

            <form id="form-laporan" method="POST" enctype="multipart/form-data">
                
                <!-- 1. IDENTITAS PELAPOR -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="user" class="lucide-sm text-primary"></i> 1. Identitas Pelapor</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-clean">
                                <label class="form-label-clean" for="nama_pelapor">
                                    <span>Nama Lengkap</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama_pelapor" name="nama_pelapor" 
                                       placeholder="Nama lengkap Anda" required
                                       value="<?php echo htmlspecialchars($user_nama); ?>">
                                <div class="form-error-clean">Nama lengkap wajib diisi</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-clean">
                                <label class="form-label-clean" for="kontak_pelapor">
                                    <span>Nomor HP / WhatsApp</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" class="form-control" id="kontak_pelapor" name="kontak_pelapor" 
                                       placeholder="08xxxxxxxxxx" required
                                       value="<?php echo htmlspecialchars($user_email); ?>">
                                <div class="form-error-clean">Kontak aktif wajib diisi untuk verifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. DETAIL PERMASALAHAN -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="file-text" class="lucide-sm text-primary"></i> 2. Detail Permasalahan</h6>
                    </div>
                    
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="kategori_id">
                            <span>Kategori Permasalahan</span>
                            <span class="required-star">*</span>
                        </label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            if (mysqli_num_rows($result_kategori) > 0) {
                                while ($kategori = mysqli_fetch_assoc($result_kategori)) {
                                    $selected = ($kategori_selected == $kategori['id']) ? 'selected' : '';
                                    echo '<option value="' . $kategori['id'] . '" ' . $selected . '>'
                                        . $kategori['ikon'] . ' ' . htmlspecialchars($kategori['nama_kategori'])
                                        . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <div class="form-error-clean">Kategori permasalahan wajib dipilih</div>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="judul">
                            <span>Judul Laporan</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" class="form-control" id="judul" name="judul" 
                               placeholder="Contoh: Tumpukan sampah liar di pinggir jalan By Pass" maxlength="255" required>
                        <div class="char-counter"><span id="judul-count">0</span>/255</div>
                        <div class="form-error-clean">Judul laporan wajib diisi</div>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="deskripsi">
                            <span>Deskripsi Masalah Lengkap</span>
                            <span class="required-star">*</span>
                        </label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Jelaskan kondisi permasalahan, perkiraan volume sampah / drainase tersumbat, dan patokan lokasi..." maxlength="1000" required></textarea>
                        <div class="char-counter"><span id="deskripsi-count">0</span>/1000</div>
                        <div class="form-error-clean">Deskripsi masalah wajib diisi</div>
                    </div>
                </div>

                <!-- 3. LOKASI PERMASALAHAN -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="map-pin" class="lucide-sm text-primary"></i> 3. Lokasi Permasalahan</h6>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-use-gps">
                            <i data-lucide="crosshair" class="lucide-sm"></i> Gunakan Lokasi GPS Saya
                        </button>
                        <span style="font-size:0.8rem; color:var(--color-slate-500); align-self:center;">atau klik langsung titik pada peta di bawah:</span>
                    </div>

                    <div id="map"></div>

                    <div class="location-badge-box" id="location-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>Koordinat Terpilih:</strong>
                            <span id="info-coords" class="text-primary font-monospace fw-semibold">-</span>
                        </div>
                        <div style="color:var(--color-slate-500);">
                            <span id="info-alamat">Titik koordinat berhasil dikunci</span>
                        </div>
                    </div>

                    <!-- Hidden Inputs Koordinat -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    <input type="hidden" id="alamat" name="alamat">
                    <input type="hidden" id="kecamatan" name="kecamatan">
                    <input type="hidden" id="kelurahan" name="kelurahan">

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-group-clean">
                                <label class="form-label-clean" style="font-size:0.8rem;">Kecamatan (Otomatis/Manual)</label>
                                <input type="text" class="form-control form-control-sm" id="input-kecamatan" placeholder="Kecamatan">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-clean">
                                <label class="form-label-clean" style="font-size:0.8rem;">Kelurahan (Otomatis/Manual)</label>
                                <input type="text" class="form-control form-control-sm" id="input-kelurahan" placeholder="Kelurahan">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. FOTO BUKTI -->
                <div class="card-custom">
                    <div class="card-header-clean">
                        <h6><i data-lucide="camera" class="lucide-sm text-primary"></i> 4. Foto Bukti Kejadian</h6>
                    </div>

                    <label class="dropzone-label" for="file-foto">
                        <i data-lucide="upload-cloud" style="width:32px; height:32px; color:var(--color-primary);"></i>
                        <span style="font-weight:700; font-size:0.95rem; color:var(--color-text-main);">Pilih Foto Bukti Kejadian</span>
                        <span style="font-size:0.8rem; color:var(--color-text-muted);">Klik untuk memilih dari galeri / kamera atau seret foto ke sini</span>
                        <input type="file" id="file-foto" accept="image/jpeg,image/png,image/jpg" multiple style="display:none;">
                    </label>
                    <span class="form-hint-clean">Format: JPG, JPEG, PNG (Maks. 5MB/file &bull; Maks. 5 foto)</span>

                    <div class="photo-preview-grid" id="photo-preview"></div>
                </div>

                <!-- SUBMIT BUTTONS -->
                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="../index.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i data-lucide="send" class="lucide-sm"></i> Kirim Laporan
                    </button>
                </div>

            </form>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        let map;
        let marker;
        let fotosSelected = [];
        const PADANG_CENTER = [-0.9471, 100.4172];

        // 1. Inisialisasi Peta
        function initMap() {
            map = L.map('map').setView(PADANG_CENTER, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            map.on('click', function(e) {
                setLocationMarker(e.latlng.lat, e.latlng.lng);
            });
        }

        // 2. Set Marker Lokasi
        function setLocationMarker(lat, lng) {
            if (marker) map.removeLayer(marker);

            const pinIcon = L.divIcon({
                html: `<div class="custom-pin" style="background-color:#059669; width:28px; height:28px;">
                         <i data-lucide="map-pin" style="width:16px; height:16px;"></i>
                       </div>`,
                className: '',
                iconSize: [28, 28],
                iconAnchor: [14, 28]
            });

            marker = L.marker([lat, lng], { icon: pinIcon }).addTo(map);
            initLucideIcons();

            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
            document.getElementById('info-coords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);

            document.getElementById('location-info').classList.add('active');
            document.getElementById('alamat').value = 'Titik Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);

            // Sync input kecamatan/kelurahan
            document.getElementById('kecamatan').value = document.getElementById('input-kecamatan').value;
            document.getElementById('kelurahan').value = document.getElementById('input-kelurahan').value;

            map.setView([lat, lng], 15);
        }

        // 3. GPS Geolocation Handler
        document.getElementById('btn-use-gps').addEventListener('click', function() {
            if (navigator.geolocation) {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengambil Lokasi...';

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        setLocationMarker(pos.coords.latitude, pos.coords.longitude);
                        document.getElementById('btn-use-gps').disabled = false;
                        document.getElementById('btn-use-gps').innerHTML = '<i data-lucide="crosshair" class="lucide-sm"></i> Gunakan Lokasi GPS Saya';
                        initLucideIcons();
                        showToast('Lokasi GPS berhasil ditemukan!', 'success');
                    },
                    function(err) {
                        document.getElementById('btn-use-gps').disabled = false;
                        document.getElementById('btn-use-gps').innerHTML = '<i data-lucide="crosshair" class="lucide-sm"></i> Gunakan Lokasi GPS Saya';
                        initLucideIcons();
                        showToast('Gagal mengakses GPS. Silakan klik langsung pada peta.', 'warning');
                    },
                    { timeout: 10000, enableHighAccuracy: true }
                );
            } else {
                showToast('Browser Anda tidak mendukung Geolocation', 'warning');
            }
        });

        // 4. Sinkronisasi input manual kecamatan/kelurahan ke hidden
        document.getElementById('input-kecamatan').addEventListener('input', function() {
            document.getElementById('kecamatan').value = this.value;
        });
        document.getElementById('input-kelurahan').addEventListener('input', function() {
            document.getElementById('kelurahan').value = this.value;
        });

        // 5. File Upload Handler
        document.getElementById('file-foto').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            if (files.length + fotosSelected.length > 5) {
                showToast('Maksimal upload 5 foto bukti', 'warning');
                return;
            }

            files.forEach((file, idx) => {
                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                    showToast('Format tidak didukung: ' + file.name, 'danger');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showToast('Ukuran foto maksimal 5MB: ' + file.name, 'danger');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(evt) {
                    fotosSelected.push({
                        file: file,
                        preview: evt.target.result,
                        id: Date.now() + idx
                    });
                    renderPhotoPreview();
                };
                reader.readAsDataURL(file);
            });
        });

        function renderPhotoPreview() {
            const container = document.getElementById('photo-preview');
            container.innerHTML = '';

            fotosSelected.forEach((item, index) => {
                const div = document.createElement('div');
                div.className = 'photo-thumb-item';
                div.innerHTML = `
                    <img src="${item.preview}" alt="Bukti ${index + 1}">
                    <button type="button" class="btn-remove-thumb" onclick="removeFoto(${index})" title="Hapus foto">
                        <i data-lucide="x" style="width:14px; height:14px;"></i>
                    </button>
                `;
                container.appendChild(div);
            });
            initLucideIcons();
        }

        function removeFoto(index) {
            fotosSelected.splice(index, 1);
            renderPhotoPreview();
        }

        // 6. Character Counters
        document.getElementById('judul').addEventListener('input', function() {
            document.getElementById('judul-count').textContent = this.value.length;
        });
        document.getElementById('deskripsi').addEventListener('input', function() {
            document.getElementById('deskripsi-count').textContent = this.value.length;
        });

        // 7. Form Submit & AJAX
        document.getElementById('form-laporan').addEventListener('submit', function(e) {
            e.preventDefault();

            // Reset error states
            document.querySelectorAll('.form-error-clean').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-control, .form-select').forEach(el => el.classList.remove('error'));

            let isValid = true;

            const nama = document.getElementById('nama_pelapor').value.trim();
            if (!nama) {
                document.getElementById('nama_pelapor').classList.add('error');
                document.getElementById('nama_pelapor').nextElementSibling.style.display = 'block';
                isValid = false;
            }

            const kontak = document.getElementById('kontak_pelapor').value.trim();
            if (!kontak) {
                document.getElementById('kontak_pelapor').classList.add('error');
                document.getElementById('kontak_pelapor').nextElementSibling.style.display = 'block';
                isValid = false;
            }

            const kategori = document.getElementById('kategori_id').value;
            if (!kategori) {
                document.getElementById('kategori_id').classList.add('error');
                document.getElementById('kategori_id').nextElementSibling.style.display = 'block';
                isValid = false;
            }

            const judul = document.getElementById('judul').value.trim();
            if (!judul) {
                document.getElementById('judul').classList.add('error');
                document.getElementById('judul').nextElementSibling.nextElementSibling.style.display = 'block';
                isValid = false;
            }

            const deskripsi = document.getElementById('deskripsi').value.trim();
            if (!deskripsi) {
                document.getElementById('deskripsi').classList.add('error');
                document.getElementById('deskripsi').nextElementSibling.nextElementSibling.style.display = 'block';
                isValid = false;
            }

            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            if (!lat || !lng) {
                showToast('Lokasi permasalahan wajib ditentukan via GPS atau klik peta', 'danger');
                isValid = false;
            }

            if (fotosSelected.length === 0) {
                showToast('Wajib melampirkan minimal 1 foto bukti', 'danger');
                isValid = false;
            }

            if (!isValid) return;

            // Submit via FormData
            const btnSubmit = document.getElementById('btn-submit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim Laporan...';

            const formData = new FormData();
            formData.append('nama_pelapor', nama);
            formData.append('kontak_pelapor', kontak);
            formData.append('kategori_id', kategori);
            formData.append('judul', judul);
            formData.append('deskripsi', deskripsi);
            formData.append('latitude', lat);
            formData.append('longitude', lng);
            formData.append('alamat', document.getElementById('alamat').value);
            formData.append('kecamatan', document.getElementById('input-kecamatan').value);
            formData.append('kelurahan', document.getElementById('input-kelurahan').value);

            fotosSelected.forEach(item => {
                formData.append('foto[]', item.file);
            });

            fetch('../proses/simpan-laporan.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Laporan berhasil dikirim! Kode: ' + data.kode_laporan, 'success');
                    setTimeout(() => {
                        window.location.href = 'tracking.php?kode=' + encodeURIComponent(data.kode_laporan);
                    }, 1500);
                } else {
                    showToast('Gagal mengirim laporan: ' + (data.message || 'Error'), 'danger');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i data-lucide="send" class="lucide-sm"></i> Kirim Laporan';
                    initLucideIcons();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
        window.addEventListener('load', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
