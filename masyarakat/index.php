<?php
// =====================================================
// HALAMAN BERANDA MASYARAKAT - REDIRECT KE UTAMA
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

// Redirect langsung ke Beranda Utama SIGALING PADANG
header('Location: ../index.php');
exit;
