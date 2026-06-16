<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page_title = 'Data Pembelian';
$page = 'pembelian';

/* ========================= PAGINATION ========================= */
$limit = 10;
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $limit;

/* ========================= SEARCH & FILTER ========================= */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "WHERE 1=1";
if (!empty($search)) {
    $search_esc = $conn->real_escape_string($search);
    $where .= " AND (s.nama_supplier LIKE '%$search_esc%' OR pb.id_pembelian LIKE '%$search_esc%')";
}

/* ========================= TOTAL DATA ========================= */
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pembelian pb JOIN supplier s ON pb.id_supplier = s.id_supplier $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

/* ========================= GET PEMBELIAN ========================= */
$query = mysqli_query($conn,
    "SELECT pb.*, s.nama_supplier, u.nama AS nama_admin
     FROM pembelian pb
     JOIN supplier s ON pb.id_supplier = s.id_supplier
     JOIN users u ON pb.id_user = u.id_user
     $where
     ORDER BY pb.tanggal_pembelian DESC
     LIMIT $limit OFFSET $offset"
);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<?php if (isset($_SESSION['success'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $_SESSION['success']; ?>', showConfirmButton: false, timer: 2000 });
    });
</script>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= $_SESSION['error']; ?>' });
    });
</script>
<?php unset($_SESSION['error']); endif; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Data Pembelian</h2>
            <p class="text-muted mb-0">Riwayat barang masuk dari supplier</p>
        </div>
        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Pembelian Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="searchInput" class="form-control border-start-0"
                            placeholder="Cari ID Pembelian atau Nama Supplier..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pembelian</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Admin</th>
                            <th>Total Pembelian</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><span class="fw-semibold text-primary">#<?= str_pad($row['id_pembelian'], 5, '0', STR_PAD_LEFT); ?></span></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['nama_supplier']); ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_pembelian'])); ?></td>
                                    <td><?= htmlspecialchars($row['nama_admin']); ?></td>
                                    <td class="fw-semibold">Rp <?= number_format($row['total_pembelian'], 0, ',', '.'); ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="detail.php?id=<?= $row['id_pembelian']; ?>" class="btn btn-info btn-sm text-white" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id_pembelian']; ?>" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>Data pembelian tidak ditemukan
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
                                <a class="page-link" href="?page_num=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script>
    const filterForm = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    let searchTimer;
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.submit(), 500);
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Hapus Pembelian?',
                text: `Menghapus data ini juga akan MENGURANGI KEMBALI stok produk yang sudah masuk. Lanjutkan?`,
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
<?php include '../../includes/footer_script.php'; ?>