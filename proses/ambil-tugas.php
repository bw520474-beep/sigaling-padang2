<?php
// =====================================================
// PROSES AMBIL TUGAS (PETUGAS)
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

if (!is_logged_in() || !is_petugas()) {
    response(false, 'Akses ditolak. Anda harus login sebagai petugas.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'Metode request tidak valid');
}

$laporan_opd_id = intval($_POST['laporan_opd_id'] ?? 0);
$petugas_id = get_user_id();

if ($laporan_opd_id <= 0) {
    response(false, 'Data tugas tidak valid');
}

// ===== CEK APAKAH TUGAS MASIH TERSEDIA (BELUM DIAMBIL ORANG LAIN) =====
$query_cek = "SELECT petugas_id FROM laporan_opd WHERE id = ?";
$stmt_cek = mysqli_prepare($koneksi, $query_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $laporan_opd_id);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);

if (mysqli_num_rows($result_cek) === 0) {
    response(false, 'Tugas tidak ditemukan');
}

$data = mysqli_fetch_assoc($result_cek);
mysqli_stmt_close($stmt_cek);

if ($data['petugas_id'] !== null) {
    response(false, 'Tugas ini sudah diambil oleh petugas lain');
}

// ===== UPDATE: ASSIGN PETUGAS_ID =====
$query_update = "UPDATE laporan_opd SET petugas_id = ? WHERE id = ?";
$stmt_update = mysqli_prepare($koneksi, $query_update);
mysqli_stmt_bind_param($stmt_update, "ii", $petugas_id, $laporan_opd_id);

if (mysqli_stmt_execute($stmt_update)) {
    response(true, 'Tugas berhasil diambil! Silakan mulai penanganan.');
} else {
    response(false, 'Gagal mengambil tugas');
}

mysqli_stmt_close($stmt_update);
?>
    }
    mysqli_stmt_close($stmt_update);

    // 2. CEK & INSERT RECORD PENANGANAN
    $query_pen = "SELECT id FROM penanganan WHERE laporan_id = ? AND petugas_id = ?";
    $stmt_pen = mysqli_prepare($koneksi, $query_pen);
    mysqli_stmt_bind_param($stmt_pen, "ii", $laporan_id, $petugas_id);
    mysqli_stmt_execute($stmt_pen);
    $res_pen = mysqli_stmt_get_result($stmt_pen);

    if (mysqli_num_rows($res_pen) === 0) {
        $query_ins_pen = "INSERT INTO penanganan (laporan_id, petugas_id, status, keterangan, tanggal_penanganan) 
                          VALUES (?, ?, 'Dalam Proses', 'Tugas telah diambil oleh petugas', NOW())";
        $stmt_ins_pen = mysqli_prepare($koneksi, $query_ins_pen);
        mysqli_stmt_bind_param($stmt_ins_pen, "ii", $laporan_id, $petugas_id);
        if (!mysqli_stmt_execute($stmt_ins_pen)) {
            throw new Exception('Gagal membuat status penanganan tugas');
        }
        mysqli_stmt_close($stmt_ins_pen);
    }
    mysqli_stmt_close($stmt_pen);

    // 3. UPDATE STATUS LAPORAN MENJADI "Dalam Penanganan"
    $query_lap = "UPDATE laporan SET status = 'Dalam Penanganan' WHERE id = ?";
    $stmt_lap = mysqli_prepare($koneksi, $query_lap);
    mysqli_stmt_bind_param($stmt_lap, "i", $laporan_id);
    if (!mysqli_stmt_execute($stmt_lap)) {
        throw new Exception('Gagal memperbarui status laporan');
    }
    mysqli_stmt_close($stmt_lap);

    mysqli_commit($koneksi);
    response(true, 'Tugas berhasil diambil! Silakan mulai penanganan.');
    
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    response(false, $e->getMessage());
}
?>

