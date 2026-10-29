<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
require_once __DIR__ . '/includes/hitung.php';

cek_login();

$keyword = trim($_GET['cari'] ?? '');
$daftar_barang = ambil_semua_barang($koneksi, $keyword);

$page_title = 'Data Barang';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Barang</li>
            </ol>
        </nav>
        <h2 class="page-header-title">Data Barang</h2>
        <p class="page-header-subtitle">Kelola seluruh data inventaris dan sarana kerja kantor.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="cetak_laporan.php" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i>
            <span>Cetak PDF</span>
        </a>
        <a href="barang_tambah.php" class="btn btn-primary">
            <i class="bi bi-plus"></i>
            <span>Tambah Barang</span>
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="barang.php" class="row g-2 align-items-center">
            <div class="col-md-10 col-sm-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" name="cari" placeholder="Cari berdasarkan kode SKU, nama barang, atau kategori..." value="<?= escape($keyword) ?>">
                </div>
            </div>
            <div class="col-md-2 col-sm-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                <?php if (!empty($keyword)): ?>
                    <a href="barang.php" class="btn btn-outline-secondary" title="Reset filter">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div class="fw-semibold">
            Daftar Barang
            <span class="badge bg-light text-muted border ms-1 fw-normal"><?= count($daftar_barang) ?> item</span>
        </div>
        <?php if (!empty($keyword)): ?>
            <span class="small text-muted">Filter: "<?= escape($keyword) ?>"</span>
        <?php endif; ?>
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
                        <th class="text-center" style="width: 120px;">Status</th>
                        <th class="text-center pe-3" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_barang)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <?= !empty($keyword) ? 'Tidak ada data barang yang sesuai dengan kata kunci.' : 'Belum ada data barang.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftar_barang as $index => $item): 
                            $status_stok = get_status_stok((int)$item['stok']);
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
                                <td class="text-center">
                                    <span class="status-pill <?= $status_stok['class'] ?>">
                                        <?= $status_stok['label'] ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="barang_edit.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                            Edit
                                        </a>
                                        <form method="POST" action="barang_hapus.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang <?= escape($item['kode']) ?> - <?= escape($item['nama']) ?>?');" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
