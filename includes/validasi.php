<?php
// Validasi data registrasi pengguna baru
function validasi_registrasi($data, $koneksi) {
    $errors = [];

    $nama = trim($data['nama'] ?? '');
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    $konfirmasi_password = $data['konfirmasi_password'] ?? '';

    if (empty($nama)) {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    } elseif (strlen($nama) > 100) {
        $errors['nama'] = 'Nama lengkap maksimal 100 karakter.';
    }

    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (strlen($username) < 4 || strlen($username) > 50) {
        $errors['username'] = 'Username harus terdiri dari 4 sampai 50 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username hanya boleh mengandung huruf, angka, dan underscore.';
    } else {
        // Cek ketersediaan username
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['username'] = 'Username sudah terdaftar, silakan pilih yang lain.';
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if ($password !== $konfirmasi_password) {
        $errors['konfirmasi_password'] = 'Konfirmasi password tidak cocok.';
    }

    return $errors;
}

// Validasi data input form login
function validasi_login($data) {
    $errors = [];

    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi.';
    }

    return $errors;
}

// Validasi data barang (tambah & edit)
function validasi_barang($data, $koneksi, $id_barang = null) {
    $errors = [];

    $kode = trim($data['kode'] ?? '');
    $nama = trim($data['nama'] ?? '');
    $kategori = trim($data['kategori'] ?? '');
    $harga = trim($data['harga'] ?? '');
    $stok = trim($data['stok'] ?? '');

    if (empty($kode)) {
        $errors['kode'] = 'Kode barang wajib diisi.';
    } elseif (strlen($kode) > 20) {
        $errors['kode'] = 'Kode barang maksimal 20 karakter.';
    } else {
        // Cek duplikasi kode barang
        if ($id_barang === null) {
            $stmt = mysqli_prepare($koneksi, "SELECT id FROM barang WHERE kode = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $kode);
        } else {
            $stmt = mysqli_prepare($koneksi, "SELECT id FROM barang WHERE kode = ? AND id != ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "si", $kode, $id_barang);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['kode'] = 'Kode barang sudah terdaftar. Gunakan kode lain.';
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($nama)) {
        $errors['nama'] = 'Nama barang wajib diisi.';
    } elseif (strlen($nama) > 100) {
        $errors['nama'] = 'Nama barang maksimal 100 karakter.';
    }

    if (empty($kategori)) {
        $errors['kategori'] = 'Kategori barang wajib dipilih atau diisi.';
    } elseif (strlen($kategori) > 50) {
        $errors['kategori'] = 'Kategori maksimal 50 karakter.';
    }

    if ($harga === '') {
        $errors['harga'] = 'Harga barang wajib diisi.';
    } elseif (!is_numeric($harga) || (float)$harga <= 0) {
        $errors['harga'] = 'Harga barang harus berupa angka lebih dari 0.';
    }

    if ($stok === '') {
        $errors['stok'] = 'Stok barang wajib diisi.';
    } elseif (!is_numeric($stok) || (int)$stok < 0 || (int)$stok != $stok) {
        $errors['stok'] = 'Stok barang harus berupa bilangan bulat positif atau 0.';
    }

    return $errors;
}

