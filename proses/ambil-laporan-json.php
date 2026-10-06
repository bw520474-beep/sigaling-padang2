<?php
// =====================================================
// AMBIL DATA LAPORAN DALAM FORMAT JSON
// Digunakan untuk Peta Preview dan Peta Lengkap
// =====================================================

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/koneksi.php';

/**
 * @var mysqli $koneksi Database connection
 */

header('Content-Type: application/json');

// Query ambil laporan yang sudah punya koordinat & sudah terverifikasi minimal
$query = "SELECT l.id, l.kode_laporan, l.judul, l.deskripsi, l.latitude, l.longitude, 
                 l.status, l.alamat, l.kecamatan, l.tanggal_laporan,
                 k.id as kategori_id, k.nama_kategori, k.ikon
          FROM laporan l
          LEFT JOIN kategori k ON l.kategori_id = k.id
          WHERE l.latitude IS NOT NULL 
          AND l.longitude IS NOT NULL
          AND l.status IN ('Terverifikasi', 'Dalam Penanganan', 'Selesai')
          ORDER BY l.created_at DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo json_encode(['error' => 'Query error']);
    exit;
}

$laporan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $laporan[] = [
        'id' => (int)$row['id'],
        'kode_laporan' => $row['kode_laporan'],
        'judul' => $row['judul'],
        'deskripsi' => mb_substr($row['deskripsi'], 0, 100),
        'latitude' => (float)$row['latitude'],
        'longitude' => (float)$row['longitude'],
        'status' => $row['status'],
        'alamat' => $row['alamat'],
        'kecamatan' => $row['kecamatan'],
        'tanggal' => date('d M Y', strtotime($row['tanggal_laporan'])),
        'kategori_id' => $row['kategori_id'] ? (int)$row['kategori_id'] : null,
        'nama_kategori' => $row['nama_kategori'],
        'ikon' => $row['ikon']
    ];
}

echo json_encode($laporan);
?>

