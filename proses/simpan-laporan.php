<?php
// =====================================================
// PROSES SIMPAN LAPORAN
// =====================================================
// File: proses/simpan-laporan.php
// Fungsi: Menerima data form, validasi, simpan ke database

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/koneksi.php';


/**
 * @var mysqli $koneksi Database connection
 */

// Header response JSON
header('Content-Type: application/json');

// =====================================================
// FUNCTION: Kirim Response JSON dan Stop Eksekusi
// =====================================================
function response($success, $message, $extra = [])
{
    $data = array_merge([
        'success' => $success,
        'message' => $message
    ], $extra);
    echo json_encode($data);
    exit;
}

// =====================================================
// VALIDASI: Hanya Menerima Method POST
// =====================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'Metode request tidak valid');
}

// =====================================================
// AMBIL & BERSIHKAN INPUT
// =====================================================
$nama_pelapor    = trim($_POST['nama_pelapor'] ?? '');
$kontak_pelapor  = trim($_POST['kontak_pelapor'] ?? '');
$kategori_id     = intval($_POST['kategori_id'] ?? 0);
$judul           = trim($_POST['judul'] ?? '');
$deskripsi       = trim($_POST['deskripsi'] ?? '');
$latitude        = trim($_POST['latitude'] ?? '');
$longitude       = trim($_POST['longitude'] ?? '');
$alamat          = trim($_POST['alamat'] ?? '');
$kecamatan       = trim($_POST['kecamatan'] ?? '');
$kelurahan       = trim($_POST['kelurahan'] ?? '');

// =====================================================
// VALIDASI SERVER-SIDE
// =====================================================

// 1. Validasi nama pelapor
if (empty($nama_pelapor)) {
    response(false, 'Nama pelapor wajib diisi');
}
if (strlen($nama_pelapor) > 255) {
    response(false, 'Nama pelapor terlalu panjang');
}

// 2. Validasi kontak pelapor (email atau nomor HP)
if (empty($kontak_pelapor)) {
    response(false, 'Kontak pelapor wajib diisi');
}

$is_email = filter_var($kontak_pelapor, FILTER_VALIDATE_EMAIL);
$is_phone = preg_match('/^(08|\+628|628)\d{8,11}$/', $kontak_pelapor);

if (!$is_email && !$is_phone) {
    response(false, 'Format email atau nomor HP tidak valid');
}

// 3. Validasi kategori
if ($kategori_id <= 0) {
    response(false, 'Kategori wajib dipilih');
}

// Cek apakah kategori benar-benar ada di database
$stmt = mysqli_prepare($koneksi, "SELECT id FROM kategori WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $kategori_id);
mysqli_stmt_execute($stmt);
$result_kategori_cek = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result_kategori_cek) === 0) {
    response(false, 'Kategori tidak valid');
}
mysqli_stmt_close($stmt);

// 4. Validasi judul
if (empty($judul)) {
    response(false, 'Judul laporan wajib diisi');
}
if (strlen($judul) > 255) {
    response(false, 'Judul laporan maksimal 255 karakter');
}

// 5. Validasi deskripsi
if (empty($deskripsi)) {
    response(false, 'Deskripsi wajib diisi');
}
if (strlen($deskripsi) > 1000) {
    response(false, 'Deskripsi maksimal 1000 karakter');
}

// 6. Validasi lokasi
if (empty($latitude) || empty($longitude)) {
    response(false, 'Lokasi wajib ditentukan');
}
if (!is_numeric($latitude) || !is_numeric($longitude)) {
    response(false, 'Format koordinat lokasi tidak valid');
}

// Validasi range koordinat Padang secara umum (opsional, cukup longgar)
$latitude = floatval($latitude);
$longitude = floatval($longitude);

// 7. Validasi foto
if (empty($_FILES['foto']) || empty($_FILES['foto']['name'][0])) {
    response(false, 'Minimal harus upload 1 foto bukti');
}

$jumlah_foto = count($_FILES['foto']['name']);
if ($jumlah_foto > 5) {
    response(false, 'Maksimal 5 foto');
}

// =====================================================
// VALIDASI FILE FOTO (SEBELUM DISIMPAN)
// =====================================================

$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
$allowed_ext = ['jpg', 'jpeg', 'png'];
$max_size = 5 * 1024 * 1024; // 5 MB

