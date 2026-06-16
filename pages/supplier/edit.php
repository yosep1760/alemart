<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Edit Supplier';
$page = 'supplier';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM supplier WHERE id_supplier = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    $_SESSION['error'] = 'Supplier tidak ditemukan!';
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_supplier = trim($_POST['nama_supplier']);
    $no_telp       = trim($_POST['no_telp']);
    $alamat        = trim($_POST['alamat']);

    if (empty($nama_supplier) || empty($no_telp) || empty($alamat)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
    } else {
        $nama_esc = $conn->real_escape_string($nama_supplier);
        $telp_esc = $conn->real_escape_string($no_telp);
        $alamat_esc = $conn->real_escape_string($alamat);

        $update = mysqli_query($conn, "UPDATE supplier SET nama_supplier = '$nama_esc', no_telp = '$telp_esc', alamat = '$alamat_esc' WHERE id_supplier = $id");
        if ($update) {
            $_SESSION['sukses'] = 'Data supplier berhasil diupdate!';
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = 'Gagal mengupdate supplier!';
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
            <h2 class="fw-bold mb-1">Edit Supplier</h2>
            <p class="text-muted mb-0">Ubah data pemasok barang</p>
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
        <div class="card-body p-4">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama_supplier" class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="<?= htmlspecialchars($data['nama_supplier']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="no_telp" class="form-label fw-semibold">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?= htmlspecialchars($data['no_telp']); ?>" required>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Update Data
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