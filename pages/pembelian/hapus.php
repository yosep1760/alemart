<?php
// 1. MEMANGGIL FILE WAJIB (Proteksi & Koneksi)
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

// ==========================================
// 2. PROTEKSI HAK AKSES (HANYA ADMIN)
// ==========================================
// Mengecek 'role' (jabatan) user yang sedang login dari session.
// Logikanya: Kasir boleh melayani, tapi yang berhak menghapus riwayat dokumen hanya Admin!
if ($_SESSION['role'] !== 'admin') {
    // Jika kasir mencoba mengakses halaman ini, beri pesan error
    $_SESSION['error'] = 'Akses ditolak! Hanya admin yang bisa menghapus pembelian.';
    header('Location: index.php'); // Lemparkan kembali
    exit; // Hentikan script di sini agar kode hapus di bawah tidak jalan
}

// ==========================================
// 3. MENGAMBIL ID PEMBELIAN DARI URL
// ==========================================
// Menangkap ID pembelian yang ingin dihapus (contoh dari URL: hapus.php?id=12)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Memastikan data struk yang mau dihapus benar-benar masih ada di database
$cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_pembelian FROM pembelian WHERE id_pembelian = $id"));
if (!$cek) {
    $_SESSION['error'] = 'Data pembelian tidak ditemukan.';
    header('Location: index.php');
    exit;
}

// ==========================================
// 4. MEMULAI TRANSAKSI DATABASE (CEGAH ERROR STOK)
// ==========================================
// Memberi tahu MySQL: "Tahan dulu, kita mau hapus data dan kurangi stok secara bersamaan. Kalau satu gagal, batalkan semua!"
$conn->begin_transaction();

try {
    // ----------------------------------------------------
    // TAHAP A: AMBIL DATA BARANG UNTUK DIKURANGI STOKNYA
    // ----------------------------------------------------
    // Kita cari dulu, di dalam nota pembelian ini, produk apa saja yang dibeli dan berapa jumlahnya?
    $detail = mysqli_query($conn, "SELECT id_produk, jumlah FROM detail_pembelian WHERE id_pembelian = $id");
    
    // Melakukan perulangan untuk setiap barang yang ada di dalam nota tersebut
    while ($d = mysqli_fetch_assoc($detail)) {
        // LOGIKA PENARIKAN STOK:
        // Karena pembelian ini BATAL/DIHAPUS, maka stok barang yang tadinya bertambah harus DIKURANGI KEMBALI!
        // Contoh: Dulu beli Indomie 100 bungkus, karena notanya dihapus, stok Indomie otomatis ditarik mundur 100 bungkus.
        $conn->query("UPDATE produk SET stok = stok - {$d['jumlah']} WHERE id_produk = {$d['id_produk']}");
    }

    // ----------------------------------------------------
    // TAHAP B: MENGHAPUS DATA DARI DATABASE
    // ----------------------------------------------------
    // 1. Menghapus rincian barang dari tabel 'detail_pembelian' terlebih dahulu
    $conn->query("DELETE FROM detail_pembelian WHERE id_pembelian = $id");
    
    // 2. Setelah rinciannya bersih, barulah menghapus kepala notanya dari tabel 'pembelian'
    $conn->query("DELETE FROM pembelian WHERE id_pembelian = $id");

    // ----------------------------------------------------
    // TAHAP C: PENGESAHAN (COMMIT)
    // ----------------------------------------------------
    // Jika penarikan stok berhasil dan penghapusan data berhasil, jalankan komitmen untuk menyimpan perubahan!
    $conn->commit();
    $_SESSION['success'] = 'Pembelian berhasil dihapus dan stok produk telah dikurangi kembali.';

} catch (Exception $e) {
    // ----------------------------------------------------
    // TAHAP D: PEMBATALAN (ROLLBACK) JIKA ERROR
    // ----------------------------------------------------
    // Jika server ngadat di tengah jalan (misal stok gagal ditarik mundur), maka JANGAN hapus nota pembeliannya agar data tidak cacat.
    $conn->rollback();
    $_SESSION['error'] = 'Gagal menghapus pembelian: ' . $e->getMessage();
}

// Terakhir, kembalikan halaman ke daftar riwayat pembelian
header('Location: index.php');
exit;
?>