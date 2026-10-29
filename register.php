<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validasi.php';

cek_guest();

$errors = [];
$nama = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifikasi_csrf($token)) {
        die('Invalid CSRF token.');
    }

    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validasi_registrasi($_POST, $koneksi);

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama, $username, $password_hash);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash('success', 'Registrasi berhasil! Silakan masuk dengan akun baru Anda.');
            header("Location: login.php");
            exit;
        } else {
            $errors['umum'] = 'Terjadi kesalahan sistem saat menyimpan data akun.';
            mysqli_stmt_close($stmt);
        }
    }
}

$page_title = 'Registrasi';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-simple-box">
    <div class="auth-brand-header">
        <div class="brand-logo-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <h4 class="fw-bold mb-1">Registrasi Akun</h4>
        <p class="text-muted small mb-0">Daftarkan akun petugas inventaris baru</p>
    </div>

    <?php if (!empty($errors['umum'])): ?>
        <div class="alert alert-danger py-2 small mb-3"><?= escape($errors['umum']) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" autocomplete="off">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= escape($nama) ?>" placeholder="Masukkan nama lengkap" required autofocus>
            <?php if (isset($errors['nama'])): ?>
                <div class="invalid-feedback"><?= escape($errors['nama']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= escape($username) ?>" placeholder="Masukkan username" required>
            <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback"><?= escape($errors['username']) ?></div>
            <?php else: ?>
                <div class="form-text">Gunakan 4-50 karakter kombinasi huruf dan angka.</div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Minimal 6 karakter" required>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= escape($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control <?= isset($errors['konfirmasi_password']) ? 'is-invalid' : '' ?>" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password" required>
            <?php if (isset($errors['konfirmasi_password'])): ?>
                <div class="invalid-feedback"><?= escape($errors['konfirmasi_password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary py-2">Daftar Akun</button>
        </div>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-muted small">Sudah memiliki akun?</span>
        <a href="login.php" class="small fw-semibold text-dark text-decoration-none d-block mt-1">
            Masuk ke sistem
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
