<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page_title = 'Daftar Produk';
$page = 'produk';

/* ========================= PAGINATION ========================= */
$limit = 10;
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}
$offset = ($current_page - 1) * $limit;

/* ========================= SEARCH & FILTER ========================= */
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

/* ========================= TOTAL DATA ========================= */
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

/* ========================= GET USERS ========================= */
$query = mysqli_query(
    $conn,
    "SELECT * FROM users 
$where ORDER BY id_user DESC LIMIT 
$limit OFFSET $offset"
);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>
<div class="main-content">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1"> Produk </h2>
            <p class="text-muted mb-0"> Kelola produk AleMart </p>
        </div>

        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
    </div>

    <!-- CARD -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row g-3 mb-4" id="filterForm">

                <!-- SEARCH -->
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text"
                            name="search"
                            id="searchInput"
                            class="form-control border-start-0"
                            placeholder="Cari nama produk..."
                            value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>

                <!-- FILTER ROLE -->
                <div class="col-md-3">
                    <select name="role"
                        id="roleFilter"
                        class="form-select">
                        <option value=""> Semua Role </option>
                        <option value="admin" <?= ($role == 'admin') ? 'selected' : ''; ?>> Admin </option>
                        <option value="kasir" <?= ($role == 'kasir') ? 'selected' : ''; ?>> Kasir </option>
                    </select>
                </div>
            </form>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th class="text-center"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no = $offset + 1;
                            while ($user = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td> <?= $no++; ?> </td>
        
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($user['avatar'])): ?>
                                                <img src="<?= BASE_URL; ?>/assets/uploads/avatar/<?= $user['avatar']; ?>" alt="Avatar" class="rounded-circle object-fit-cover border border-2 border-success" style="width: 48px; height: 48px;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px; font-size: 16px;">
                                                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>

                                            </div>
                                    </td>

                                    <td>
                                        <?= $user['username']; ?>
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill <?= ($user['role'] == 'admin') ? 'bg-success' : 'bg-primary'; ?>">
                                            <?= ucfirst($user['role']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        Rp 0
                                    </td>

                                    <td>
                                        100 </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <?php if ($user['id_user'] !== $_SESSION['id_user']): ?>
                                                <a href="edit.php?id=<?= $user['id_user']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="hapus.php?id=<?= $user['id_user']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-secondary-subtle text-secondary border">
                                                    Akun Anda
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endwhile; ?> 
                        <?php else: ?> 
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Data user tidak ditemukan
                                </td>
                            </tr> 
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-end">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($current_page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page_num=<?= $i; ?>&search=<?= $search; ?>&role=<?= $role; ?>"> <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav> <?php endif; ?>
        </div>
    </div>
</div>

<?php
include '../../includes/footer.php';
?>

<script>
    const filterForm = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    const roleFilter = document.getElementById("roleFilter");

    /* =========================
       AUTO SEARCH
    ========================= */
    let searchTimer;

    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });

    /* =========================
       AUTO FILTER
    ========================= */
    roleFilter.addEventListener("change", () => {
        filterForm.submit();
    });
</script>

<?php
include '../../includes/footer_script.php';
?>