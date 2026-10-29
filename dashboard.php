<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/hitung.php';
require_once __DIR__ . '/includes/crud.php';

cek_login();

// Ambil semua data barang real-time dari database
$daftar_barang = ambil_semua_barang($koneksi);

// Hitung metrik dinamis
$total_barang = count($daftar_barang);
$total_stok = 0;
$total_nilai = 0;
$stok_menipis_count = 0;
$stok_habis_count = 0;

foreach ($daftar_barang as $item) {
    $stok = (int)$item['stok'];
    $harga = (float)$item['harga'];
    
    $total_stok += $stok;
    $total_nilai += ($harga * $stok);

    if ($stok <= 0) {
        $stok_habis_count++;
    } elseif ($stok <= 5) {
        $stok_menipis_count++;
    }
}

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="page-header-title">Dashboard</h2>
        <p class="page-header-subtitle">Ringkasan inventaris dan sarana kerja kantor.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="barang_tambah.php" class="btn btn-primary">
            <i class="bi bi-plus"></i>
            <span>Tambah Barang</span>
        </a>
        <a href="cetak_laporan.php" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i>
            <span>Cetak PDF</span>
        </a>
    </div>
</div>

<!-- 4 KPI Cards: 100% Berkorelasi dengan Data Tabel di Bawah -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-metric-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="kpi-title">Jenis Barang</span>
                <i class="bi bi-box-seam text-muted"></i>
            </div>
            <div class="kpi-number"><?= number_format($total_barang, 0, ',', '.') ?></div>
            <div class="kpi-desc">Total jenis / baris barang</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-metric-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="kpi-title">Total Stok Fisik</span>
                <i class="bi bi-boxes text-muted"></i>
            </div>
            <div class="kpi-number"><?= number_format($total_stok, 0, ',', '.') ?></div>
            <div class="kpi-desc">Akumulasi jumlah unit stok</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-metric-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="kpi-title">Total Valuasi</span>
                <i class="bi bi-wallet2 text-muted"></i>
            </div>
            <div class="kpi-number" style="font-size: 1.3rem;"><?= format_rupiah($total_nilai) ?></div>
            <div class="kpi-desc">Akumulasi nilai seluruh aset</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-metric-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="kpi-title">Perlu Perhatian</span>
                <i class="bi bi-exclamation-circle text-muted"></i>
            </div>
            <div class="kpi-number"><?= $stok_menipis_count + $stok_habis_count ?></div>
            <div class="kpi-desc"><?= $stok_habis_count ?> habis &bull; <?= $stok_menipis_count ?> menipis (&le; 5)</div>
        </div>
    </div>
</div>

<!-- Tabel Lengkap Inventaris -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div class="fw-semibold">
            Daftar Seluruh Barang Inventaris
            <span class="badge bg-light text-muted border ms-1 fw-normal"><?= $total_barang ?> item</span>
        </div>
        <a href="barang.php" class="small text-muted text-decoration-none">Kelola di Data Barang &rarr;</a>
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
                        <th class="text-end" style="width: 140px;">Harga Satuan</th>
                        <th class="text-center" style="width: 80px;">Stok</th>
                        <th class="text-end" style="width: 160px;">Total Nilai</th>
                        <th class="text-center" style="width: 120px;">Status</th>
                        <th class="text-center pe-3" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_barang)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Belum ada data barang di sistem. Silakan klik tombol <strong>Tambah Barang</strong>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftar_barang as $index => $item): 
                            $status_stok = get_status_stok((int)$item['stok']);
                            $subtotal_item = (float)$item['harga'] * (int)$item['stok'];
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
                                <td class="text-end fw-semibold text-dark" style="font-variant-numeric: tabular-nums;">
                                    <?= format_rupiah($subtotal_item) ?>
                                </td>
                                <td class="text-center">
                                    <span class="status-pill <?= $status_stok['class'] ?>">
                                        <?= $status_stok['label'] ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <a href="barang_edit.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($daftar_barang)): ?>
                    <tfoot>
                        <tr class="bg-light fw-bold" style="border-top: 1px solid var(--theme-border);">
                            <td colspan="5" class="ps-3 text-uppercase text-muted py-3">
                                Total Akumulasi (Sesuai 4 Kartu di Atas)
                            </td>
                            <td class="text-center py-3 text-dark" style="font-variant-numeric: tabular-nums;">
                                <?= number_format($total_stok, 0, ',', '.') ?>
                            </td>
                            <td class="text-end py-3 text-dark" style="font-variant-numeric: tabular-nums;">
                                <?= format_rupiah($total_nilai) ?>
                            </td>
                            <td class="text-center py-3 small text-muted">
                                <?= $stok_menipis_count + $stok_habis_count ?> item
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
