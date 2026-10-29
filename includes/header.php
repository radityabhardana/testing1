<?php
if (!isset($page_title)) {
    $page_title = 'Sistem Inventaris Barang';
}
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($page_title) ?> - INKOM</title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= $is_logged_in ? 'app-has-sidebar' : 'app-guest' ?>">

<?php if ($is_logged_in): ?>
    <div class="sidebar-wrapper">
        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <div class="main-content-wrapper">
            <!-- Mobile Topbar -->
            <header class="app-mobile-topbar d-lg-none">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam text-dark"></i>
                    <span class="fw-bold text-dark">INKOM</span>
                </div>
                <a href="logout.php" class="text-muted small text-decoration-none">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </a>
            </header>

            <main class="app-main-content">
                <?php
                $flash = get_flash();
                if ($flash):
                    $alert_icon = 'bi-info-circle';
                    if ($flash['tipe'] === 'success') $alert_icon = 'bi-check-circle';
                    if ($flash['tipe'] === 'danger') $alert_icon = 'bi-exclamation-circle';
                    if ($flash['tipe'] === 'warning') $alert_icon = 'bi-exclamation-triangle';
                ?>
                    <div class="alert alert-<?= escape($flash['tipe']) ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-none" role="alert">
                        <i class="bi <?= $alert_icon ?> fs-5"></i>
                        <div class="flex-grow-1"><?= escape($flash['pesan']) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
<?php else: ?>
    <main class="app-guest-content">
        <div class="auth-wrapper-inner">
            <?php
            $flash = get_flash();
            if ($flash):
                $alert_icon = 'bi-info-circle';
                if ($flash['tipe'] === 'success') $alert_icon = 'bi-check-circle';
                if ($flash['tipe'] === 'danger') $alert_icon = 'bi-exclamation-circle';
                if ($flash['tipe'] === 'warning') $alert_icon = 'bi-exclamation-triangle';
            ?>
                <div class="alert alert-<?= escape($flash['tipe']) ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-none w-100" role="alert">
                    <i class="bi <?= $alert_icon ?>"></i>
                    <div class="flex-grow-1"><?= escape($flash['pesan']) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
<?php endif; ?>
