<?php
// Memasukkan file auth_check.php untuk mengecek apakah user sudah login.
// __DIR__ memastikan path (jalur) file akurat dari lokasi file index.php ini berada.
include __DIR__ . '/../../auth/auth_check.php';

// Memasukkan file config.php yang berisi pengaturan dasar aplikasi seperti BASE_URL.
require_once __DIR__ . '/../../config/config.php';

// Memasukkan file koneksi.php untuk menghubungkan PHP ke database MySQL/Rumahweb.
require_once __DIR__ . '/../../config/koneksi.php';

// Menentukan judul halaman yang akan ditampilkan di tab browser (misal: "Daftar Supplier - AleMart").
$page_title = 'Daftar Supplier';

// Menandai halaman aktif untuk menu sidebar (agar menu "Supplier" di kiri menyala/aktif).
$page = 'supplier';

// ==========================================
// PENGATURAN PAGINATION (HALAMAN)
// ==========================================
// Menentukan batas maksimal data yang ditampilkan per halaman (10 data).
$limit = 10;

// Mengambil nomor halaman dari URL (contoh: index.php?page_num=2). Jika tidak ada, anggap halaman 1.
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;

// Jika user iseng memasukkan angka halaman kurang dari 1 (misal 0 atau -1), paksa kembali ke halaman 1.
if ($current_page < 1) $current_page = 1;

// Menghitung offset (data ke-berapa yang mulai ditampilkan).
// Contoh: Halaman 1 -> (1-1)*10 = offset 0 (Mulai dari data pertama).
// Contoh: Halaman 2 -> (2-1)*10 = offset 10 (Mulai dari data kesebelas).
$offset = ($current_page - 1) * $limit;


// ==========================================
// PENGATURAN FITUR PENCARIAN (SEARCH)
// ==========================================
// Mengambil kata kunci pencarian dari URL (contoh: index.php?search=indofood).
// Fungsi trim() digunakan untuk menghapus spasi kosong di awal/akhir kata.
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Membuat dasar kondisi untuk query database (WHERE 1=1 adalah trik agar mudah menambah kondisi AND).
$where  = "WHERE 1=1";

// Jika variabel $search tidak kosong (user mengetik sesuatu di kolom pencarian)...
if (!empty($search)) {
    // Mengamankan teks inputan agar tidak terkena serangan SQL Injection.
    $search_esc = $conn->real_escape_string($search);
    
    // Menambahkan perintah ke query database untuk mencari nama supplier yang mirip dengan inputan user.
    $where .= " AND nama_supplier LIKE '%$search_esc%'";
}

// Menghitung total seluruh data supplier di database (digunakan untuk membuat tombol halaman/pagination).
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier $where");

// Mengambil hasil hitungan angka totalnya dari query di atas.
$total_data  = mysqli_fetch_assoc($total_query)['total'];

// Menghitung total halaman. Contoh: ada 25 data, dibagi 10 limit = 2.5 halaman. 
// Fungsi ceil() akan membulatkan 2.5 ke atas menjadi 3 halaman.
$total_pages = ceil($total_data / $limit);

// Mengambil data supplier dari database untuk ditampilkan di tabel.
// Menggunakan LIMIT dan OFFSET untuk membatasi data sesuai halaman saat ini.
$query = mysqli_query($conn, "
    SELECT * FROM supplier
    $where 
    ORDER BY id_supplier DESC 
    LIMIT $limit OFFSET $offset
");

// Memasukkan template bagian atas web (Logo, CSS, dll).
include __DIR__ . '/../../includes/header.php';

// Memasukkan template navigasi atas (Navbar).
include __DIR__ . '/../../includes/navbar.php';

// Memasukkan template menu samping (Sidebar).
include __DIR__ . '/../../includes/sidebar.php';
?>

<?php if (isset($_SESSION['sukses'])): ?>
    <script>
        // Saat halaman HTML selesai dimuat, jalankan script SweetAlert ini
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $_SESSION['sukses']; ?>', showConfirmButton: false, timer: 2000 });
        });
    </script>
    <?php unset($_SESSION['sukses']); ?>
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
            <h2 class="fw-bold mb-1">Supplier</h2>
            <p class="text-muted mb-0">Kelola data pemasok barang AleMart</p>
        </div>
        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Supplier
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput"
                            class="form-control border-start-0"
                            placeholder="Cari nama supplier..."
                            value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Supplier</th>
                            <th width="15%">No. Telp</th>
                            <th width="35%">Alamat</th>
                            <th class="text-center" width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            
                            <?php $no = $offset + 1;
                            
                            // Looping (perulangan) untuk setiap baris data supplier dari database
                            while ($supplier = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    
                                    <td class="fw-semibold text-capitalize">
                                        <?= htmlspecialchars($supplier['nama_supplier']); ?>
                                    </td>
                                    
                                    <td>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $supplier['no_telp']); ?>" target="_blank" class="text-decoration-none text-success">
                                            <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($supplier['no_telp']); ?>
                                        </a>
                                    </td>
                                    
                                    <td class="text-truncate" style="max-width: 250px;">
                                        <?= htmlspecialchars($supplier['alamat']); ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="edit.php?id=<?= $supplier['id_supplier']; ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                    data-id="<?= $supplier['id_supplier']; ?>"
                                                    data-nama="<?= htmlspecialchars($supplier['nama_supplier']); ?>">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-truck fs-1 d-block mb-2"></i>
                                    Data supplier tidak ditemukan
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
                                <a class="page-link" href="?page_num=<?= $i; ?>&search=<?= urlencode($search); ?>">
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
    // Fitur Auto Search: Web akan mencari otomatis saat user mengetik tanpa perlu klik tombol "Cari"
    const filterForm  = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    let searchTimer;

    // Saat user mengetik di kolom pencarian...
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer); // Hapus timer lama
        // Tunggu 0.5 detik (500ms) setelah user berhenti mengetik, lalu submit formulir otomatis
        searchTimer = setTimeout(() => filterForm.submit(), 500);
    });

    // Fitur Konfirmasi Hapus SweetAlert
    // Mencari semua tombol yang memiliki class "btn-delete"
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    // Memasang aksi klik pada masing-masing tombol hapus
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mengambil ID dan Nama dari atribut HTML "data-id" dan "data-nama"
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            
            // Memunculkan pop-up konfirmasi peringatan
            Swal.fire({
                title: 'Hapus Supplier?',
                text: `Anda akan menghapus data supplier "${nama}". Pastikan supplier ini tidak sedang digunakan di data pembelian.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                // Jika user menekan tombol "Ya, hapus", alihkan ke file hapus.php
                if (result.isConfirmed) {
                    window.location.href = `hapus.php?id=${id}`;
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../../includes/footer_script.php'; ?>