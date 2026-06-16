<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

// PROTEKSI ROLE: Jika bukan admin, tendang keluar
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Akses ditolak! Halaman tersebut hanya untuk Admin.";
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $id_kategori = (int)$_POST['id_kategori'];
    $harga_beli  = (float)$_POST['harga_beli'];
    $harga_jual  = (float)$_POST['harga_jual'];
    $stok        = (int)$_POST['stok'];
    $satuan      = mysqli_real_escape_string($conn, $_POST['satuan']);

    $q_lama    = mysqli_query($conn, "SELECT foto_produk FROM produk WHERE id_produk = '$id_produk'");
    $data_lama = mysqli_fetch_assoc($q_lama);
    $foto_produk = $data_lama['foto_produk'];

    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] === 0) {
        // 1. Ambil nama file asli
        $foto_produk = str_replace(' ', '_', basename($_FILES['foto_produk']['name']));
        
        // 2. Tambahkan @ untuk membungkam error Vercel
        @move_uploaded_file($_FILES['foto_produk']['tmp_name'], __DIR__ . '/../../assets/uploads/produk/' . $foto_produk);
        
        // 3. Tambahkan @ untuk membungkam error saat menghapus foto lama
        if (!empty($data_lama['foto_produk'])) {
            @unlink(__DIR__ . '/../../assets/uploads/produk/' . $data_lama['foto_produk']);
        }
    }

    $q_update = "UPDATE produk SET 
                    nama_produk = '$nama_produk',
                    id_kategori = '$id_kategori',
                    harga_beli  = '$harga_beli',
                    harga_jual  = '$harga_jual',
                    stok        = '$stok',
                    satuan      = '$satuan',
                    foto_produk = '$foto_produk'
                  WHERE id_produk = '$id_produk'";
                  
    if (mysqli_query($conn, $q_update)) {
        $_SESSION['success'] = "Data produk berhasil diperbarui!";
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}

$q_produk = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
$produk   = mysqli_fetch_assoc($q_produk);

if (!$produk) { header("Location: index.php"); exit(); }

$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$page_title = 'Edit Produk';
$page = 'produk';

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content" style="background-color:#f8f9fa;min-height:100vh;padding:20px;padding-top:80px;">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-light border-0 shadow-sm rounded-3 px-3 py-2 bg-white text-secondary d-inline-flex align-items-center justify-content-center" style="width:42px;height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size:1.6rem;">Edit Produk</h2>
            <p class="text-muted mb-0" style="font-size:0.9rem;"><?= htmlspecialchars($produk['nama_produk']); ?></p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="" method="POST" enctype="multipart/form-data">

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_produk" class="form-control bg-light border-0 rounded-3"
                        value="<?= htmlspecialchars($produk['nama_produk']); ?>" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Kategori <span class="text-danger">*</span></label>
                        <select name="id_kategori" class="form-select bg-light border-0 rounded-3" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                <option value="<?= $kat['id_kategori']; ?>"
                                    <?= ($kat['id_kategori'] == $produk['id_kategori']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Satuan <span class="text-danger">*</span></label>
                        <select name="satuan" class="form-select bg-light border-0 rounded-3" required>
                            <?php
                            $satuans = ['pcs','bungkus','botol','kaleng','kg','gram','liter','ml','pack','sak','butir'];
                            foreach ($satuans as $s):
                            ?>
                                <option value="<?= $s; ?>" <?= ($produk['satuan'] == $s) ? 'selected' : ''; ?>>
                                    <?= ucfirst($s); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Harga Beli</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 fw-medium">Rp</span>
                            <input type="number" name="harga_beli" class="form-control bg-light border-0 rounded-end-3"
                                value="<?= $produk['harga_beli']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Harga Jual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 fw-medium">Rp</span>
                            <input type="number" name="harga_jual" class="form-control bg-light border-0 rounded-end-3"
                                value="<?= $produk['harga_jual']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Stok</label>
                    <input type="number" name="stok" class="form-control bg-light border-0 rounded-3"
                        value="<?= $produk['stok']; ?>" required>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size:0.9rem;">Ganti Foto <span class="text-muted">(opsional)</span></label>
                    <div class="position-relative border rounded-4 p-4 text-center bg-light" style="border-style:dashed!important;border-width:2px!important;border-color:#ced4da!important;">
                        <input type="file" id="fotoInput" name="foto_produk"
                               class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                               style="cursor:pointer;" accept="image/jpeg,image/png,image/webp">

                        <?php if (!empty($produk['foto_produk'])): ?>
                            <img id="fotoPreview"
                                 src="<?= BASE_URL; ?>/assets/uploads/produk/<?= htmlspecialchars($produk['foto_produk']); ?>"
                                 class="rounded-3 mb-3 object-fit-cover" style="width:120px;height:120px;">
                            <div id="fotoDefault" class="d-none py-3">
                        <?php else: ?>
                            <img id="fotoPreview" src="" class="d-none rounded-3 mb-3 object-fit-cover" style="width:120px;height:120px;">
                            <div id="fotoDefault" class="py-3">
                        <?php endif; ?>
                                <i class="bi bi-cloud-arrow-up text-muted mb-2 d-block" style="font-size:2.5rem;"></i>
                                <span class="d-block text-dark fw-medium" style="font-size:0.95rem;">Klik untuk ganti foto</span>
                                <span class="text-muted d-block mt-1" style="font-size:0.8rem;">(JPG, PNG, WEBP — maks 2MB)</span>
                            </div>
                    </div>
                </div>

                <div class="d-flex gap-2 border-top pt-4">
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm text-white d-flex align-items-center gap-2">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn btn-light bg-white border px-4 py-2 rounded-3 fw-semibold text-secondary shadow-sm">Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
    // ========================= PREVIEW FOTO =========================
    const fotoInput   = document.getElementById('fotoInput');
    const fotoPreview = document.getElementById('fotoPreview');
    const fotoDefault = document.getElementById('fotoDefault');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                fotoPreview.src = event.target.result;
                fotoPreview.classList.remove('d-none');
                fotoDefault.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
    
<?php include __DIR__ . '/../../includes/footer_script.php'; ?>