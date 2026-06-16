<?php
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../auth/auth_check.php';
include __DIR__ . '/../../auth/isAdmin.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Tambah User';
$page = 'users';

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tambah User</h2>
            <p class="text-muted mb-0">Tambahkan akun user baru ke sistem AleMart</p>
        </div>
        <a href="index.php" class="btn btn-light border">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width:44px; height:44px;">
                        <i class="bi bi-person-plus fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Data User</h5>
                        <small class="text-muted">Isi data user dengan benar</small>
                    </div>
                </div>

                <?php if (isset($_SESSION['error'])) : ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; ?>
                    </div>
                <?php unset($_SESSION['error']); endif; ?>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold">Avatar User</label>
                        <div class="mb-3">
                            <img id="avatarPreview" src="" class="rounded-circle border object-fit-cover d-none" style="width: 90px; height: 90px;">
                            <div id="avatarDefault" class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 90px; height: 90px; font-size: 2rem;">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatarInput" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Format: JPG, JPEG, PNG</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end">
            <button type="reset" class="btn btn-light border px-4">Reset</button>
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-check-circle"></i> Simpan User
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");
    togglePassword.addEventListener("click", () => {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        togglePassword.innerHTML = type === "password" ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });

    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarDefault = document.getElementById('avatarDefault');

    avatarInput.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
                avatarPreview.classList.remove('d-none');
                avatarDefault.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer_script.php'; ?>