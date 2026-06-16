<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Hanya admin yang boleh hapus
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses untuk menghapus transaksi.';
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

/* ========================= CEK TRANSAKSI ========================= */
$cek = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT id_transaksi FROM transaksi WHERE id_transaksi = $id"
));

if (!$cek) {
    $_SESSION['error'] = 'Transaksi tidak ditemukan.';
    header('Location: index.php');
    exit;
}

/* ========================= HAPUS (CASCADE ke detail_transaksi) ========================= */
$conn->begin_transaction();

try {
    // Kembalikan stok produk
    $detail = mysqli_query($conn,
        "SELECT id_produk, jumlah FROM detail_transaksi WHERE id_transaksi = $id"
    );

    while ($d = mysqli_fetch_assoc($detail)) {
        $conn->query(
            "UPDATE produk SET stok = stok + {$d['jumlah']} WHERE id_produk = {$d['id_produk']}"
        );
    }

    // Hapus transaksi (detail terhapus otomatis via ON DELETE CASCADE)
    $conn->query("DELETE FROM transaksi WHERE id_transaksi = $id");

    $conn->commit();
    $_SESSION['success'] = 'Transaksi berhasil dihapus dan stok telah dikembalikan.';

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Gagal menghapus transaksi: ' . $e->getMessage();
}

header('Location: index.php');
exit;
?>