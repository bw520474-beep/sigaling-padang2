<?php
// =====================================================
// PROSES TERUSKAN LAPORAN KE OPD
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

if (!is_logged_in() || !is_admin()) {
    response(false, 'Akses ditolak. Anda harus login sebagai admin.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'Metode request tidak valid');
}

$laporan_id = intval($_POST['laporan_id'] ?? 0);
$opd_id = intval($_POST['opd_id'] ?? 0);
$catatan = trim($_POST['catatan'] ?? '');

// ===== VALIDASI =====
if ($laporan_id <= 0) {
    response(false, 'ID laporan tidak valid');
}
if ($opd_id <= 0) {
    response(false, 'OPD wajib dipilih');
}

// ===== CEK LAPORAN SUDAH TERVERIFIKASI =====
$query_cek = "SELECT status FROM laporan WHERE id = ?";
$stmt_cek = mysqli_prepare($koneksi, $query_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $laporan_id);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);

if (mysqli_num_rows($result_cek) === 0) {
    response(false, 'Laporan tidak ditemukan');
}

$laporan_data = mysqli_fetch_assoc($result_cek);
mysqli_stmt_close($stmt_cek);

if ($laporan_data['status'] !== 'Terverifikasi') {
    response(false, 'Laporan harus berstatus "Terverifikasi" sebelum diteruskan');
}

// ===== CEK OPD ADA =====
$query_opd = "SELECT id FROM opd WHERE id = ?";
$stmt_opd = mysqli_prepare($koneksi, $query_opd);
mysqli_stmt_bind_param($stmt_opd, "i", $opd_id);
mysqli_stmt_execute($stmt_opd);
if (mysqli_num_rows(mysqli_stmt_get_result($stmt_opd)) === 0) {
    response(false, 'OPD tidak valid');
}
mysqli_stmt_close($stmt_opd);

// ===== MULAI TRANSAKSI =====
mysqli_begin_transaction($koneksi);

try {
    // 1. Insert ke tabel laporan_opd
    $query_insert = "INSERT INTO laporan_opd (laporan_id, opd_id, catatan, tanggal_diteruskan) 
                      VALUES (?, ?, ?, NOW())";
    $stmt_insert = mysqli_prepare($koneksi, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "iis", $laporan_id, $opd_id, $catatan);
    
    if (!mysqli_stmt_execute($stmt_insert)) {
        throw new Exception('Gagal meneruskan laporan ke OPD');
    }
    mysqli_stmt_close($stmt_insert);
    
    // 2. Update status laporan menjadi "Dalam Penanganan"
    $query_update = "UPDATE laporan SET status = 'Dalam Penanganan' WHERE id = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "i", $laporan_id);
    
    if (!mysqli_stmt_execute($stmt_update)) {
        throw new Exception('Gagal memperbarui status laporan');
    }
    mysqli_stmt_close($stmt_update);
    
    mysqli_commit($koneksi);
    response(true, 'Laporan berhasil diteruskan ke OPD');
    
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    response(false, $e->getMessage());
}
?>

