<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
require_once __DIR__ . '/includes/hitung.php';
require_once __DIR__ . '/lib/fpdf/fpdf.php';

cek_login();

// Mengambil data barang dan total
$daftar_barang = ambil_semua_barang($koneksi);
$total_barang = hitung_total_barang($koneksi);
$total_stok = hitung_total_stok($koneksi);
$total_nilai = hitung_total_nilai($koneksi);

$petugas = $_SESSION['user_nama'] ?? $_SESSION['username'] ?? 'Petugas';
$tanggal_cetak = date('d-m-Y H:i:s');

// Custom class FPDF untuk header dan footer otomatis
class PDFLaporan extends FPDF {
    function Header() {
        // Judul Laporan
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(190, 7, 'LAPORAN DATA INVENTARIS BARANG', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(190, 6, 'Sistem Informasi Inventaris Barang - Uji Kompetensi', 0, 1, 'C');
        
        // Garis batas kop laporan
        $this->SetLineWidth(0.5);
        $this->Line(10, 24, 200, 24);
        $this->Ln(6);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 10, 'Dicetak dari Sistem Inventaris', 0, 0, 'L');
        $this->Cell(95, 10, 'Halaman ' . $this->PageNo() . ' / {nb}', 0, 0, 'R');
    }
}

// Inisialisasi PDF (A4 Portrait, satuan mm)
$pdf = new PDFLaporan('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Informasi Meta Cetak
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, 'Tanggal Cetak : ' . $tanggal_cetak, 0, 0, 'L');
$pdf->Cell(95, 5, 'Petugas : ' . $petugas, 0, 1, 'R');
$pdf->Ln(3);

// Header Tabel
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 235, 245);
$pdf->SetDrawColor(180, 180, 180);

$pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Kode', 1, 0, 'C', true);
$pdf->Cell(50, 7, 'Nama Barang', 1, 0, 'L', true);
$pdf->Cell(35, 7, 'Kategori', 1, 0, 'L', true);
$pdf->Cell(25, 7, 'Harga (Rp)', 1, 0, 'R', true);
$pdf->Cell(15, 7, 'Stok', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Total (Rp)', 1, 1, 'R', true);

// Isi Data Tabel
$pdf->SetFont('Arial', '', 8.5);
$fill = false;

if (empty($daftar_barang)) {
    $pdf->Cell(190, 10, 'Belum ada data barang.', 1, 1, 'C');
} else {
    $no = 1;
    foreach ($daftar_barang as $row) {
        $subtotal = (float)$row['harga'] * (int)$row['stok'];
        
        // Memastikan teks tidak melebihi lebar cell
        $nama = substr($row['nama'], 0, 30);
        $kategori = substr($row['kategori'], 0, 22);

        $pdf->Cell(10, 6, $no++, 1, 0, 'C');
        $pdf->Cell(25, 6, $row['kode'], 1, 0, 'C');
        $pdf->Cell(50, 6, $nama, 1, 0, 'L');
        $pdf->Cell(35, 6, $kategori, 1, 0, 'L');
        $pdf->Cell(25, 6, number_format($row['harga'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(15, 6, number_format($row['stok'], 0, ',', '.'), 1, 0, 'C');
        $pdf->Cell(30, 6, number_format($subtotal, 0, ',', '.'), 1, 1, 'R');
    }

    // Baris Total
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(120, 7, 'TOTAL KESELURUHAN', 1, 0, 'C', true);
    $pdf->Cell(25, 7, '-', 1, 0, 'C', true);
    $pdf->Cell(15, 7, number_format($total_stok, 0, ',', '.'), 1, 0, 'C', true);
    $pdf->Cell(30, 7, number_format($total_nilai, 0, ',', '.'), 1, 1, 'R', true);
}

// Tanda Tangan Ringkas
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(130, 5, '', 0, 0);
$pdf->Cell(60, 5, 'Dicetak pada: ' . date('d F Y'), 0, 1, 'C');
$pdf->Cell(130, 5, '', 0, 0);
$pdf->Cell(60, 5, 'Penanggung Jawab,', 0, 1, 'C');
$pdf->Ln(18);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(130, 5, '', 0, 0);
$pdf->Cell(60, 5, '( ' . $petugas . ' )', 0, 1, 'C');

// Kirim dokumen ke browser
$pdf->Output('I', 'laporan_data_barang.pdf');
exit;

