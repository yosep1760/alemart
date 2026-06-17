<?php
// 1. MEMANGGIL FILE WAJIB (Proteksi & Koneksi)
// Memastikan hanya user yang sudah login yang bisa membuka halaman ini
include __DIR__ . '/../../auth/auth_check.php';
// Memanggil pengaturan dasar seperti BASE_URL
require_once __DIR__ . '/../../config/config.php';
// Memanggil koneksi database agar bisa menyimpan data
require_once __DIR__ . '/../../config/koneksi.php';

// Menentukan judul halaman di tab browser
$page_title = 'Tambah Supplier';
// Menandai menu 'supplier' agar menyala di sidebar kiri
$page = 'supplier';

// ==========================================
// 2. LOGIKA PENYIMPANAN DATA (Jika tombol Simpan ditekan)
// ==========================================
// Mengecek apakah halaman ini sedang menerima kiriman data dari form (metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Mengambil data yang diisi user di dalam form HTML di bawah.
    // Fungsi trim() digunakan untuk memotong spasi kosong tidak sengaja di awal/akhir kata.
    $nama_supplier = trim($_POST['nama_supplier']);
    $no_telp       = trim($_POST['no_telp']);
    $alamat        = trim($_POST['alamat']);

    // Validasi 1: Memastikan tidak ada kotak isian yang dibiarkan kosong
    if (empty($nama_supplier) || empty($no_telp) || empty($alamat)) {
        // Jika ada yang kosong, buat pesan error untuk ditampilkan nanti
        $_SESSION['error'] = 'Semua field wajib diisi!';
    } else {
        // Validasi 2: Keamanan Database (Mencegah SQL Injection)
        // Fungsi real_escape_string akan menetralisir karakter bahaya seperti tanda kutip (')
        // Contoh: Jika user mengetik nama supplier "PT. Jum'at", tanda kutipnya tidak akan merusak sistem database.
        $nama_esc = $conn->real_escape_string($nama_supplier);
        $telp_esc = $conn->real_escape_string($no_telp);
        $alamat_esc = $conn->real_escape_string($alamat);

        // Menjalankan perintah SQL untuk MENAMBAH (INSERT) data ke tabel 'supplier'
        $query = mysqli_query($conn, "INSERT INTO supplier (nama_supplier, no_telp, alamat) VALUES ('$nama_esc', '$telp_esc', '$alamat_esc')");

        // Mengecek apakah perintah penambahan data di atas berhasil dijalankan database
        if ($query) {
            // Jika berhasil, buat pesan sukses
            $_SESSION['sukses'] = 'Supplier berhasil ditambahkan!';
            // Lemparkan (redirect) user kembali ke halaman daftar supplier (index.php)
            header("Location: index.php");
            exit; // Hentikan script di sini agar tidak memuat kode di bawahnya
        } else {
            // Jika gagal (misal server down), buat pesan error
            $_SESSION['error'] = 'Gagal menambahkan supplier!';
        }
    }
}

// 3. MEMANGGIL TAMPILAN ATAS (Template)
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tambah Supplier</h2>
            <p class="text-muted mb-0">Tambahkan data pemasok barang baru</p>
        </div>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            
            <form method="POST" action="">
                
                <div class="mb-3">
                    <label for="nama_supplier" class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" placeholder="Contoh: PT Indofood CBP" required>
                </div>
                
                <div class="mb-3">
                    <label for="no_telp" class="form-label fw-semibold">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="Contoh: 08123456789" required>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap supplier" required></textarea>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-light border px-4 me-2">Reset</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Simpan Data
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>

<?php 
include __DIR__ . '/../../includes/footer.php';
include __DIR__ . '/../../includes/footer_script.php'; 
?>