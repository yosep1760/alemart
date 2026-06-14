<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

// --- PROSES SIMPAN DATA (SELF-SUBMIT) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $supplier    = mysqli_real_escape_string($conn, $_POST['supplier']);
    $harga_beli  = (int)$_POST['harga_beli'];
    $harga_jual  = (int)$_POST['harga_jual'];
    $stok        = (int)$_POST['stok'];
    $stok_min    = (int)$_POST['stok_minimum'];
    
    $nama_gambar = ''; 

    // Proses upload gambar jika user memasukkan foto
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_name  = $_FILES['gambar']['tmp_name'];
        $ekstensi  = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_gambar = time() . '_' . uniqid() . '.' . $ekstensi;
        
        move_uploaded_file($tmp_name, '../../assets/uploads/produk/' . $nama_gambar);
    }

    // Query ke tabel produk kamu
    $query = "INSERT INTO produk (nama_produk, kategori, supplier, harga_beli, harga_jual, stok, stok_minimum, gambar) 
              VALUES ('$nama_produk', '$kategori', '$supplier', '$harga_beli', '$harga_jual', '$stok', '$stok_min', '$nama_gambar')";

    if (mysqli_query($conn, $query)) {
        // Set pesan sukses di session, lalu lempar ke index.php
        $_SESSION['sukses'] = "Produk baru berhasil ditambahkan!";
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan data ke database!');</script>";
    }
}

$page_title = 'Tambah Produk';
$page = 'produk';

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="main-content" style="background-color: #f8f9fa; min-height: 100vh; padding: 20px;">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-light border-0 shadow-sm rounded-3 px-3 py-2 bg-white text-secondary d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">Tambah produk</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Isi semua field yang diperlukan</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="" method="POST" enctype="multipart/form-data" id="formTambahProduk">
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Nama produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_produk" class="form-control bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-medium" placeholder="Contoh: Indomie Goreng" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-medium" required>
                            <option value="">-- Pilih kategori --</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier" class="form-select bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-medium" required>
                            <option value="">-- Pilih supplier --</option>
                            <option value="PT Makmur Jaya">PT Makmur Jaya</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Harga beli <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted px-3 rounded-start-3 fw-medium">Rp</span>
                            <input type="number" name="harga_beli" class="form-control bg-light border-0 py-2.5 fs-6 rounded-end-3 text-dark fw-semibold" placeholder="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Harga jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted px-3 rounded-start-3 fw-medium">Rp</span>
                            <input type="number" name="harga_jual" class="form-control bg-light border-0 py-2.5 fs-6 rounded-end-3 text-dark fw-semibold" placeholder="0" required>
                        </div>
                    </div> 
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-semibold" placeholder="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Stok minimum <span class="text-danger">*</span></label>
                        <input type="number" name="stok_minimum" class="form-control bg-light border-0 py-2.5 px-3 fs-6 rounded-3 text-dark fw-semibold" placeholder="5" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold text-secondary mb-2" style="font-size: 0.9rem;">Gambar produk</label>
                    <div class="position-relative border rounded-4 p-4 text-center bg-light" style="border-style: dashed !important; border-width: 2px !important; border-color: #ced4da !important;">
                        <input type="file" name="gambar" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept="image/jpeg, image/png, image/webp">
                        <div class="py-3">
                            <i class="bi bi-cloud-arrow-up text-muted mb-2 d-block" style="font-size: 2.5rem;"></i>
                            <span class="d-block text-dark fw-medium" style="font-size: 0.95rem;">Klik untuk upload gambar</span>
                            <span class="text-muted d-block mt-1" style="font-size: 0.8rem;">(JPG, PNG, WEBP — maks 2MB)</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 border-top pt-4">
                    <button type="submit" id="btnSimpan" class="btn btn-secondary px-4 py-2 rounded-3 fw-semibold shadow-sm text-white d-flex align-items-center gap-2" style="background-color: #6c757d; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                        <i class="bi bi-check-lg"></i> Simpan produk
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

<script>
    const formInputs = document.querySelectorAll('#formTambahProduk input[required], #formTambahProduk select[required]');
    const btnSimpan = document.getElementById('btnSimpan');

    function cekValidasiForm() {
        let semuaTerisi = true;
        formInputs.forEach(input => {
            if (input.value.trim() === '') { semuaTerisi = false; }
        });

        if (semuaTerisi) {
            btnSimpan.removeAttribute('disabled');
            btnSimpan.style.opacity = '1';
            btnSimpan.style.cursor = 'pointer';
            btnSimpan.style.backgroundColor = '#198754'; 
        } else {
            btnSimpan.setAttribute('disabled', 'true');
            btnSimpan.style.opacity = '0.5';
            btnSimpan.style.cursor = 'not-allowed';
            btnSimpan.style.backgroundColor = '#6c757d'; 
        }
    }

    formInputs.forEach(input => {
        input.addEventListener('input', cekValidasiForm);
        input.addEventListener('change', cekValidasiForm);
    });
    window.addEventListener('DOMContentLoaded', cekValidasiForm);
</script>
<?php include '../../includes/footer_script.php'; ?>