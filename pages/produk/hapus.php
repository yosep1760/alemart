<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

// Validasi apakah parameter ID dikirimkan
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produk = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Ambil nama file gambar terlebih dahulu sebelum datanya dihapus
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM produk WHERE id_produk = '$id_produk'");
    $data_produk  = mysqli_fetch_assoc($query_gambar);

    if ($data_produk) {
        // Hapus file fisik gambar dari penyimpanan lokal jika ada
        if (!empty($data_produk['gambar']) && file_exists('../../assets/uploads/produk/' . $data_produk['gambar'])) {
            unlink('../../assets/uploads/produk/' . $data_produk['gambar']);
        }

        // 2. Hapus data produk dari tabel database
        $query_hapus = mysqli_query($conn, "DELETE FROM produk WHERE id_produk = '$id_produk'");

        if ($query_hapus) {
            $_SESSION['sukses'] = "Produk berhasil dihapus secara permanen!";
        } else {
            $_SESSION['error'] = "Gagal menghapus produk dari database: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data produk tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "Akses ditolak, ID produk tidak valid.";
}

// Kembalikan halaman otomatis ke index.php setelah proses selesai
header("Location: index.php");
exit();