for ($i = 0; $i < $jumlah_foto; $i++) {
    // Cek error upload
    if ($_FILES['foto']['error'][$i] !== UPLOAD_ERR_OK) {
        response(false, 'Terjadi kesalahan saat upload foto ke-' . ($i + 1));
    }

    // Cek ukuran file
    if ($_FILES['foto']['size'][$i] > $max_size) {
        response(false, 'Ukuran foto ke-' . ($i + 1) . ' melebihi 5MB');
    }

    // Cek tipe file menggunakan MIME type asli (bukan dari nama file)
    $tmp_name = $_FILES['foto']['tmp_name'][$i];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        response(false, 'Format foto ke-' . ($i + 1) . ' tidak didukung. Gunakan JPG atau PNG');
    }

    // Cek ekstensi file
    $ext = strtolower(pathinfo($_FILES['foto']['name'][$i], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        response(false, 'Ekstensi foto ke-' . ($i + 1) . ' tidak valid');
    }
}

// =====================================================
// MULAI TRANSAKSI DATABASE
// =====================================================

mysqli_begin_transaction($koneksi);

try {

    // ===== 1. GENERATE KODE LAPORAN =====
    $kode_laporan = generate_kode_laporan();

    // ===== 2. AMBIL USER ID JIKA LOGIN =====
    // ===== 2. AMBIL USER ID JIKA LOGIN =====
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // ===== 3. SIMPAN DATA LAPORAN =====
    $query_insert = "INSERT INTO laporan 
        (kode_laporan, user_id, nama_pelapor, kontak_pelapor, kategori_id, 
         judul, deskripsi, latitude, longitude, alamat, kecamatan, kelurahan, 
         status, prioritas, tanggal_laporan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Verifikasi', 'Sedang', NOW())";

    $stmt = mysqli_prepare($koneksi, $query_insert);

    if (!$stmt) {
        throw new Exception('Gagal menyiapkan query: ' . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sississddsss",
        $kode_laporan,
        $user_id,
        $nama_pelapor,
        $kontak_pelapor,
        $kategori_id,
        $judul,
        $deskripsi,
        $latitude,
        $longitude,
        $alamat,
        $kecamatan,
        $kelurahan
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Gagal menyimpan laporan: ' . mysqli_stmt_error($stmt));
    }

    $laporan_id = mysqli_insert_id($koneksi);
    mysqli_stmt_close($stmt);

    // ===== 4. UPLOAD & SIMPAN FOTO =====
    $upload_dir = '../uploads/laporan/';

    // Pastikan folder ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    for ($i = 0; $i < $jumlah_foto; $i++) {
        $tmp_name = $_FILES['foto']['tmp_name'][$i];
        $original_name = $_FILES['foto']['name'][$i];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        // Generate nama file unik (JANGAN percaya nama file dari user)
        $nama_file_unik = 'laporan_' . $laporan_id . '_' . uniqid() . '_' . time() . '.' . $ext;
        $target_path = $upload_dir . $nama_file_unik;

        // Pindahkan file dari temp ke folder upload
        if (!move_uploaded_file($tmp_name, $target_path)) {
            throw new Exception('Gagal menyimpan foto ke-' . ($i + 1));
        }

        // Simpan data foto ke database
        $query_foto = "INSERT INTO foto_laporan (laporan_id, nama_file, jenis) 
                        VALUES (?, ?, 'bukti_laporan')";
        $stmt_foto = mysqli_prepare($koneksi, $query_foto);
        mysqli_stmt_bind_param($stmt_foto, "is", $laporan_id, $nama_file_unik);

        if (!mysqli_stmt_execute($stmt_foto)) {
            throw new Exception('Gagal menyimpan data foto ke-' . ($i + 1));
        }
        mysqli_stmt_close($stmt_foto);
    }

    // ===== 5. COMMIT TRANSAKSI =====
    mysqli_commit($koneksi);

    // ===== 6. RESPONSE SUKSES =====
    response(true, 'Laporan berhasil dibuat', [
        'kode_laporan' => $kode_laporan,
        'laporan_id' => $laporan_id
    ]);
} catch (Exception $e) {

    // ===== ROLLBACK JIKA ADA ERROR =====
    mysqli_rollback($koneksi);

    // Hapus file yang sudah terupload jika ada error di tengah proses
    if (isset($upload_dir) && isset($nama_file_unik)) {
        $file_to_delete = $upload_dir . $nama_file_unik;
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }

    response(false, 'Terjadi kesalahan: ' . $e->getMessage());
}

