<?php
// =====================================================
// REDIRECT DETAIL LAPORAN KE TRACKING
// =====================================================

require_once '../config/koneksi.php';

/**
 * @var mysqli $koneksi Database connection
 */

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $query = "SELECT kode_laporan FROM laporan WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        header('Location: tracking.php?kode=' . urlencode($data['kode_laporan']));
        exit;
    }
}

// Jika tidak ditemukan, kembali ke beranda
header('Location: ../index.php');
exit;
?>

