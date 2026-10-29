<?php
// Menghitung jumlah jenis barang yang tersimpan
function hitung_total_barang($koneksi) {
    $sql = "SELECT COUNT(*) AS total FROM barang";
    $query = mysqli_query($koneksi, $sql);
    $data = mysqli_fetch_assoc($query);
    return (int)($data['total'] ?? 0);
}

// Menghitung akumulasi jumlah stok seluruh barang
function hitung_total_stok($koneksi) {
    $sql = "SELECT COALESCE(SUM(stok), 0) AS total FROM barang";
    $query = mysqli_query($koneksi, $sql);
    $data = mysqli_fetch_assoc($query);
    return (int)($data['total'] ?? 0);
}

// Menghitung akumulasi total nilai inventaris (harga * stok)
function hitung_total_nilai($koneksi) {
    $sql = "SELECT COALESCE(SUM(harga * stok), 0) AS total FROM barang";
    $query = mysqli_query($koneksi, $sql);
    $data = mysqli_fetch_assoc($query);
    return (float)($data['total'] ?? 0);
}

