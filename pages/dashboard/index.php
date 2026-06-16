<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page_title = 'Dashboard';
$page = 'dashboard';
include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';

// Get statistics
$total_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM produk"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori"))['total'];
$total_supplier = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()"))['total'];
$total_pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()"))['total'];
$total_pendapatan_hari_ini = $total_pendapatan_hari_ini ? $total_pendapatan_hari_ini : 0;

$stok_tipis_query = mysqli_query($conn, "SELECT nama_produk, stok FROM produk WHERE stok <= 10 ORDER BY stok ASC LIMIT 5");
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard</h2>
            <p class="text-muted mb-0">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? 'User'); ?>!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Total Pendapatan (Hari Ini)</p>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($total_pendapatan_hari_ini, 0, ',', '.'); ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Transaksi Hari Ini</p>
                            <h3 class="mb-0 fw-bold"><?= $total_transaksi; ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-cart-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Total Produk</p>
                            <h3 class="mb-0 fw-bold"><?= $total_produk; ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Total Kategori</p>
                            <h3 class="mb-0 fw-bold"><?= $total_kategori; ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning / Stok Tipis -->
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Peringatan Stok Tipis</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($stok_tipis_query) > 0): ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th class="text-end">Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($stok_tipis_query)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                            <td class="text-end">
                                                <span class="badge bg-danger px-3 py-2 fs-6"><?= $row['stok']; ?></span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <a href="<?= BASE_URL; ?>/pages/produk/index.php" class="text-decoration-none">Lihat semua produk <i class="bi bi-arrow-right"></i></a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                            Stok produk aman.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include '../../includes/footer.php';
include '../../includes/footer_script.php';
?>
