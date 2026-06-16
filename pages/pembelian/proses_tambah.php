<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$id_user         = $_SESSION['id_user'];
$id_supplier     = (int) $_POST['id_supplier'];
$total_pembelian = (float) ($_POST['total_pembelian'] ?? 0);
$items_json      = $_POST['items'] ?? '[]';
$items           = json_decode($items_json, true);

if (empty($items) || $total_pembelian <= 0 || !$id_supplier) {
    $_SESSION['error'] = 'Data pembelian tidak valid atau kosong.';
    header('Location: tambah.php');
    exit;
}

$conn->begin_transaction();

try {
    $tanggal = date('Y-m-d H:i:s');

    // 1. INSERT TRANSAKSI PEMBELIAN
    $stmt = $conn->prepare("INSERT INTO pembelian (id_supplier, id_user, tanggal_pembelian, total_pembelian) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iisd', $id_supplier, $id_user, $tanggal, $total_pembelian);
    $stmt->execute();
    $id_pembelian = $conn->insert_id;
    $stmt->close();

    // 2. INSERT DETAIL & UPDATE STOK PRODUK
    $stmt_detail = $conn->prepare("INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_beli, subtotal, no_batch, expired) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Query untuk update stok
    $stmt_stok = $conn->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");

    foreach ($items as $item) {
        $id_produk = (int) $item['id_produk'];
        $jumlah    = (int) $item['jumlah'];
        $harga     = (float) $item['harga_beli'];
        $subtotal  = $harga * $jumlah;
        $no_batch  = !empty($item['no_batch']) ? $item['no_batch'] : NULL;
        $expired   = !empty($item['expired']) ? $item['expired'] : NULL;

        // Insert Detail
        $stmt_detail->bind_param('iiiddss', $id_pembelian, $id_produk, $jumlah, $harga, $subtotal, $no_batch, $expired);
        $stmt_detail->execute();

        // Update Stok Otomatis
        $stmt_stok->bind_param('ii', $jumlah, $id_produk);
        $stmt_stok->execute();
    }

    $stmt_detail->close();
    $stmt_stok->close();

    $conn->commit();
    $_SESSION['success'] = "Pembelian #" . str_pad($id_pembelian, 5, '0', STR_PAD_LEFT) . " berhasil disimpan dan stok telah ditambahkan.";
    header("Location: detail.php?id=$id_pembelian");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
    header('Location: tambah.php');
    exit;
}
?>