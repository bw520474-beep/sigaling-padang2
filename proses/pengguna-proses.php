<?php
// =====================================================
// PROSES RESET PASSWORD & HAPUS PENGGUNA (ADMIN)
// =====================================================

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

header('Content-Type: application/json');

function response($success, $message)
{
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
$current_user_id = get_user_id();

// ===== AKSI: RESET PASSWORD =====
if ($aksi === 'reset_password') {
    if ($id <= 0) {
        response(false, 'ID pengguna tidak valid');
    }

    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        response(false, 'Password minimal 6 karakter');
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $password_hash, $id);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        response(true, 'Password berhasil direset');
    } else {
        response(false, 'Gagal reset password atau data tidak ditemukan');
    }
}

// ===== AKSI: HAPUS =====
elseif ($aksi === 'hapus') {
    if ($id <= 0) {
        response(false, 'ID pengguna tidak valid');
    }

    // ===== PROTEKSI: TIDAK BISA HAPUS DIRI SENDIRI =====
    if ($id == $current_user_id) {
        response(false, 'Anda tidak bisa menghapus akun Anda sendiri');
    }

    // ===== CEK ROLE TARGET =====
    $query_role = "SELECT role FROM users WHERE id = ?";
    $stmt_role = mysqli_prepare($koneksi, $query_role);
    mysqli_stmt_bind_param($stmt_role, "i", $id);
    mysqli_stmt_execute($stmt_role);
    $result_role = mysqli_stmt_get_result($stmt_role);

    if (mysqli_num_rows($result_role) === 0) {
        response(false, 'Pengguna tidak ditemukan');
    }
    $target_role = mysqli_fetch_assoc($result_role)['role'];
    mysqli_stmt_close($stmt_role);

    // ===== PROTEKSI KHUSUS PETUGAS: TIDAK BOLEH HAPUS JIKA MASIH ADA TUGAS =====
    if ($target_role === 'petugas') {
        $query_cek = "SELECT COUNT(*) as jumlah FROM laporan_opd WHERE petugas_id = ?";
        $stmt_cek = mysqli_prepare($koneksi, $query_cek);
        mysqli_stmt_bind_param($stmt_cek, "i", $id);
        mysqli_stmt_execute($stmt_cek);
        $jumlah = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek))['jumlah'];
        mysqli_stmt_close($stmt_cek);

        if ($jumlah > 0) {
            response(false, 'Petugas ini masih memiliki ' . $jumlah . ' tugas, tidak bisa dihapus');
        }
    }

    // ===== PROTEKSI: MINIMAL 1 ADMIN HARUS TETAP ADA =====
    if ($target_role === 'admin') {
        $query_jumlah_admin = "SELECT COUNT(*) as jumlah FROM users WHERE role = 'admin'";
        $jumlah_admin = mysqli_fetch_assoc(mysqli_query($koneksi, $query_jumlah_admin))['jumlah'];

        if ($jumlah_admin <= 1) {
            response(false, 'Tidak bisa menghapus admin terakhir. Sistem harus memiliki minimal 1 admin');
        }
    }

    // ===== HAPUS (untuk masyarakat, laporan tetap ada karena user_id nullable) =====
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        response(true, 'Pengguna berhasil dihapus');
    } else {
        response(false, 'Gagal menghapus pengguna');
    }
} else {
    response(false, 'Aksi tidak dikenali');
}
