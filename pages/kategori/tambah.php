<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Tambah Kategori';
$page = 'kategori';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (empty($nama_kategori)) {
        $_SESSION['error'] = 'Nama kategori wajib diisi!';
    } else {
        // Cek nama kategori sudah ada belum
        $cek = mysqli_query($conn, "SELECT id_kategori FROM kategori WHERE nama_kategori = '" . $conn->real_escape_string($nama_kategori) . "'");
        
        if (mysqli_num_rows($cek) > 0) {
            $_SESSION['error'] = 'Nama kategori sudah ada!';
        } else {
            $query = mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('" . $conn->real_escape_string($nama_kategori) . "')");
            
            if ($query) {
                $_SESSION['sukses'] = 'Kategori berhasil ditambahkan!';
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['error'] = 'Gagal menambahkan kategori!';
            }
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tambah Kategori</h2>
            <p class="text-muted mb-0">Tambahkan kategori produk baru</p>
        </div>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
include __DIR__ . '/../../includes/footer.php';
include __DIR__ . '/../../includes/footer_script.php'; 
?>