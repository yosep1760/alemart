<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$id_user      = $_SESSION['id_user'];
$total_harga  = (float) ($_POST['total_harga'] ?? 0);
$bayar        = (float) ($_POST['bayar'] ?? 0);
$kembalian    = (float) ($_POST['kembalian'] ?? 0);
$items_json   = $_POST['items'] ?? '[]';
$items        = json_decode($items_json, true);

/* ========================= VALIDASI ========================= */
if (empty($items) || $total_harga <= 0 || $bayar < $total_harga) {
    $_SESSION['error'] = 'Data transaksi tidak valid.';
    header('Location: tambah.php');
    exit;
}

/* ========================= MULAI TRANSAKSI DB ========================= */
$conn->begin_transaction();

try {
    $tanggal = date('Y-m-d H:i:s');

    // INSERT transaksi
    $stmt = $conn->prepare(
        "INSERT INTO transaksi (id_user, tanggal_transaksi, total_harga, bayar, kembalian)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('issdd', $id_user, $tanggal, $total_harga, $bayar, $kembalian);
    $stmt->execute();
    $id_transaksi = $conn->insert_id;
    $stmt->close();

    // INSERT detail + update stok
    $stmt_detail = $conn->prepare(
        "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_jual, subtotal)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_stok = $conn->prepare(
        "UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND stok >= ?"
    );

    foreach ($items as $item) {
        $id_produk = (int) $item['id'];
        $jumlah    = (int) $item['jumlah'];
        $harga     = (float) $item['harga'];
        $subtotal  = $harga * $jumlah;

        // cek stok
        $cek = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT stok FROM produk WHERE id_produk = $id_produk FOR UPDATE"
        ));
        if (!$cek || $cek['stok'] < $jumlah) {
            throw new Exception("Stok produk tidak mencukupi (ID: $id_produk).");
        }

        $stmt_detail->bind_param('iiidd', $id_transaksi, $id_produk, $jumlah, $harga, $subtotal);
        $stmt_detail->execute();

        $stmt_stok->bind_param('iii', $jumlah, $id_produk, $jumlah);
        $stmt_stok->execute();
        if ($stmt_stok->affected_rows === 0) {
            throw new Exception("Gagal update stok produk (ID: $id_produk).");
        }
    }

    $stmt_detail->close();
    $stmt_stok->close();

    $conn->commit();

    $_SESSION['success'] = "Transaksi #" . str_pad($id_transaksi, 5, '0', STR_PAD_LEFT) . " berhasil disimpan.";
    header("Location: detail.php?id=$id_transaksi");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
    header('Location: tambah.php');
    exit;
}