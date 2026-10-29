<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validasi.php';
require_once __DIR__ . '/includes/crud.php';

cek_login();

// Validasi parameter ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    set_flash('danger', 'ID barang tidak valid.');
    header("Location: barang.php");
    exit;
}

$barang = ambil_barang_by_id($koneksi, $id);

if (!$barang) {
    set_flash('danger', 'Data barang tidak ditemukan.');
    header("Location: barang.php");
    exit;
}

$errors = [];
$kode = $barang['kode'];
$nama = $barang['nama'];
$kategori = $barang['kategori'];
$harga = $barang['harga'];
$stok = $barang['stok'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifikasi_csrf($token)) {
        die('Invalid CSRF token.');
    }

    $kode = strtoupper(trim($_POST['kode'] ?? ''));
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    $input_data = [
        'kode' => $kode,
        'nama' => $nama,
        'kategori' => $kategori,
        'harga' => $harga,
        'stok' => $stok
    ];

    $errors = validasi_barang($input_data, $koneksi, $id);

    if (empty($errors)) {
        $berhasil = update_barang($koneksi, $id, $kode, $nama, $kategori, (float)$harga, (int)$stok);

        if ($berhasil) {
            set_flash('success', 'Data barang [' . $kode . '] berhasil diperbarui.');
            header("Location: barang.php");
            exit;
        } else {
            $errors['umum'] = 'Terjadi kesalahan sistem saat memperbarui data barang.';
        }
    }
}

$page_title = 'Edit Barang';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="barang.php">Data Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="fw-semibold">Edit Data Barang</div>
                <a href="barang.php" class="btn btn-sm btn-outline-secondary">Kembali</a>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors['umum'])): ?>
                    <div class="alert alert-danger py-2 mb-3"><?= escape($errors['umum']) ?></div>
                <?php endif; ?>

                <form method="POST" action="barang_edit.php?id=<?= (int)$id ?>" autocomplete="off">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="kode" class="form-label">Kode Barang / SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace <?= isset($errors['kode']) ? 'is-invalid' : '' ?>" id="kode" name="kode" value="<?= escape($kode) ?>" style="text-transform: uppercase;" required autofocus>
                            <?php if (isset($errors['kode'])): ?>
                                <div class="invalid-feedback"><?= escape($errors['kode']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-7">
                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['kategori']) ? 'is-invalid' : '' ?>" id="kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $opsi_kategori = [
                                    'IT & Komputer',
                                    'Furniture & Perabotan',
                                    'Alat Tulis Kantor (ATK)',
                                    'Elektronik & Presentasi',
                                    'Perlengkapan Ruangan',
                                    'Lainnya'
                                ];
                                foreach ($opsi_kategori as $kat):
                                ?>
                                    <option value="<?= escape($kat) ?>" <?= ($kategori === $kat) ? 'selected' : '' ?>><?= escape($kat) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['kategori'])): ?>
                                <div class="invalid-feedback"><?= escape($errors['kategori']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="nama" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= escape($nama) ?>" required>
                            <?php if (isset($errors['nama'])): ?>
                                <div class="invalid-feedback"><?= escape($errors['nama']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="harga" class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="1" min="1" class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>" id="harga" name="harga" value="<?= escape($harga) ?>" required>
                            </div>
                            <?php if (isset($errors['harga'])): ?>
                                <div class="invalid-feedback d-block"><?= escape($errors['harga']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="stok" class="form-label">Jumlah Stok Fisik <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control <?= isset($errors['stok']) ? 'is-invalid' : '' ?>" id="stok" name="stok" value="<?= escape($stok) ?>" required>
                                <span class="input-group-text">Unit</span>
                            </div>
                            <?php if (isset($errors['stok'])): ?>
                                <div class="invalid-feedback d-block"><?= escape($errors['stok']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="barang.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
