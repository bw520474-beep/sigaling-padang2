-- =====================================================
-- DATABASE SIGALING PADANG
-- Sistem Informasi Pelaporan & Monitoring Lingkungan
-- =====================================================

-- Hapus database lama jika ada (opsional)
DROP DATABASE IF EXISTS sigaling_padang;

-- Buat database baru
CREATE DATABASE sigaling_padang 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Gunakan database yang baru dibuat
USE sigaling_padang;

-- =====================================================
-- TABEL: users
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    no_hp VARCHAR(15),
    role ENUM('masyarakat', 'admin', 'petugas') DEFAULT 'masyarakat',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: kategori
-- =====================================================
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    ikon VARCHAR(50),
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nama (nama_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: laporan
-- =====================================================
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_laporan VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NULL,
    nama_pelapor VARCHAR(255) NOT NULL,
    kontak_pelapor VARCHAR(255),
    kategori_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    alamat TEXT,
    kecamatan VARCHAR(100),
    kelurahan VARCHAR(100),
    status ENUM('Menunggu Verifikasi', 'Terverifikasi', 'Dalam Penanganan', 'Selesai', 'Ditolak') DEFAULT 'Menunggu Verifikasi',
    prioritas ENUM('Rendah', 'Sedang', 'Tinggi') DEFAULT 'Sedang',
    tanggal_laporan DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT,
    INDEX idx_kode (kode_laporan),
    INDEX idx_status (status),
    INDEX idx_kategori (kategori_id),
    INDEX idx_tanggal (tanggal_laporan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: foto_laporan
-- =====================================================
CREATE TABLE foto_laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    laporan_id INT NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    jenis ENUM('bukti_laporan', 'bukti_penanganan') DEFAULT 'bukti_laporan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (laporan_id) REFERENCES laporan(id) ON DELETE CASCADE,
    INDEX idx_laporan (laporan_id),
    INDEX idx_jenis (jenis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: opd
-- =====================================================
CREATE TABLE opd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_opd VARCHAR(255) NOT NULL UNIQUE,
    deskripsi TEXT,
    kontak VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nama (nama_opd)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: laporan_opd
-- =====================================================
CREATE TABLE laporan_opd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    laporan_id INT NOT NULL,
    opd_id INT NOT NULL,
    petugas_id INT,
    catatan TEXT,
    tanggal_diteruskan DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (laporan_id) REFERENCES laporan(id) ON DELETE CASCADE,
    FOREIGN KEY (opd_id) REFERENCES opd(id) ON DELETE RESTRICT,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_laporan (laporan_id),
    INDEX idx_opd (opd_id),
    INDEX idx_petugas (petugas_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: penanganan
-- =====================================================
CREATE TABLE penanganan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    laporan_id INT NOT NULL,
    petugas_id INT NOT NULL,
    status ENUM('Menunggu', 'Dalam Proses', 'Selesai') DEFAULT 'Menunggu',
    keterangan TEXT,
    foto VARCHAR(255),
    tanggal_penanganan DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (laporan_id) REFERENCES laporan(id) ON DELETE CASCADE,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_laporan (laporan_id),
    INDEX idx_petugas (petugas_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATA AWAL: Kategori Masalah Lingkungan
-- =====================================================
INSERT INTO kategori (nama_kategori, ikon, deskripsi) VALUES
('Sampah', '🗑️', 'Laporan terkait pembuangan sampah yang tidak sesuai'),
('Drainase Tersumbat', '🌊', 'Laporan saluran air tersumbat atau tidak lancar'),
('Pencemaran Air', '💧', 'Laporan air tercemar atau tidak layak'),
('Pencemaran Udara', '💨', 'Laporan kualitas udara yang buruk'),
('Pohon Tumbang/Rusak', '🌳', 'Laporan pohon tumbang atau rusak'),
('Pembakaran Sampah', '🔥', 'Laporan pembakaran sampah liar'),
('Limbah', '⚠️', 'Laporan limbah industri atau rumah tangga'),
('Kebisingan', '🔊', 'Laporan kebisingan dari berbagai sumber'),
('Sungai', '🏞️', 'Laporan masalah sungai dan ekosistem'),
('Permasalahan Lainnya', '📋', 'Laporan masalah lingkungan lainnya');

-- =====================================================
-- DATA AWAL: OPD (Organisasi Perangkat Daerah)
-- =====================================================
INSERT INTO opd (nama_opd, deskripsi, kontak) VALUES
('Dinas Lingkungan Hidup', 'Menangani laporan sampah dan pencemaran', '0751-xxxxx'),
('Dinas Pekerjaan Umum', 'Menangani laporan drainase dan jalan', '0751-xxxxx'),
('Dinas Kehutanan', 'Menangani laporan pohon dan hutan', '0751-xxxxx'),
('Dinas Kesehatan', 'Menangani laporan kesehatan lingkungan', '0751-xxxxx'),
('Satuan Polisi Pamong Praja', 'Menangani laporan ketenangan dan ketertiban', '0751-xxxxx');

-- =====================================================
-- DATA AWAL: Admin
-- =====================================================
INSERT INTO users (nama, email, password, no_hp, role) VALUES
('Admin SIGALING', 'admin@sigaling.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/iu', '081234567890', 'admin');

-- Password: admin123 (hashed dengan password_hash)

-- =====================================================
-- SELESAI
-- =====================================================
