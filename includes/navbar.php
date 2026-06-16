<nav class="navbar-custom">
    <div class="navbar-left">

        <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <a href="#" class="navbar-logo">
            <i class="bi bi-cart3"></i>
            AleMart
        </a>

    </div>

    <div class="navbar-right">

        <div class="dropdown">

            <button class="user-dropdown" data-bs-toggle="dropdown">
                
                <?php if (!empty($_SESSION['avatar'])): ?>
                    <img src="<?= BASE_URL; ?>/assets/uploads/avatar/<?= $_SESSION['avatar']; ?>"
                        alt="Avatar"
                        class="rounded-circle object-fit-cover border border-2 border-success"
                        style="width: 48px; height: 48px;">
                <?php else: ?>
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['nama'], 0, 1)); ?>
                    </div>
                <?php endif; ?>

                <div class="user-info d-none d-md-flex">
                    <span class="user-name">
                        <?= $_SESSION['nama']; ?>
                    </span>
                    <span class="user-role">
                        <?= $_SESSION['role']; ?>
                    </span>
                </div>

                <i class="bi bi-chevron-down"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li>
                    <a class="dropdown-item" href="<?= BASE_URL; ?>/pages/profile/index.php">
                        <i class="bi bi-person-circle me-2"></i>
                        Profil
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= BASE_URL; ?>/auth/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>

        </div>

    </div>
</nav>