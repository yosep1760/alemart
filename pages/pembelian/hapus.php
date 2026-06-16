<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Proteksi, hanya Admin yang boleh membatalkan/menghapus faktur pembelian
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Akses ditolak! Hanya admin yang bisa menghapus pembelian.';
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

$cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_pembelian FROM pembelian WHERE id_pembelian = $id"));

if (!$cek) {
    $_SESSION['error'] = 'Data pembelian tidak ditemukan.';
    header('Location: index.php');
    exit;
}

$conn->begin_transaction();

try {
    // 1. Ambil data detail barang yang dibeli untuk mengurangi stok produk
    $detail = mysqli_query($conn, "SELECT id_produk, jumlah FROM detail_pembelian WHERE id_pembelian = $id");

    while ($d = mysqli_fetch_assoc($detail)) {
        // Kurangi stok karena pembelian dibatalkan/dihapus
        $conn->query("UPDATE produk SET stok = stok - {$d['jumlah']} WHERE id_produk = {$d['id_produk']}");
    }

    // 2. Hapus dari tabel pembelian (Detail akan otomatis terhapus jika di DB di-setting ON DELETE CASCADE. 
    // Jika tidak, kita hapus manual detailnya dulu)
    $conn->query("DELETE FROM detail_pembelian WHERE id_pembelian = $id");
    $conn->query("DELETE FROM pembelian WHERE id_pembelian = $id");

    $conn->commit();
    $_SESSION['success'] = 'Pembelian berhasil dihapus dan stok produk telah dikurangi kembali.';

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Gagal menghapus pembelian: ' . $e->getMessage();
}

header('Location: index.php');
exit;
?>