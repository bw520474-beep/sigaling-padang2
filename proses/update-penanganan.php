<?php
// =====================================================
// PROSES UPDATE PENANGANAN (PETUGAS)
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

if (!is_logged_in() || !is_petugas()) {
    response(false, 'Akses ditolak. Anda harus login sebagai petugas.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'Metode request tidak valid');
}

$petugas_id = get_user_id();
$laporan_id = intval($_POST['laporan_id'] ?? 0);
$keterangan = trim($_POST['keterangan'] ?? '');
$status_penanganan = trim($_POST['status_penanganan'] ?? 'Dalam Proses');

// ===== VALIDASI =====
if ($laporan_id <= 0) {
    response(false, 'ID laporan tidak valid');
}
if (empty($keterangan)) {
    response(false, 'Keterangan penanganan wajib diisi');
}

$status_valid = ['Dalam Proses', 'Selesai'];
if (!in_array($status_penanganan, $status_valid)) {
    response(false, 'Status penanganan tidak valid');
}

// ===== VALIDASI: PASTIKAN PETUGAS INI YANG PUNYA TUGAS =====
$query_cek = "SELECT lo.petugas_id, l.status as status_laporan 
              FROM laporan_opd lo
              JOIN laporan l ON lo.laporan_id = l.id
              WHERE lo.laporan_id = ?
              ORDER BY lo.tanggal_diteruskan DESC LIMIT 1";
$stmt_cek = mysqli_prepare($koneksi, $query_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $laporan_id);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);

if (mysqli_num_rows($result_cek) === 0) {
    response(false, 'Data tugas tidak ditemukan');
}

$data_cek = mysqli_fetch_assoc($result_cek);
mysqli_stmt_close($stmt_cek);

if ($data_cek['petugas_id'] != $petugas_id) {
    response(false, 'Anda tidak memiliki akses ke tugas ini');
}

if ($data_cek['status_laporan'] === 'Selesai') {
    response(false, 'Laporan ini sudah berstatus selesai, tidak bisa diubah lagi');
}

// ===== VALIDASI & UPLOAD FOTO (JIKA ADA) =====
$nama_file_foto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024;
    
    if ($_FILES['foto']['size'] > $max_size) {
        response(false, 'Ukuran foto maksimal 5MB');
    }
    
    $tmp_name = $_FILES['foto']['tmp_name'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        response(false, 'Format foto tidak didukung. Gunakan JPG atau PNG');
    }
    
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        response(false, 'Ekstensi foto tidak valid');
    }
    
    // Upload ke folder penanganan
    $upload_dir = '../uploads/penanganan/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $nama_file_foto = 'penanganan_' . $laporan_id . '_' . uniqid() . '_' . time() . '.' . $ext;
    $target_path = $upload_dir . $nama_file_foto;
    
    if (!move_uploaded_file($tmp_name, $target_path)) {
        response(false, 'Gagal menyimpan foto penanganan');
    }
}

// ===== MULAI TRANSAKSI =====
mysqli_begin_transaction($koneksi);

try {
    // 1. Insert ke tabel penanganan
    $query_insert = "INSERT INTO penanganan (laporan_id, petugas_id, status, keterangan, foto, tanggal_penanganan) 
                      VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt_insert = mysqli_prepare($koneksi, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "iisss", $laporan_id, $petugas_id, $status_penanganan, $keterangan, $nama_file_foto);
    
    if (!mysqli_stmt_execute($stmt_insert)) {
        throw new Exception('Gagal menyimpan data penanganan');
    }
    mysqli_stmt_close($stmt_insert);
    
    // 2. Jika status "Selesai", update status laporan utama juga
    if ($status_penanganan === 'Selesai') {
        $query_update = "UPDATE laporan SET status = 'Selesai' WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $laporan_id);
        
        if (!mysqli_stmt_execute($stmt_update)) {
            throw new Exception('Gagal memperbarui status laporan');
        }
        mysqli_stmt_close($stmt_update);
    } else {
        // Pastikan status laporan tetap "Dalam Penanganan"
        $query_update = "UPDATE laporan SET status = 'Dalam Penanganan' WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $laporan_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }
    
    mysqli_commit($koneksi);
    
    $pesan = ($status_penanganan === 'Selesai') 
        ? 'Laporan berhasil ditandai selesai!' 
        : 'Progress penanganan berhasil disimpan';
    response(true, $pesan);
    
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    
    // Hapus file yang sudah terupload jika ada error
    if ($nama_file_foto && file_exists('../uploads/penanganan/' . $nama_file_foto)) {
        unlink('../uploads/penanganan/' . $nama_file_foto);
    }
    
    response(false, $e->getMessage());
}
?>
