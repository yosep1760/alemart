<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Daftar Transaksi';
$page = 'transaksi';

/* ========================= PAGINATION ========================= */
$limit = 10;
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $limit;

/* ========================= SEARCH & FILTER ========================= */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tanggal = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : '';
$where = "WHERE 1=1";

if (!empty($search)) {
    $search_esc = $conn->real_escape_string($search);
    $where .= " AND (u.nama LIKE '%$search_esc%' OR t.id_transaksi LIKE '%$search_esc%')";
}
if (!empty($tanggal)) {
    $tanggal_esc = $conn->real_escape_string($tanggal);
    $where .= " AND DATE(t.tanggal_transaksi) = '$tanggal_esc'";
}

/* ========================= TOTAL DATA ========================= */
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi t JOIN users u ON t.id_user = u.id_user $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

/* ========================= GET TRANSAKSI ========================= */
$query = mysqli_query($conn,
    "SELECT t.*, u.nama AS nama_kasir
     FROM transaksi t
     JOIN users u ON t.id_user = u.id_user
     $where
     ORDER BY t.tanggal_transaksi DESC
     LIMIT $limit OFFSET $offset"
);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<?php if (isset($_SESSION['success'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $_SESSION['success']; ?>',
            showConfirmButton: false,
            timer: 2000
        });
    });
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= $_SESSION['error']; ?>'
        });
    });
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Data Transaksi</h2>
            <p class="text-muted mb-0">Kelola semua transaksi penjualan AleMart</p>
        </div>
        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Transaksi Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                            name="search"
                            id="searchInput"
                            class="form-control border-start-0"
                            placeholder="Cari ID transaksi atau nama kasir..."
                            value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="date"
                        name="tanggal"
                        id="tanggalFilter"
                        class="form-control"
                        value="<?= htmlspecialchars($tanggal); ?>">
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Transaksi</th>
                            <th>Kasir</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no = $offset + 1;
                            while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="fw-semibold text-success">
                                            #<?= str_pad($row['id_transaksi'], 5, '0', STR_PAD_LEFT); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($row['nama_kasir']); ?></div>
                                    </td>
                                    <td>
                                        <?= date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?>
                                    </td>
                                    <td class="fw-semibold">
                                        Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        Rp <?= number_format($row['bayar'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        Rp <?= number_format($row['kembalian'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="detail.php?id=<?= $row['id_transaksi']; ?>"
                                                class="btn btn-info btn-sm text-white"
                                                title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-delete"
                                                    data-id="<?= $row['id_transaksi']; ?>"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Data transaksi tidak ditemukan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-end">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($current_page == $i) ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?page_num=<?= $i; ?>&search=<?= urlencode($search); ?>&tanggal=<?= urlencode($tanggal); ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
    const filterForm = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    const tanggalFilter = document.getElementById("tanggalFilter");

    let searchTimer;
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.submit(), 500);
    });
    tanggalFilter.addEventListener("change", () => filterForm.submit());

    /* ========================= DELETE ========================= */
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: `Transaksi #${String(id).padStart(5, '0')} akan dihapus permanen`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `hapus.php?id=${id}`;
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../../includes/footer_script.php'; ?>