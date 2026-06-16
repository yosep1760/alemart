<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Panggil file ini agar Cookie Vercel terbaca dan kamu tidak ditendang ke login!
include __DIR__ . '/../../auth/auth_check.php';

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