<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';

cek_login();

// Hanya menerima metode POST untuk aksi hapus
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Metode request tidak diizinkan.');
    header("Location: barang.php");
    exit;
}

// Verifikasi token CSRF
$token = $_POST['csrf_token'] ?? '';
if (!verifikasi_csrf($token)) {
    die('Invalid CSRF token.');
}

// Validasi ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    set_flash('danger', 'ID barang tidak valid.');
    header("Location: barang.php");
    exit;
}

// Cek apakah barang ada
$barang = ambil_barang_by_id($koneksi, $id);
if (!$barang) {
    set_flash('danger', 'Data barang tidak ditemukan atau sudah dihapus.');
    header("Location: barang.php");
    exit;
}

// Eksekusi hapus
$berhasil = hapus_barang($koneksi, $id);

if ($berhasil) {
    set_flash('success', 'Data barang (' . $barang['kode'] . ' - ' . $barang['nama'] . ') berhasil dihapus.');
} else {
    set_flash('danger', 'Gagal menghapus data barang.');
}

header("Location: barang.php");
exit;

