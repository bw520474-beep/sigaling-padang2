<?php
// =====================================================
// PROSES CRUD KATEGORI (ADMIN)
// =====================================================

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

header('Content-Type: application/json');

function response($success, $message) {
    echo json_encode(['success' => $success, 'status' => $success ? 'success' : 'error', 'message' => $message]);
    exit;
}

if (!is_logged_in() || !is_admin()) {
    response(false, 'Akses ditolak. Anda harus login sebagai admin.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'Metode request tidak valid');
}

$aksi = trim($_POST['aksi'] ?? '');
$id = intval($_POST['id'] ?? 0);

// ===== AKSI: TAMBAH =====
if ($aksi === 'tambah') {
    $nama = trim($_POST['nama_kategori'] ?? '');
    $ikon = trim($_POST['ikon'] ?? '📋');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    if (empty($nama)) {
        response(false, 'Nama kategori wajib diisi');
    }
    if (strlen($nama) > 100) {
        response(false, 'Nama kategori maksimal 100 karakter');
    }
    
    $query_cek = "SELECT id FROM kategori WHERE nama_kategori = ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "s", $nama);
    mysqli_stmt_execute($stmt_cek);
    if (mysqli_num_rows(mysqli_stmt_get_result($stmt_cek)) > 0) {
        response(false, 'Kategori dengan nama tersebut sudah ada');
    }
    mysqli_stmt_close($stmt_cek);
    
    $query = "INSERT INTO kategori (nama_kategori, ikon, deskripsi) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sss", $nama, $ikon, $deskripsi);
    
    if (mysqli_stmt_execute($stmt)) {
        response(true, 'Kategori berhasil ditambahkan');
    } else {
        response(false, 'Gagal menambahkan kategori');
    }
}

// ===== AKSI: EDIT =====
elseif ($aksi === 'edit') {
    if ($id <= 0) {
        response(false, 'ID kategori tidak valid');
    }
    
    $nama = trim($_POST['nama_kategori'] ?? '');
    $ikon = trim($_POST['ikon'] ?? '📋');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    if (empty($nama)) {
        response(false, 'Nama kategori wajib diisi');
    }
    
    $query_cek = "SELECT id FROM kategori WHERE nama_kategori = ? AND id != ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "si", $nama, $id);
    mysqli_stmt_execute($stmt_cek);
    if (mysqli_num_rows(mysqli_stmt_get_result($stmt_cek)) > 0) {
        response(false, 'Kategori dengan nama tersebut sudah ada');
    }
    mysqli_stmt_close($stmt_cek);
    
    $query = "UPDATE kategori SET nama_kategori = ?, ikon = ?, deskripsi = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $ikon, $deskripsi, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        response(true, 'Kategori berhasil diperbarui');
    } else {
        response(false, 'Gagal memperbarui kategori');
    }
}

// ===== AKSI: HAPUS =====
elseif ($aksi === 'hapus') {
    if ($id <= 0) {
        response(false, 'ID kategori tidak valid');
    }
    
    $query_cek = "SELECT COUNT(*) as jumlah FROM laporan WHERE kategori_id = ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "i", $id);
    mysqli_stmt_execute($stmt_cek);
    $jumlah = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek))['jumlah'];
    mysqli_stmt_close($stmt_cek);
    
    if ($jumlah > 0) {
        response(false, 'Kategori tidak bisa dihapus karena masih dipakai oleh ' . $jumlah . ' laporan');
    }
    
    $query = "DELETE FROM kategori WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        response(true, 'Kategori berhasil dihapus');
    } else {
        response(false, 'Gagal menghapus kategori');
    }
}

else {
    response(false, 'Aksi tidak dikenali');
}
?>