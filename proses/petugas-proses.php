<?php
// =====================================================
// PROSES CRUD PETUGAS (ADMIN)
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
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($nama) || empty($email) || empty($password)) {
        response(false, 'Nama, email, dan password wajib diisi');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response(false, 'Format email tidak valid');
    }
    if (strlen($password) < 6) {
        response(false, 'Password minimal 6 karakter');
    }
    
    // Cek email duplikat
    $query_cek = "SELECT id FROM users WHERE email = ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "s", $email);
    mysqli_stmt_execute($stmt_cek);
    if (mysqli_num_rows(mysqli_stmt_get_result($stmt_cek)) > 0) {
        response(false, 'Email sudah digunakan oleh akun lain');
    }
    mysqli_stmt_close($stmt_cek);
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (nama, email, password, no_hp, role) VALUES (?, ?, ?, ?, 'petugas')";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $password_hash, $no_hp);
    
    if (mysqli_stmt_execute($stmt)) {
        response(true, 'Akun petugas berhasil ditambahkan');
    } else {
        response(false, 'Gagal menambahkan akun petugas');
    }
}

// ===== AKSI: EDIT =====
elseif ($aksi === 'edit') {
    if ($id <= 0) {
        response(false, 'ID petugas tidak valid');
    }
    
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($nama) || empty($email)) {
        response(false, 'Nama dan email wajib diisi');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response(false, 'Format email tidak valid');
    }
    
    // Pastikan target adalah role petugas (bukan admin/masyarakat)
    $query_role = "SELECT role FROM users WHERE id = ?";
    $stmt_role = mysqli_prepare($koneksi, $query_role);
    mysqli_stmt_bind_param($stmt_role, "i", $id);
    mysqli_stmt_execute($stmt_role);
    $result_role = mysqli_stmt_get_result($stmt_role);
    if (mysqli_num_rows($result_role) === 0 || mysqli_fetch_assoc($result_role)['role'] !== 'petugas') {
        response(false, 'Data petugas tidak ditemukan');
    }
    mysqli_stmt_close($stmt_role);
    
    // Cek email duplikat (kecuali diri sendiri)
    $query_cek = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "si", $email, $id);
    mysqli_stmt_execute($stmt_cek);
    if (mysqli_num_rows(mysqli_stmt_get_result($stmt_cek)) > 0) {
        response(false, 'Email sudah digunakan oleh akun lain');
    }
    mysqli_stmt_close($stmt_cek);
    
    if (!empty($password)) {
        if (strlen($password) < 6) {
            response(false, 'Password minimal 6 karakter');
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET nama = ?, email = ?, no_hp = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $nama, $email, $no_hp, $password_hash, $id);
    } else {
        $query = "UPDATE users SET nama = ?, email = ?, no_hp = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $no_hp, $id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        response(true, 'Data petugas berhasil diperbarui');
    } else {
        response(false, 'Gagal memperbarui data petugas');
    }
}

// ===== AKSI: RESET PASSWORD =====
elseif ($aksi === 'reset_password') {
    if ($id <= 0) {
        response(false, 'ID petugas tidak valid');
    }
    
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        response(false, 'Password minimal 6 karakter');
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "UPDATE users SET password = ? WHERE id = ? AND role = 'petugas'";
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
        response(false, 'ID petugas tidak valid');
    }
    
    $query_cek = "SELECT COUNT(*) as jumlah FROM laporan_opd WHERE petugas_id = ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek);
    mysqli_stmt_bind_param($stmt_cek, "i", $id);
    mysqli_stmt_execute($stmt_cek);
    $jumlah = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek))['jumlah'];
    mysqli_stmt_close($stmt_cek);
    
    if ($jumlah > 0) {
        response(false, 'Petugas tidak bisa dihapus karena masih memiliki ' . $jumlah . ' tugas');
    }
    
    $query = "DELETE FROM users WHERE id = ? AND role = 'petugas'";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        response(true, 'Akun petugas berhasil dihapus');
    } else {
        response(false, 'Gagal menghapus akun petugas');
    }
}

else {
    response(false, 'Aksi tidak dikenali');
}
?>