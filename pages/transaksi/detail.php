<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Detail Transaksi';
$page = 'transaksi';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

/* ========================= GET TRANSAKSI ========================= */
$transaksi = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT t.*, u.nama AS nama_kasir
     FROM transaksi t
     JOIN users u ON t.id_user = u.id_user
     WHERE t.id_transaksi = $id"
));

if (!$transaksi) {
    $_SESSION['error'] = 'Transaksi tidak ditemukan.';
    header('Location: index.php');
    exit;
}

/* ========================= GET DETAIL ========================= */
$detail_query = mysqli_query($conn,
    "SELECT dt.*, p.nama_produk, p.satuan
     FROM detail_transaksi dt
     JOIN produk p ON dt.id_produk = p.id_produk
     WHERE dt.id_transaksi = $id"
);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Detail Transaksi</h2>
            <p class="text-muted mb-0">
                #<?= str_pad($transaksi['id_transaksi'], 5, '0', STR_PAD_LEFT); ?>
            </p>
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
                    <h6 class="fw-bold text-muted text-uppercase mb-3">Info Transaksi</h6>

                    <div class="mb-3">
                        <div class="small text-muted">ID Transaksi</div>
                        <div class="fw-bold text-success fs-5">
                            #<?= str_pad($transaksi['id_transaksi'], 5, '0', STR_PAD_LEFT); ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted">Tanggal</div>
                        <div class="fw-semibold">
                            <?= date('d M Y, H:i', strtotime($transaksi['tanggal_transaksi'])); ?> WIB
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted">Kasir</div>
                        <div class="fw-semibold"><?= htmlspecialchars($transaksi['nama_kasir']); ?></div>
                    </div>

                    <hr>

                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Total Harga</span>
                        <span class="fw-bold text-success">
                            Rp <?= number_format($transaksi['total_harga'], 0, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Bayar</span>
                        <span class="fw-semibold">
                            Rp <?= number_format($transaksi['bayar'], 0, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Kembalian</span>
                        <span class="fw-bold text-primary">
                            Rp <?= number_format($transaksi['kembalian'], 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold text-muted text-uppercase mb-3">Item yang Dibeli</h6>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                $grand = 0;
                                while ($d = mysqli_fetch_assoc($detail_query)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($d['nama_produk']); ?></td>
                                        <td class="text-center">
                                            <?= $d['jumlah']; ?> <?= $d['satuan']; ?>
                                        </td>
                                        <td class="text-end">
                                            Rp <?= number_format($d['harga_jual'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?>
                                        </td>
                                    </tr>
                                    <?php $grand += $d['subtotal'];
                                endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="4" class="fw-bold text-end">Total</td>
                                    <td class="fw-bold text-end text-success">
                                        Rp <?= number_format($grand, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<?php include __DIR__ . '/../../includes/footer_script.php'; ?>