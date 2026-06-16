<?php
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../auth/auth_check.php';
include __DIR__ . '/../../auth/isAdmin.php';
require_once __DIR__ . '/../../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

if ($id == $_SESSION['id_user']) {
    $_SESSION['error'] = "Kamu tidak bisa menghapus akun sendiri.";
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    $_SESSION['error'] = "User tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if (!empty($user['avatar']) && file_exists(__DIR__ . "/../../assets/uploads/avatar/" . $user['avatar'])) {
    unlink(__DIR__ . "/../../assets/uploads/avatar/" . $user['avatar']);
}

$cek_transaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_user = '$id'");
$data_transaksi = mysqli_fetch_assoc($cek_transaksi);

if ($data_transaksi['total'] > 0) {
    $_SESSION['error'] = "User tidak bisa dihapus karena sudah memiliki transaksi.";
    header("Location: index.php");
    exit;
}

$delete = mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id'");

if ($delete) {
    $_SESSION['success'] = "User berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus user.";
}

header("Location: index.php");
exit;
?>