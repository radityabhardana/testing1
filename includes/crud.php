<?php
// Mengambil semua data barang dengan opsi pencarian kata kunci
function ambil_semua_barang($koneksi, $keyword = null) {
    if (!empty($keyword)) {
        $param = '%' . $keyword . '%';
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE kode LIKE ? OR nama LIKE ? OR kategori LIKE ? ORDER BY id DESC");
        mysqli_stmt_bind_param($stmt, "sss", $param, $param, $param);
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM barang ORDER BY id DESC");
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    mysqli_stmt_close($stmt);

    return $data;
}

// Mengambil satu data barang berdasarkan ID
function ambil_barang_by_id($koneksi, $id) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $data;
}

// Menambahkan data barang baru
function tambah_barang($koneksi, $kode, $nama, $kategori, $harga, $stok) {
    $stmt = mysqli_prepare($koneksi, "INSERT INTO barang (kode, nama, kategori, harga, stok) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssdi", $kode, $nama, $kategori, $harga, $stok);
    $hasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $hasil;
}

// Memperbarui data barang
function update_barang($koneksi, $id, $kode, $nama, $kategori, $harga, $stok) {
    $stmt = mysqli_prepare($koneksi, "UPDATE barang SET kode = ?, nama = ?, kategori = ?, harga = ?, stok = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssdii", $kode, $nama, $kategori, $harga, $stok, $id);
    $hasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $hasil;
}

// Menghapus data barang berdasarkan ID
function hapus_barang($koneksi, $id) {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM barang WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $hasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $hasil;
}

// Format badge kategori minimalis & seragam
function get_kategori_badge_class($kategori) {
    return 'badge-category-neutral';
}

// Format status ketersediaan stok fisik
function get_status_stok($stok) {
    if ($stok <= 0) {
        return ['class' => 'status-danger', 'label' => 'Habis'];
    } elseif ($stok <= 5) {
        return ['class' => 'status-warning', 'label' => 'Menipis (' . $stok . ')'];
    } else {
        return ['class' => 'status-safe', 'label' => 'Tersedia'];
    }
}
