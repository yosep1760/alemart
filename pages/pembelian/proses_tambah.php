<?php
// 1. MEMANGGIL FILE WAJIB (Proteksi & Koneksi Database)
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Cek keamanan: Pastikan file ini HANYA BISA DIAKSES jika ada data yang dikirim dari Form (POST).
// Jika user iseng mengetikkan URL "proses_tambah.php" langsung di browser, tendang kembali ke "tambah.php".
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

// ==========================================
// 2. MENANGKAP DATA DARI FORM (tambah.php)
// ==========================================
// Mengambil ID Admin/Kasir yang sedang login untuk dicatat sebagai penerima barang
$id_user         = $_SESSION['id_user']; 

// Mengambil ID Supplier dari pilihan dropdown (Dipaksa jadi integer/angka untuk keamanan)
$id_supplier     = (int) $_POST['id_supplier'];

// Mengambil Total Pembelian dari input hidden (Dipaksa jadi float/desimal)
$total_pembelian = (float) ($_POST['total_pembelian'] ?? 0);

// Menangkap keranjang belanja berformat JSON (Teks rahasia yang dikirim JavaScript tadi)
$items_json      = $_POST['items'] ?? '[]';

// MEMBONGKAR JSON: json_decode akan mengubah teks JSON tadi kembali menjadi Array PHP yang bisa dilooping!
$items           = json_decode($items_json, true);

// ==========================================
// 3. VALIDASI DATA
// ==========================================
// Cek apakah keranjang kosong, atau total belanjanya 0, atau supplier belum dipilih
if (empty($items) || $total_pembelian <= 0 || !$id_supplier) {
    $_SESSION['error'] = 'Data pembelian tidak valid atau kosong.';
    header('Location: tambah.php');
    exit;
}

// ==========================================
// 4. MEMULAI TRANSAKSI DATABASE (SANGAT PENTING!)
// ==========================================
// Memberi tahu MySQL: "Tahan dulu, jangan simpan permanen sampai saya bilang COMMIT!"
$conn->begin_transaction();

// Menggunakan blok Try-Catch. Jika di dalam blok 'try' ada yang error, maka akan langsung terlempar ke 'catch'
try {
    // Membuat tanggal dan jam saat ini
    $tanggal = date('Y-m-d H:i:s');
    
    // ----------------------------------------------------
    // TAHAP 1: INSERT KE TABEL UTAMA (pembelian)
    // ----------------------------------------------------
    // Menggunakan Prepared Statement (?) agar 100% aman dari SQL Injection
    $stmt = $conn->prepare("INSERT INTO pembelian (id_supplier, id_user, tanggal_pembelian, total_pembelian) VALUES (?, ?, ?, ?)");
    
    // Mengisi tanda (?) secara berurutan: i = integer, s = string, d = double/desimal
    $stmt->bind_param('iisd', $id_supplier, $id_user, $tanggal, $total_pembelian);
    $stmt->execute(); // Jalankan perintah insert
    
    // Mengambil ID Pembelian yang baru saja tercipta (Misal: barusan masuk urutan ID ke-15)
    $id_pembelian = $conn->insert_id;
    $stmt->close();

    // ----------------------------------------------------
    // TAHAP 2: INSERT KERANJANG (detail_pembelian) & UPDATE STOK (produk)
    // ----------------------------------------------------
    // Siapkan "Cetakan" query untuk menyimpan detail barang
    $stmt_detail = $conn->prepare("INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_beli, subtotal, no_batch, expired) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Siapkan "Cetakan" query untuk menambah stok barang (stok lama + jumlah beli baru)
    $stmt_stok = $conn->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");

    // Lakukan perulangan (looping) untuk setiap barang yang ada di keranjang array $items
    foreach ($items as $item) {
        $id_produk = (int) $item['id_produk'];
        $jumlah    = (int) $item['jumlah'];
        $harga     = (float) $item['harga_beli'];
        $subtotal  = $harga * $jumlah; // Hitung ulang subtotal demi keamanan
        
        // Cek jika kosong, ubah jadi NULL (kosong murni di database)
        $no_batch  = !empty($item['no_batch']) ? $item['no_batch'] : NULL;
        $expired   = !empty($item['expired']) ? $item['expired'] : NULL;

        // A. Simpan barang ini ke tabel detail_pembelian
        $stmt_detail->bind_param('iiiddss', $id_pembelian, $id_produk, $jumlah, $harga, $subtotal, $no_batch, $expired);
        $stmt_detail->execute();

        // B. Tambahkan stok barang ini di tabel produk
        $stmt_stok->bind_param('ii', $jumlah, $id_produk);
        $stmt_stok->execute();
    }

    // Tutup "Cetakan" query
    $stmt_detail->close();
    $stmt_stok->close();

    // ----------------------------------------------------
    // TAHAP 3: COMMIT (PENGESAHAN)
    // ----------------------------------------------------
    // Jika semua proses di atas sukses tanpa macet, berikan instruksi COMMIT ke MySQL untuk menyimpan permanen!
    $conn->commit();
    
    // Siapkan pesan sukses dan lemparkan ke halaman "Detail Pembelian" untuk melihat fakturnya
    $_SESSION['success'] = "Pembelian #" . str_pad($id_pembelian, 5, '0', STR_PAD_LEFT) . " berhasil disimpan dan stok telah ditambahkan.";
    header("Location: detail.php?id=$id_pembelian");
    exit;

} catch (Exception $e) {
    // ----------------------------------------------------
    // TAHAP 4: ROLLBACK (PEMBATALAN JIKA ERROR)
    // ----------------------------------------------------
    // Jika di tengah proses ada query yang error, batalkan semua perintah yang sudah terlanjur jalan!
    $conn->rollback();
    
    // Munculkan pesan error teknis ke layar
    $_SESSION['error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
    header('Location: tambah.php');
    exit;
}
?>