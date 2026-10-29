<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validasi.php';
require_once __DIR__ . '/includes/crud.php';

cek_login();

$errors = [];
$kode = '';
$nama = '';
$kategori = '';
$harga = '';
$stok = '';

$opsi_kategori = [
    'IT & Komputer',
    'Furniture & Perabotan',
    'Alat Tulis Kantor (ATK)',
    'Elektronik & Presentasi',
    'Perlengkapan Pantry & Fasilitas',
    'Lainnya'
];

// Siapkan pemetaan kode otomatis untuk tiap kategori
$kode_otomatis_map = [];
foreach ($opsi_kategori as $kat) {
    $kode_otomatis_map[$kat] = generate_kode_otomatis($koneksi, $kat);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifikasi_csrf($token)) {
        die('Invalid CSRF token.');
    }

    $kategori = trim($_POST['kategori'] ?? '');
    $kode = strtoupper(trim($_POST['kode'] ?? ''));
    $nama = trim($_POST['nama'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    // Jika kode kosong tapi kategori terpilih, generate otomatis di server
    if (empty($kode) && !empty($kategori)) {
        $kode = generate_kode_otomatis($koneksi, $kategori);
    }

    $input_data = [
        'kode' => $kode,
        'nama' => $nama,
        'kategori' => $kategori,
        'harga' => $harga,
        'stok' => $stok
    ];

    $errors = validasi_barang($input_data, $koneksi);

    if (empty($errors)) {
        $berhasil = tambah_barang($koneksi, $kode, $nama, $kategori, (float)$harga, (int)$stok);

        if ($berhasil) {
            set_flash('success', 'Barang baru [' . $kode . '] ' . $nama . ' berhasil ditambahkan.');
            header("Location: barang.php");
            exit;
        } else {
            $errors['umum'] = 'Terjadi kesalahan sistem saat menyimpan barang.';
        }
    }
}

$page_title = 'Tambah Barang';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="barang.php">Data Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="fw-semibold">Form Tambah Barang Baru</div>
                <a href="barang.php" class="btn btn-sm btn-outline-secondary">Kembali</a>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors['umum'])): ?>
                    <div class="alert alert-danger py-2 mb-3"><?= escape($errors['umum']) ?></div>
                <?php endif; ?>

                <form method="POST" action="barang_tambah.php" autocomplete="off">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-3">
                        <!-- Kategori ditaruh di awal agar kode otomatis bisa terisi -->
                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['kategori']) ? 'is-invalid' : '' ?>" id="kategori" name="kategori" required autofocus>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($opsi_kategori as $kat): ?>
                                    <option value="<?= escape($kat) ?>" 
                                            data-kode="<?= escape($kode_otomatis_map[$kat]) ?>"
                                            <?= ($kategori === $kat) ? 'selected' : '' ?>>
                                        <?= escape($kat) ?> (Next: <?= escape($kode_otomatis_map[$kat]) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['kategori'])): ?>
                                <div class="invalid-feedback"><?= escape($errors['kategori']) ?></div>
                            <?php else: ?>
                                <div class="form-text">Pilih kategori untuk generate kode SKU otomatis.</div>
                            <?php endif; ?>
                        </div>

                        <!-- Kode SKU otomatis terisi -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="kode" class="form-label mb-0">Kode Barang / SKU <span class="text-danger">*</span></label>
                                <span class="badge bg-light text-success border small" id="badgeOtomatis" style="display: <?= !empty($kode) ? 'inline-block' : 'none' ?>;">
                                    <i class="bi bi-magic me-1"></i>Otomatis
                                </span>
                            </div>
                            <input type="text" 
                                   class="form-control font-monospace <?= isset($errors['kode']) ? 'is-invalid' : '' ?>" 
                                   id="kode" 
                                   name="kode" 
                                   value="<?= escape($kode) ?>" 
                                   placeholder="Pilih kategori terlebih dahulu" 
                                   style="text-transform: uppercase;" 
                                   required>
                            <?php if (isset($errors['kode'])): ?>
                                <div class="invalid-feedback"><?= escape($errors['kode']) ?></div>
                            <?php else: ?>
                                <div class="form-text">Nomor urut otomatis sesuai kategori (bisa diedit jika perlu).</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="nama" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= escape($nama) ?>" placeholder="Contoh: Laptop ThinkPad T14 / Kursi Kerja Ergonomis" required>
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
                                <input type="number" step="1" min="1" class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>" id="harga" name="harga" value="<?= escape($harga) ?>" placeholder="0" required>
                            </div>
                            <?php if (isset($errors['harga'])): ?>
                                <div class="invalid-feedback d-block"><?= escape($errors['harga']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="stok" class="form-label">Jumlah Stok Fisik <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control <?= isset($errors['stok']) ? 'is-invalid' : '' ?>" id="stok" name="stok" value="<?= escape($stok) ?>" placeholder="0" required>
                                <span class="input-group-text">Unit</span>
                            </div>
                            <?php if (isset($errors['stok'])): ?>
                                <div class="invalid-feedback d-block"><?= escape($errors['stok']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="barang.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script Auto-Fill Kode Barang saat Kategori Dipilih
    const kategoriSelect = document.getElementById('kategori');
    const kodeInput = document.getElementById('kode');
    const badgeOtomatis = document.getElementById('badgeOtomatis');

    kategoriSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const autoKode = selectedOption.getAttribute('data-kode');
        
        if (autoKode) {
            kodeInput.value = autoKode;
            if (badgeOtomatis) badgeOtomatis.style.display = 'inline-block';
            
            // Animasi halus highlight input
            kodeInput.style.backgroundColor = '#f0fdf4';
            setTimeout(() => {
                kodeInput.style.backgroundColor = '';
            }, 400);
        } else {
            kodeInput.value = '';
            if (badgeOtomatis) badgeOtomatis.style.display = 'none';
        }
    });

    // Jalankan saat pertama kali dibuka jika kategori sudah terpilih
    if (kategoriSelect.value && !kodeInput.value) {
        const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
        const autoKode = selectedOption.getAttribute('data-kode');
        if (autoKode) {
            kodeInput.value = autoKode;
            if (badgeOtomatis) badgeOtomatis.style.display = 'inline-block';
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
