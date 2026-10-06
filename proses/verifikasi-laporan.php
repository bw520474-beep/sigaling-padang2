<?php
// =====================================================
// PROSES VERIFIKASI / TOLAK LAPORAN
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
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// ===== PROTEKSI: HANYA ADMIN =====
if (!is_logged_in() || !is_admin()) {
    response(false, 'Akses ditolak. Anda harus login sebagai admin.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'Metode request tidak valid');
}

$laporan_id = intval($_POST['laporan_id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$prioritas = trim($_POST['prioritas'] ?? 'Sedang');

// ===== VALIDASI =====
if ($laporan_id <= 0) {
    response(false, 'ID laporan tidak valid');
}

$status_valid = ['Terverifikasi', 'Ditolak'];
if (!in_array($status, $status_valid)) {
    response(false, 'Status tidak valid');
}

$prioritas_valid = ['Rendah', 'Sedang', 'Tinggi'];
if (!in_array($prioritas, $prioritas_valid)) {
    $prioritas = 'Sedang';
}

// ===== CEK LAPORAN ADA & MASIH MENUNGGU VERIFIKASI =====
$query_cek = "SELECT status FROM laporan WHERE id = ?";
$stmt_cek = mysqli_prepare($koneksi, $query_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $laporan_id);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);

if (mysqli_num_rows($result_cek) === 0) {
    response(false, 'Laporan tidak ditemukan');
}

$laporan_lama = mysqli_fetch_assoc($result_cek);
mysqli_stmt_close($stmt_cek);

if ($laporan_lama['status'] !== 'Menunggu Verifikasi') {
    response(false, 'Laporan ini sudah pernah diverifikasi sebelumnya');
}

// ===== UPDATE STATUS LAPORAN =====
$query_update = "UPDATE laporan SET status = ?, prioritas = ? WHERE id = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);
mysqli_stmt_bind_param($stmt_update, "ssi", $status, $prioritas, $laporan_id);

if (mysqli_stmt_execute($stmt_update)) {
    $pesan = ($status === 'Terverifikasi') 
        ? 'Laporan berhasil diverifikasi' 
        : 'Laporan berhasil ditolak';
    response(true, $pesan);
} else {
    response(false, 'Gagal memperbarui status laporan');
}

mysqli_stmt_close($stmt_update);
?>

