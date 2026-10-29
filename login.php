<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validasi.php';

cek_guest();

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifikasi_csrf($token)) {
        die('Invalid CSRF token.');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validasi_login($_POST);

    if (empty($errors)) {
        $stmt = mysqli_prepare($koneksi, "SELECT id, nama, username, password FROM users WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['username'] = $user['username'];

            set_flash('success', 'Selamat datang, ' . $user['nama'] . '!');
            header("Location: dashboard.php");
            exit;
        } else {
            $errors['login'] = 'Username atau password yang dimasukkan salah.';
        }
    }
}

$page_title = 'Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-simple-box">
    <div class="auth-brand-header">
        <div class="brand-logo-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <h4 class="fw-bold mb-1">Masuk ke Sistem</h4>
        <p class="text-muted small mb-0">Inventaris Barang & Sarana Kantor</p>
    </div>

    <?php if (!empty($errors['login'])): ?>
        <div class="alert alert-danger py-2 small mb-3"><?= escape($errors['login']) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="off">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= escape($username) ?>" placeholder="Masukkan username" required autofocus>
            <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback"><?= escape($errors['username']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Masukkan password" required>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= escape($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary py-2">Masuk</button>
        </div>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-muted small">Belum punya akun?</span>
        <a href="register.php" class="small fw-semibold text-dark text-decoration-none d-block mt-1">
            Daftar akun baru
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
