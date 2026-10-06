<?php
// =====================================================
// HALAMAN REGISTER (MASYARAKAT) - SIGALING PADANG
// =====================================================

require_once '../config/koneksi.php';
require_once '../config/session.php';

/**
 * @var mysqli $koneksi Database connection
 */

// Jika sudah login, redirect
if (is_logged_in()) {
    header('Location: ../index.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
    
    // ===== VALIDASI =====
    if (empty($nama) || empty($email) || empty($password)) {
        $error_message = 'Nama, email, dan password wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal 6 karakter';
    } elseif ($password !== $konfirmasi_password) {
        $error_message = 'Konfirmasi password tidak sesuai';
    } else {
        // Cek email sudah terdaftar atau belum
        $query_cek = "SELECT id FROM users WHERE email = ?";
        $stmt_cek = mysqli_prepare($koneksi, $query_cek);
        mysqli_stmt_bind_param($stmt_cek, "s", $email);
        mysqli_stmt_execute($stmt_cek);
        $result_cek = mysqli_stmt_get_result($stmt_cek);
        
        if (mysqli_num_rows($result_cek) > 0) {
            $error_message = 'Email sudah terdaftar. Silakan login.';
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan user baru dengan role masyarakat
            $query_insert = "INSERT INTO users (nama, email, password, no_hp, role) 
                              VALUES (?, ?, ?, ?, 'masyarakat')";
            $stmt_insert = mysqli_prepare($koneksi, $query_insert);
            mysqli_stmt_bind_param($stmt_insert, "ssss", $nama, $email, $password_hash, $no_hp);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                // Registrasi berhasil, redirect ke login
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error_message = 'Gagal mendaftar. Silakan coba lagi.';
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_cek);
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Masyarakat - SIGALING PADANG</title>
    
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
            max-width: 460px;
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
            width: 100%;
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
        <div style="width: 100%; max-width: 460px;">
            <div class="auth-card">
                <div class="auth-brand">
                    <div class="brand-icon-box">
                        <i data-lucide="user-plus" style="width: 26px; height: 26px;"></i>
                    </div>
                    <h1>Daftar Akun Warga</h1>
                    <p>SIGALING Kota Padang</p>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert-clean alert-clean-danger">
                        <i data-lucide="alert-circle" class="lucide-sm" style="flex-shrink:0; margin-top:2px;"></i>
                        <div><?php echo htmlspecialchars($error_message); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group-clean">
                        <label class="form-label-clean" for="nama">
                            <span>Nama Lengkap</span>
                            <span class="required-star">*</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="user" class="input-icon lucide-sm"></i>
                            <input type="text" id="nama" name="nama" class="form-control" 
                                   placeholder="Nama lengkap Anda" required autofocus
                                   value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="email">
                            <span>Email</span>
                            <span class="required-star">*</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="mail" class="input-icon lucide-sm"></i>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="nama@email.com" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="no_hp">
                            <span>Nomor HP / WhatsApp</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="phone" class="input-icon lucide-sm"></i>
                            <input type="tel" id="no_hp" name="no_hp" class="form-control" 
                                   placeholder="08xxxxxxxxxx"
                                   value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="password">
                            <span>Password</span>
                            <span class="required-star">*</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="lock" class="input-icon lucide-sm"></i>
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        <span class="form-hint-clean">Minimal 6 karakter kombinasi huruf & angka</span>
                    </div>

                    <div class="form-group-clean">
                        <label class="form-label-clean" for="konfirmasi_password">
                            <span>Konfirmasi Password</span>
                            <span class="required-star">*</span>
                        </label>
                        <div class="input-group-clean">
                            <i data-lucide="lock" class="input-icon lucide-sm"></i>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" 
                                   placeholder="Ulangi password di atas" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i data-lucide="user-check" class="lucide-sm"></i> Daftar Sekarang
                    </button>
                </form>

                <div style="text-align: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--color-border); font-size: 0.85rem; color: var(--color-text-muted);">
                    Sudah punya akun? <a href="login.php" style="font-weight: 600;">Masuk di sini</a>
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
