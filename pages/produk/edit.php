<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

// Pastikan ada parameter ID yang dilempar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);

// --- PROSES UPDATE DATA (SELF-SUBMIT) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga_beli  = (int)$_POST['harga_beli'];
    $harga_jual  = (int)$_POST['harga_jual'];
    $stok        = (int)$_POST['stok'];
    $stok_min    = (int)$_POST['stok_minimum'];
    
    // Ambil data gambar lama untuk cadangan
    $query_lama = mysqli_query($conn, "SELECT gambar FROM produk WHERE id_produk = '$id_produk'");
    $data_lama  = mysqli_fetch_assoc($query_lama);
    $nama_gambar = $data_lama['gambar']; 

    // Cek apakah user mengupload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_name  = $_FILES['gambar']['tmp_name'];
        $ekstensi  = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_gambar = time() . '_' . uniqid() . '.' . $ekstensi;
        
        // Pindahkan file baru
        if (move_uploaded_file($tmp_name, '../../assets/uploads/produk/' . $nama_gambar)) {
            // Hapus gambar lama dari server jika ada dan file-nya eksis
            if (!empty($data_lama['gambar']) && file_exists('../../assets/uploads/produk/' . $data_lama['gambar'])) {
                unlink('../../assets/uploads/produk/' . $data_lama['gambar']);
            }
        }
    }

    // Jalankan query update data produk
    $query_update = "UPDATE produk SET 
                        nama_produk = '$nama_produk', 
                        harga_beli = '$harga_beli', 
                        harga_jual = '$harga_jual', 
                        stok = '$stok', 
                        stok_minimum = '$stok_min', 
                        gambar = '$nama_gambar' 
                     WHERE id_produk = '$id_produk'";

    if (mysqli_query($conn, $query_update)) {
        $_SESSION['sukses'] = "Data produk berhasil diperbarui!";
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($conn) . "');</script>";
    }
}

// --- AMBIL DATA PRODUK UNTUK DITAMPILKAN DI FORM ---
$query_ambil = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
$produk = mysqli_fetch_assoc($query_ambil);

// Jika data tidak ditemukan di database, tendang kembali ke index
if (!$produk) {
    header("Location: index.php");
    exit();
}

$page_title = 'Edit Produk';
$page = 'produk';

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="main-content" style="background-color: #f8f9fa; min-height: 100vh; padding: 20px;">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1"> Produk </h2>
        </div>
    </div>


    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-light border-0 shadow-sm rounded-3 px-3 py-2 bg-white text-secondary d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">Edit produk</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;"><?= htmlspecialchars($produk['nama_produk']); ?></p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Nama produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_produk" class="form-control bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-medium" value="<?= htmlspecialchars($produk['nama_produk']); ?>" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Harga beli</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted px-3 rounded-start-3 fw-medium">Rp</span>
                            <input type="number" name="harga_beli" class="form-control bg-light border-0 py-2.5 fs-6 rounded-end-3 text-dark fw-semibold" value="<?= $produk['harga_beli']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Harga jual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted px-3 rounded-start-3 fw-medium">Rp</span>
                            <input type="number" name="harga_jual" class="form-control bg-light border-0 py-2.5 fs-6 rounded-end-3 text-dark fw-semibold" value="<?= $produk['harga_jual']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Stok</label>
                        <input type="number" name="stok" class="form-control bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-semibold" value="<?= isset($produk['stok']) ? htmlspecialchars($produk['stok']) : 0; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Stok minimum</label>
                        <input type="number" name="stok_minimum" class="form-control bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-semibold" value="<?= isset($produk['stok_minimum']) ? htmlspecialchars($produk['stok_minimum']) : 5; ?>" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Ganti gambar <span class="text-muted">(opsional)</span></label>
                    <div class="position-relative border rounded-4 p-4 text-center bg-light" style="border-style: dashed !important; border-width: 2px !important; border-color: #ced4da !important;">
                        <input type="file" name="gambar" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept="image/jpeg, image/png, image/webp">
                        <div class="py-3">
                            <i class="bi bi-cloud-arrow-up text-muted mb-2 d-block" style="font-size: 2.5rem;"></i>
                            <span class="d-block text-dark fw-medium" style="font-size: 0.95rem;">Biarkan kosong jika tidak ingin mengganti gambar</span>
                            <?php if(!empty($produk['gambar'])): ?>
                                <span class="text-success d-block mt-1" style="font-size: 0.8rem;">(Gambar saat ini: <?= $produk['gambar']; ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 border-top pt-4">
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm text-white d-flex align-items-center gap-2" style="background-color: #198754; border: none;">
                        <i class="bi bi-check-lg"></i> Simpan perubahan
                    </button>
                    <a href="index.php" class="btn btn-light bg-white border px-4 py-2 rounded-3 fw-semibold text-secondary shadow-sm">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<?php include '../../includes/footer_script.php'; ?> 