<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Profil Saya';

$id = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id'");
$user = mysqli_fetch_assoc($query);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $_SESSION['success']; ?>', showConfirmButton: false, timer: 2000 });
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= $_SESSION['error']; ?>' });
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Profil Saya</h2>
            <p class="text-muted mb-0">Kelola informasi akun Anda</p>
        </div>
        <a href="edit.php" class="btn btn-success rounded-3 px-4">
            <i class="bi bi-pencil-square me-2"></i> Edit Profil
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <?php
        $coverBg = !empty($user['avatar']) ? BASE_URL . '/assets/uploads/avatar/' . $user['avatar'] : BASE_URL . '/assets/img/default-cover.jpg';
        ?>
        <div class="p-4 p-lg-5 text-white position-relative overflow-hidden"
            style="min-height: 240px; background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('<?= $coverBg ?>'); background-size: cover; background-position: center;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="backdrop-filter: blur(4px); z-index: 0;"></div>

            <div class="position-relative z-1">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= BASE_URL; ?>/assets/uploads/avatar/<?= htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="rounded-circle border border-4 border-white object-fit-cover shadow" style="width: 110px; height: 110px;">
                    <?php else: ?>
                        <div class="rounded-circle bg-white text-success fw-bold d-flex align-items-center justify-content-center shadow" style="width: 110px; height: 110px; font-size: 40px;">
                            <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <h3 class="fw-bold mb-1 text-capitalize"><?= htmlspecialchars($user['nama']); ?></h3>
                        <div class="opacity-75 mb-2">@<?= htmlspecialchars($user['username']); ?></div>
                        <span class="badge bg-light text-success rounded-pill px-3 py-2">
                            <i class="bi bi-person-badge me-1"></i> <?= ucfirst($user['role']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            <h5 class="fw-bold mb-4">Informasi Akun</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 220px;">ID User</td>
                            <td>#<?= $user['id_user']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nama Lengkap</td>
                            <td><?= htmlspecialchars($user['nama']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Username</td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Role</td>
                            <td>
                                <span class="badge rounded-pill <?= ($user['role'] == 'admin') ? 'bg-success' : 'bg-primary'; ?>">
                                    <?= ucfirst($user['role']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Password</td>
                            <td>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <span id="passwordText">••••••••••••</span>
                                    <a href="ubah_password.php" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-key"></i> Ubah Password
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let visible = false;
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordText = document.getElementById('passwordText');
        if (!visible) {
            passwordText.innerHTML = 'Password disembunyikan';
            this.innerHTML = '<i class="bi bi-eye-slash"></i> Sembunyikan';
            visible = true;
        } else {
            passwordText.innerHTML = '••••••••••••';
            this.innerHTML = '<i class="bi bi-eye"></i> Lihat';
            visible = false;
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<?php include __DIR__ . '/../../includes/footer_script.php'; ?>