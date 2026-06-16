<?php
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../auth/auth_check.php';
include __DIR__ . '/../../auth/isAdmin.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Daftar User';
$page = 'users';

$limit = 10;
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) { $current_page = 1; }
$offset = ($current_page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$where = "WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND ( nama LIKE '%$search%' OR username LIKE '%$search%' ) ";
}
if (!empty($role)) {
    $role = $conn->real_escape_string($role);
    $where .= " AND role = '$role'";
}

$sort = $_GET['sort'] ?? 'terbaru';
switch ($sort) {
    case 'nama_asc': $orderBy = "u.nama ASC"; break;
    case 'nama_desc': $orderBy = "u.nama DESC"; break;
    case 'transaksi_desc': $orderBy = "total_transaksi DESC"; break;
    case 'transaksi_asc': $orderBy = "total_transaksi ASC"; break;
    case 'penjualan_desc': $orderBy = "total_penjualan DESC"; break;
    case 'penjualan_asc': $orderBy = "total_penjualan ASC"; break;
    default: $orderBy = "u.id_user DESC";
}

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$query = mysqli_query($conn, "
    SELECT u.*, COUNT(t.id_transaksi) as total_transaksi, COALESCE(SUM(t.total_harga),0) as total_penjualan
    FROM users u
    LEFT JOIN transaksi t ON u.id_user = t.id_user
    $where
    GROUP BY u.id_user
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
");

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
            <h2 class="fw-bold mb-1"> Data User </h2>
            <p class="text-muted mb-0"> Kelola semua user AleMart </p>
        </div>
        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah User
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput" class="form-control border-start-0" placeholder="Cari nama atau username..." value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="role" id="roleFilter" class="form-select">
                        <option value=""> Semua Role </option>
                        <option value="admin" <?= ($role == 'admin') ? 'selected' : ''; ?>> Admin </option>
                        <option value="kasir" <?= ($role == 'kasir') ? 'selected' : ''; ?>> Kasir </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="sort" id="sortFilter" class="form-select">
                        <option value="id_user" <?= ($sort == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="nama_asc" <?= ($sort == 'nama_asc') ? 'selected' : ''; ?>>Nama A-Z</option>
                        <option value="nama_desc" <?= ($sort == 'nama_desc') ? 'selected' : ''; ?>>Nama Z-A</option>
                        <option value="transaksi_desc" <?= ($sort == 'transaksi_desc') ? 'selected' : ''; ?>>Transaksi Terbanyak</option>
                        <option value="transaksi_asc" <?= ($sort == 'transaksi_asc') ? 'selected' : ''; ?>>Transaksi Terkecil</option>
                        <option value="penjualan_desc" <?= ($sort == 'penjualan_desc') ? 'selected' : ''; ?>>Penjualan Terbesar</option>
                        <option value="penjualan_asc" <?= ($sort == 'penjualan_asc') ? 'selected' : ''; ?>>Penjualan Terkecil</option>
                    </select>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Performa</th>
                            <th class="text-center"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no = $offset + 1; while ($user = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td> <?= $no++; ?> </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($user['avatar'])): ?>
                                                <img src="<?= BASE_URL; ?>/assets/uploads/avatar/<?= htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="rounded-circle object-fit-cover border border-2 border-success" style="width: 48px; height: 48px;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center shadow-sm" style=" width: 48px; height: 48px; min-width: 48px; font-size: 16px;">
                                                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($user['nama']); ?></div>
                                                <small class="text-muted"> ID: <?= $user['id_user']; ?> </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['username']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= ($user['role'] == 'admin') ? 'bg-success' : 'bg-primary'; ?>">
                                            <?= ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-semibold text-success">
                                                <i class="bi bi-cart-check"></i> <?= number_format($user['total_transaksi']); ?> transaksi
                                            </div>
                                            <div class="text-muted">
                                                Rp <?= number_format($user['total_penjualan'], 0, ',', '.'); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <?php if ($user['id_user'] !== $_SESSION['id_user']): ?>
                                                <a href="edit.php?id=<?= $user['id_user']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $user['id_user']; ?>" data-nama="<?= htmlspecialchars($user['nama']); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else : ?>
                                                <span class="badge bg-secondary-subtle text-secondary border">Akun Anda</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?> 
                        <?php else: ?> 
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i> Data user tidak ditemukan
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
                                <a class="page-link" href="?page_num=<?= $i; ?>&search=<?= urlencode($search); ?>&role=<?= urlencode($role); ?>&sort=<?= urlencode($sort); ?>"> <?= $i; ?> </a>
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
    const roleFilter = document.getElementById("roleFilter");
    const sortFilter = document.getElementById("sortFilter");

    let searchTimer;
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { filterForm.submit(); }, 500);
    });

    roleFilter.addEventListener("change", () => { filterForm.submit(); });
    sortFilter.addEventListener("change", () => { filterForm.submit(); });

    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            Swal.fire({
                title: 'Hapus?',
                text: `Anda akan menghapus user ${nama}`,
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