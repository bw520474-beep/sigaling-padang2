<?php
// =====================================================
// HALAMAN LOGIN - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

// Jika sudah login, redirect sesuai role
if (is_logged_in()) {
    $role = get_user_role();
    if ($role === 'admin') {
        header('Location: ../admin/index.php');
        exit;
    } elseif ($role === 'petugas') {
        header('Location: ../petugas/index.php');
        exit;
    } else {
        header('Location: ../index.php');
        exit;
    }
}

$error_message = '';
$success_message = '';

// Cek pesan dari redirect (misal setelah register berhasil)
if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $success_message = 'Registrasi berhasil! Silakan login dengan akun Anda.';
}

// Cek pesan timeout session
if (isset($_SESSION['timeout_message'])) {
    $error_message = $_SESSION['timeout_message'];
    unset($_SESSION['timeout_message']);
}

// ===== RATE LIMITING: CEGAH BRUTE FORCE =====
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_locked_until'] = 0;
}

$is_locked = $_SESSION['login_locked_until'] > time();

// ===== PROSES LOGIN =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = 'Email dan password wajib diisi';
    } else {
        // Cari user berdasarkan email
        $query = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil, set session
                set_login_session($user['id'], $user['nama'], $user['email'], $user['role']);
                $_SESSION['login_attempts'] = 0; // reset percobaan gagal

                // Redirect sesuai role
                if ($user['role'] === 'admin') {
                    header('Location: ../admin/index.php');
                    exit;
                } elseif ($user['role'] === 'petugas') {
                    header('Location: ../petugas/index.php');
                    exit;
                } else {
                    header('Location: ../index.php');
                    exit;
                }
            } else {
                $_SESSION['login_attempts']++;
                $error_message = 'Email atau password salah';
            }
        } else {
            $_SESSION['login_attempts']++;
            $error_message = 'Email atau password salah';
        }
        mysqli_stmt_close($stmt);

        // Kunci akun sementara setelah 5 kali gagal
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 300; // kunci 5 menit
            $_SESSION['login_attempts'] = 0;
        }
    }
} elseif ($is_locked) {
    $sisa_detik = $_SESSION['login_locked_until'] - time();
    $sisa_menit = ceil($sisa_detik / 60);
    $error_message = "Terlalu banyak percobaan gagal. Silakan coba lagi dalam $sisa_menit menit.";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - SIGALING PADANG</title>
    
    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            background: linear-gradient(180deg, #F0FDF4 0%, #F8FAFC 100%);
        }

        .auth-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-brand .brand-icon-box {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            background-color: var(--color-primary-light);
            color: var(--color-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }

        .auth-brand h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--color-text-main);
            letter-spacing: -0.02em;
            margin-bottom: 0.2rem;
        }

        .auth-brand p {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            margin: 0;
        }

        .input-group-clean {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-clean .input-icon {
            position: absolute;
            left: 0.85rem;
            color: var(--color-text-subtle);
            pointer-events: none;
        }

        .input-group-clean input {
            padding-left: 2.5rem !important;
            padding-right: 2.5rem !important;
            width: 100%;
        }

        .input-group-clean .btn-toggle-pw {
            position: absolute;
            right: 0.75rem;
            background: none;
            border: none;
            color: var(--color-text-subtle);
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-group-clean .btn-toggle-pw:hover {
            color: var(--color-text-main);
        }

        .auth-info-box {
            background-color: var(--color-surface-subtle);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 0.75rem 0.85rem;
            margin-top: 1.25rem;
            font-size: 0.8rem;
            color: var(--color-text-muted);
            line-height: 1.4;
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .auth-info-box i {
            color: var(--color-primary);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .back-link {
            text-align: center;
            margin-top: 1.25rem;
        }

        .back-link a {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        .back-link a:hover {
            color: var(--color-primary);
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 1.5rem 1.25rem;
                border-radius: var(--radius-lg);
            }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <div style="width: 100%; max-width: 420px;">
            <div class="auth-card">
                <div class="auth-brand">
                    <div class="brand-icon-box">
                        <i data-lucide="leaf" style="width: 26px; height: 26px;"></i>
                    </div>
                    <h1>SIGALING PADANG</h1>
                    <p>Sistem Pelaporan Lingkungan Kota Padang</p>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert-clean alert-clean-danger">
                        <i data-lucide="alert-circle" class="lucide-sm" style="flex-shrink:0; margin-top:2px;"></i>
                        <div><?php echo htmlspecialchars($error_message); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert-clean alert-clean-success">
                        <i data-lucide="check-circle" class="lucide-sm" style="flex-shrink:0; margin-top:2px;"></i>
                        <div><?php echo htmlspecialchars($success_message); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="email">
                            <span>Email</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="mail" class="input-icon lucide-sm"></i>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="nama@email.com" required autofocus
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="password">
                            <span>Password</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="lock" class="input-icon lucide-sm"></i>
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="••••••••" required>
                            <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('password', 'pw-icon')" aria-label="Lihat password">
                                <i data-lucide="eye" id="pw-icon" class="lucide-sm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i data-lucide="log-in" class="lucide-sm"></i> Masuk ke Sistem
                    </button>
                </form>

                <div style="text-align: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--color-border); font-size: 0.85rem; color: var(--color-text-muted);">
                    Belum memiliki akun? <a href="register.php" style="font-weight: 600;">Daftar di sini</a>
                </div>

                <div class="auth-info-box">
                    <i data-lucide="info" class="lucide-sm"></i>
                    <div>
                        <strong>Catatan:</strong> Masyarakat dapat membuat laporan publik tanpa harus login. Akun berguna untuk memantau riwayat laporan Anda.
                    </div>
                </div>
            </div>

            <div class="back-link">
                <a href="../index.php">
                    <i data-lucide="arrow-left" class="lucide-sm"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/script.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
