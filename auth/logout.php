<?php
// =====================================================
// PROSES LOGOUT
// =====================================================

require_once '../config/session.php';

// Hapus semua session
destroy_session();

// Redirect ke beranda
header('Location: ../index.php');
exit;
