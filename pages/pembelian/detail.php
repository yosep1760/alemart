<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page_title = 'Detail Pembelian';
$page = 'pembelian';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

/* ========================= GET PEMBELIAN ========================= */
$pembelian = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT pb.*, s.nama_supplier, u.nama AS nama_admin
     FROM pembelian pb
     JOIN supplier s ON pb.id_supplier = s.id_supplier
     JOIN users u ON pb.id_user = u.id_user
     WHERE pb.id_pembelian = $id"
));

if (!$pembelian) {
    $_SESSION['error'] = 'Data pembelian tidak ditemukan.';
    header('Location: index.php');
    exit;
}

/* ========================= GET DETAIL ========================= */
$detail_query = mysqli_query($conn,
    "SELECT dp.*, p.nama_produk, p.satuan
     FROM detail_pembelian dp
     JOIN produk p ON dp.id_produk = p.id_produk
     WHERE dp.id_pembelian = $id"
);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Pembelian</h2>
            <p class="text-muted mb-0">#<?= str_pad($pembelian['id_pembelian'], 5, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-muted text-uppercase mb-3">Info Pembelian</h6>
                    <div class="mb-3">
                        <div class="small text-muted">ID Pembelian</div>
                        <div class="fw-bold text-primary fs-5">#<?= str_pad($pembelian['id_pembelian'], 5, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Tanggal Masuk</div>
                        <div class="fw-semibold"><?= date('d M Y, H:i', strtotime($pembelian['tanggal_pembelian'])); ?> WIB</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Supplier</div>
                        <div class="fw-semibold text-capitalize"><?= htmlspecialchars($pembelian['nama_supplier']); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Admin Penerima</div>
                        <div class="fw-semibold"><?= htmlspecialchars($pembelian['nama_admin']); ?></div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Tagihan</span>
                        <span class="fw-bold text-success fs-5">Rp <?= number_format($pembelian['total_pembelian'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold text-muted text-uppercase mb-3">Item yang Diterima</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>No. Batch & Exp</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Beli</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; $grand = 0; while ($d = mysqli_fetch_assoc($detail_query)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($d['nama_produk']); ?></td>
                                        <td>
                                            <span class="d-block small text-muted">Batch: <?= htmlspecialchars($d['no_batch'] ?? '-'); ?></span>
                                            <span class="d-block small text-danger">Exp: <?= $d['expired'] ? date('d M Y', strtotime($d['expired'])) : '-'; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border"><?= $d['jumlah']; ?> <?= $d['satuan']; ?></span>
                                        </td>
                                        <td class="text-end">Rp <?= number_format($d['harga_beli'], 0, ',', '.'); ?></td>
                                        <td class="text-end fw-semibold">Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php $grand += $d['subtotal']; endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="fw-bold text-end">Total Keseluruhan</td>
                                    <td class="fw-bold text-end text-success fs-6">Rp <?= number_format($grand, 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<?php include '../../includes/footer_script.php'; ?>