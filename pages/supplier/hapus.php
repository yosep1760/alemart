<?php
session_start();
require_once '../../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Cek apakah supplier ini sudah pernah melakukan transaksi pembelian
$cek_pembelian = mysqli_query($conn, "SELECT id_pembelian FROM pembelian WHERE id_supplier = $id");
if (mysqli_num_rows($cek_pembelian) > 0) {
    $_SESSION['error'] = 'Supplier tidak dapat dihapus karena sudah tercatat dalam riwayat pembelian!';
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "DELETE FROM supplier WHERE id_supplier = $id");

if ($query) {
    $_SESSION['sukses'] = 'Supplier berhasil dihapus!';
} else {
    $_SESSION['error'] = 'Gagal menghapus supplier!';
}

header("Location: index.php");
exit;