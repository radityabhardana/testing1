<?php if (isset($_SESSION['user_id'])): ?>
            </main>
            <footer class="app-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        &copy; <?= date('Y') ?> INKOM &mdash; Sistem Inventaris Kantor
                    </div>
                    <div class="text-muted small">
                        <span class="status-indicator-dot"></span> Sistem Aktif
                    </div>
                </div>
            </footer>
        </div> <!-- /.main-content-wrapper -->
    </div> <!-- /.sidebar-wrapper -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<?php else: ?>
        </div> <!-- /.auth-wrapper-inner -->
    </main>
<?php endif; ?>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle for mobile responsive
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        });
    }
    if (backdrop && sidebar) {
        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }
</script>
</body>
</html>
