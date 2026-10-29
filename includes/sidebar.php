<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_display_name = $_SESSION['user_nama'] ?? $_SESSION['username'] ?? 'User';
$user_role = 'Petugas Inventaris';
$user_initial = strtoupper(substr($user_display_name, 0, 1));
?>
<aside class="app-sidebar" id="appSidebar">
    <!-- Brand / Logo -->
    <div class="sidebar-brand">
        <a href="dashboard.php" class="brand-link">
            <div class="brand-logo-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="brand-info">
                <span class="brand-name">INKOM</span>
                <span class="brand-sub">Inventaris Kantor</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="sidebar-menu-wrapper">
        <div class="sidebar-section-title">Menu Utama</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="dashboard.php" class="sidebar-nav-link <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="barang.php" class="sidebar-nav-link <?= in_array($current_page, ['barang.php', 'barang_edit.php']) ? 'active' : '' ?>">
                    <i class="bi bi-boxes"></i>
                    <span>Data Barang</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="barang_tambah.php" class="sidebar-nav-link <?= ($current_page === 'barang_tambah.php') ? 'active' : '' ?>">
                    <i class="bi bi-plus-circle"></i>
                    <span>Tambah Barang</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="laporan.php" class="sidebar-nav-link <?= ($current_page === 'laporan.php') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Laporan</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- User Profile & Logout at Bottom -->
    <div class="sidebar-footer">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar"><?= $user_initial ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name" title="<?= escape($user_display_name) ?>"><?= escape($user_display_name) ?></span>
                <span class="sidebar-user-role"><?= $user_role ?></span>
            </div>
            <a href="logout.php" class="sidebar-logout-btn" title="Keluar dari sistem">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>

