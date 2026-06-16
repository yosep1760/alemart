<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Ubah Password';

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h3 class="fw-bold mb-1">Ubah Password</h3>
                        <p class="text-muted mb-0">Pastikan password baru mudah diingat</p>
                    </div>

                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; ?>
                        </div>
                    <?php unset($_SESSION['error']); endif; ?>

                    <form action="proses_ubah_password.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Lama</label>
                            <div class="input-group">
                                <input type="password" name="password_lama" id="password_lama" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_lama">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="password_baru" id="password_baru" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_baru">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="konfirmasi_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Simpan Password
                            </button>
                            <a href="index.php" class="btn btn-light border">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<?php include __DIR__ . '/../../includes/footer_script.php'; ?>