<?php
// 1. MEMANGGIL FILE WAJIB
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';
// Memanggil file ini agar mengecek sesi/login user
include __DIR__ . '/../../auth/auth_check.php';

// ==========================================
// 2. CEK PARAMETER ID
// ==========================================
// Cek apakah ada ID yang dikirim melalui URL (contoh: hapus.php?id=3).
// Jika seseorang iseng membuka 'hapus.php' secara langsung tanpa ID, tendang kembali ke index.php
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Mengubah ID menjadi tipe angka (Integer) untuk menghindari serangan SQL Injection.
$id = (int)$_GET['id'];

// ==========================================
// 3. LOGIKA PERLINDUNGAN DATA (MENCEGAH FATAL ERROR)
// ==========================================
// Cek apakah supplier ini sudah pernah menyuplai barang sebelumnya di tabel 'pembelian'.
$cek_pembelian = mysqli_query($conn, "SELECT id_pembelian FROM pembelian WHERE id_supplier = $id");

// Jika fungsi mysqli_num_rows() menghasilkan angka lebih dari 0, artinya supplier ini SEDANG DIPAKAI di data pembelian.
if (mysqli_num_rows($cek_pembelian) > 0) {
    // Buat pesan error peringatan
    $_SESSION['error'] = 'Supplier tidak dapat dihapus karena sudah tercatat dalam riwayat pembelian!';
    // Kembalikan ke halaman index tanpa menghapus data
    header("Location: index.php");
    exit; // Wajib dihentikan di sini agar kode DELETE di bawah tidak tereksekusi!
}

// ==========================================
// 4. EKSEKUSI HAPUS DATA
// ==========================================
// Jika lolos dari pengecekan di atas (artinya supplier ini belum pernah dipakai transaksi),
// maka jalankan perintah DELETE untuk menghapus supplier dari database secara permanen.
$query = mysqli_query($conn, "DELETE FROM supplier WHERE id_supplier = $id");

// Mengecek apakah perintah DELETE berhasil
if ($query) {
    // Jika berhasil, siapkan pop-up sukses
    $_SESSION['sukses'] = 'Supplier berhasil dihapus!';
} else {
    // Jika sistem database gagal menghapus, siapkan pop-up error
    $_SESSION['error'] = 'Gagal menghapus supplier!';
}

// Lemparkan kembali user ke halaman daftar supplier untuk melihat hasilnya
header("Location: index.php");
exit;