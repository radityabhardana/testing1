<?php
// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menghasilkan token CSRF
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Menampilkan input tersembunyi token CSRF
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . escape(csrf_token()) . '">';
}

// Memverifikasi token CSRF dari request
function verifikasi_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Proteksi halaman yang wajib login
function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Proteksi halaman guest (login/registrasi jika sudah login)
function cek_guest() {
    if (isset($_SESSION['user_id'])) {
        header("Location: dashboard.php");
        exit;
    }
}

// Sanitasi output HTML untuk mencegah XSS
function escape($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Format mata uang Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

// Menyimpan flash message ke session
function set_flash($tipe, $pesan) {
    $_SESSION['flash'] = [
        'tipe' => $tipe,
        'pesan' => $pesan
    ];
}

// Mengambil dan membersihkan flash message dari session
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

