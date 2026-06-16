<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="bi bi-shop"></i>
            <span>AleMart</span>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-menu">

        <span class="sidebar-title">
            Main Menu
        </span>

        <a href="<?= BASE_URL; ?>/pages/dashboard/index.php"
            class="sidebar-link <?= ($page == 'dashboard') ? 'aktif' : ''; ?>">
            <i class="bi bi-grid"></i>
            Dashboard
        </a>

        <a href="<?= BASE_URL; ?>/pages/produk/index.php"
            class="sidebar-link <?= ($page == 'produk') ? 'aktif' : ''; ?>">
            <i class="bi bi-box-seam"></i>
            Produk
        </a>

        <a href="<?= BASE_URL; ?>/pages/kategori/index.php"
            class="sidebar-link <?= ($page == 'kategori') ? 'aktif' : ''; ?>">
            <i class="bi bi-tags"></i>
            Kategori
        </a>

        <a href="<?= BASE_URL; ?>/pages/supplier/index.php"
            class="sidebar-link <?= ($page == 'supplier') ? 'aktif' : ''; ?>">
            <i class="bi bi-truck"></i>
            Supplier
        </a>

        <a href="<?= BASE_URL; ?>/pages/pembelian/index.php"
            class="sidebar-link <?= ($page == 'pembelian') ? 'aktif' : ''; ?>">
            <i class="bi bi-bag"></i>
            Pembelian
        </a>

        <a href="<?= BASE_URL; ?>/pages/transaksi/index.php"
            class="sidebar-link <?= ($page == 'transaksi') ? 'aktif' : ''; ?>">
            <i class="bi bi-cash-stack"></i>
            Transaksi
        </a>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="<?= BASE_URL; ?>/pages/users/index.php"
                class="sidebar-link <?= ($page == 'users') ? 'aktif' : ''; ?>">
                <i class="bi bi-people"></i>
                User
            </a>
        <?php endif; ?>

    </div>

</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>