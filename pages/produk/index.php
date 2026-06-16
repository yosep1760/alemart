<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Daftar Produk';
$page = 'produk';

// =============================================
// SORTING (Fitur Baru untuk Poin 3)
// =============================================
$valid_sort_cols = ['nama_produk' => 'p.nama_produk', 'harga' => 'p.harga_jual', 'stok' => 'p.stok'];
$sort_by = isset($_GET['sort_by']) && array_key_exists($_GET['sort_by'], $valid_sort_cols) ? $_GET['sort_by'] : 'id_produk';
$order   = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

$order_by_clause = ($sort_by === 'id_produk') ? "p.id_produk DESC" : "{$valid_sort_cols[$sort_by]} $order";

// =============================================
// PAGINATION
// =============================================
$limit        = 10;
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $limit;

// =============================================
// SEARCH
// =============================================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where  = "WHERE 1=1";

if (!empty($search)) {
    $search_esc = $conn->real_escape_string($search);
    $where .= " AND p.nama_produk LIKE '%$search_esc%'";
}

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk p $where");
$total_data  = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// QUERY UTAMA (Sudah ditambahkan klausa ORDER BY dinamis)
$query = mysqli_query($conn, "
    SELECT p.*, k.nama_kategori 
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
    $where 
    ORDER BY $order_by_clause
    LIMIT $limit OFFSET $offset
");

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Produk</h2>
            <p class="text-muted mb-0">Kelola produk AleMart</p>
        </div>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="tambah.php" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </a>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput"
                            class="form-control border-start-0"
                            placeholder="Cari nama produk..."
                            value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="sort_by" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="id_produk" <?= $sort_by == 'id_produk' ? 'selected' : ''; ?>>Urutan Default</option>
                        <option value="nama_produk" <?= $sort_by == 'nama_produk' ? 'selected' : ''; ?>>Nama Produk</option>
                        <option value="harga" <?= $sort_by == 'harga' ? 'selected' : ''; ?>>Harga Jual</option>
                        <option value="stok" <?= $sort_by == 'stok' ? 'selected' : ''; ?>>Stok</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="order" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="desc" <?= $order == 'DESC' ? 'selected' : ''; ?>>Menurun (Z-A / Termahal / Terbanyak)</option>
                        <option value="asc" <?= $order == 'ASC' ? 'selected' : ''; ?>>Menaik (A-Z / Termurah / Tersedikit)</option>
                    </select>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th class="text-center">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php
                            $no = $offset + 1;
                            while ($produk = mysqli_fetch_assoc($query)):
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <?php if (!empty($produk['foto_produk'])): ?>
                                            <img src="<?= BASE_URL; ?>/assets/uploads/produk/<?= htmlspecialchars($produk['foto_produk']); ?>"
                                                 class="rounded-3 object-fit-cover border"
                                                 style="width:52px;height:52px;">
                                        <?php else: ?>
                                            <div class="rounded-3 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center"
                                                 style="width:52px;height:52px;font-size:20px;">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold"><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                            <?= htmlspecialchars($produk['nama_kategori'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($produk['satuan']); ?></td>
                                    <td>Rp <?= number_format($produk['harga_jual'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge <?= $produk['stok'] <= 10 ? 'bg-danger' : 'bg-success'; ?>">
                                            <?= $produk['stok']; ?>
                                        </span>
                                    </td>
                                    
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="edit.php?id=<?= $produk['id_produk']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm btn-delete"
                                                        data-id="<?= $produk['id_produk']; ?>"
                                                        data-nama="<?= htmlspecialchars($produk['nama_produk']); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $_SESSION['role'] === 'admin' ? '8' : '7'; ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Data produk tidak ditemukan
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
                                <a class="page-link" href="?page_num=<?= $i; ?>&search=<?= urlencode($search); ?>&sort_by=<?= $sort_by; ?>&order=<?= $order; ?>">
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
    
<script>
    // AUTO SEARCH
    const filterForm  = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    let searchTimer;
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.submit(), 500);
    });

    // KONFIRMASI HAPUS
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            Swal.fire({
                title: 'Hapus Produk?',
                text: `Anda akan menghapus produk "${nama}"`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = `hapus.php?id=${id}`; }
            });
        });
    });
</script>

<?php include __DIR__ . '/../../includes/footer_script.php'; ?>