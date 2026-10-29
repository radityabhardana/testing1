<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
require_once __DIR__ . '/includes/hitung.php';

cek_login();

$daftar_barang = ambil_semua_barang($koneksi);
$total_barang = hitung_total_barang($koneksi);
$total_stok = hitung_total_stok($koneksi);
$total_nilai = hitung_total_nilai($koneksi);

$page_title = 'Laporan Inventaris';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>
        <h2 class="page-header-title">Laporan Inventaris Barang</h2>
        <p class="page-header-subtitle">Rekapitulasi seluruh data barang dan total nilai inventaris kantor.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="cetak_laporan.php" target="_blank" class="btn btn-primary">
            <i class="bi bi-printer"></i>
            <span>Cetak PDF</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-metric-card h-100">
            <span class="kpi-title">Jenis Barang</span>
            <div class="kpi-number"><?= number_format($total_barang, 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">Item</span></div>
            <div class="kpi-desc">Total jenis barang terdaftar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-metric-card h-100">
            <span class="kpi-title">Total Stok Fisik</span>
            <div class="kpi-number"><?= number_format($total_stok, 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">Unit</span></div>
            <div class="kpi-desc">Akumulasi jumlah seluruh unit</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-metric-card h-100">
            <span class="kpi-title">Total Nilai Inventaris</span>
            <div class="kpi-number" style="font-size: 1.35rem;"><?= format_rupiah($total_nilai) ?></div>
            <div class="kpi-desc">Akumulasi nilai aset kantor</div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div class="fw-semibold">Rincian Data Inventaris</div>
        <span class="small text-muted">Tanggal: <?= date('d/m/Y') ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table clean-table align-middle">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th style="width: 140px;">Kode SKU</th>
                        <th>Nama Barang</th>
                        <th style="width: 180px;">Kategori</th>
                        <th class="text-end" style="width: 150px;">Harga Satuan</th>
                        <th class="text-center" style="width: 80px;">Stok</th>
                        <th class="text-end pe-3" style="width: 180px;">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_barang)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data barang untuk dilaporkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftar_barang as $index => $item): 
                            $subtotal = (float)$item['harga'] * (int)$item['stok'];
                        ?>
                            <tr>
                                <td class="ps-3 text-muted small"><?= $index + 1 ?></td>
                                <td>
                                    <span class="badge-sku"><?= escape($item['kode']) ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= escape($item['nama']) ?></span>
                                </td>
                                <td>
                                    <span class="badge-category-neutral"><?= escape($item['kategori']) ?></span>
                                </td>
                                <td class="text-end" style="font-variant-numeric: tabular-nums;">
                                    <?= format_rupiah($item['harga']) ?>
                                </td>
                                <td class="text-center fw-semibold" style="font-variant-numeric: tabular-nums;">
                                    <?= number_format($item['stok'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end pe-3 fw-semibold text-dark" style="font-variant-numeric: tabular-nums;">
                                    <?= format_rupiah($subtotal) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($daftar_barang)): ?>
                    <tfoot>
                        <tr class="bg-light fw-bold" style="border-top: 1px solid var(--theme-border);">
                            <td colspan="5" class="ps-3 text-uppercase text-muted py-3">Total Keseluruhan</td>
                            <td class="text-center py-3 text-dark" style="font-variant-numeric: tabular-nums;">
                                <?= number_format($total_stok, 0, ',', '.') ?>
                            </td>
                            <td class="text-end pe-3 py-3 text-dark fs-6" style="font-variant-numeric: tabular-nums;">
                                <?= format_rupiah($total_nilai) ?>
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
