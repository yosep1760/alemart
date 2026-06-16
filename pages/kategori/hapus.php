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

// Check if category is used in products
$cek_produk = mysqli_query($conn, "SELECT id_produk FROM produk WHERE id_kategori = $id");
if (mysqli_num_rows($cek_produk) > 0) {
    $_SESSION['error'] = 'Kategori tidak dapat dihapus karena masih digunakan pada produk!';
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = $id");

if ($query) {
    $_SESSION['sukses'] = 'Kategori berhasil dihapus!';
} else {
    $_SESSION['error'] = 'Gagal menghapus kategori!';
}

header("Location: index.php");
exit;
