<?php

require_once '../../config/config.php';
include '../../auth/auth_check.php';
include '../../auth/isAdmin.php';
require_once '../../config/koneksi.php';

/* =========================
   VALIDASI ID
========================= */
if (!isset($_GET['id'])) {

    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

/* =========================
   CEGAH HAPUS DIRI SENDIRI
========================= */
if ($id == $_SESSION['id_user']) {

    $_SESSION['error'] =
        "Kamu tidak bisa menghapus akun sendiri.";

    header("Location: index.php");
    exit;
}

/* =========================
   GET USER
========================= */
$query = mysqli_query(
    $conn,
    "SELECT * FROM users
     WHERE id_user = '$id'"
);

$user = mysqli_fetch_assoc($query);

/* jika user tidak ditemukan */
if (!$user) {

    $_SESSION['error'] =
        "User tidak ditemukan.";

    header("Location: index.php");
    exit;
}

/* =========================
   HAPUS AVATAR
========================= */
if (
    !empty($user['avatar']) &&
    file_exists(
        "../../assets/uploads/avatar/" .
            $user['avatar']
    )
) {

    unlink(
        "../../assets/uploads/avatar/" .
            $user['avatar']
    );
}

/* =========================
   HAPUS USER
========================= */

/* cek apakah user memiliki transaksi */
$cek_transaksi = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
     FROM transaksi
     WHERE id_user = '$id'"
);

$data_transaksi = mysqli_fetch_assoc($cek_transaksi);

if ($data_transaksi['total'] > 0) {

    $_SESSION['error'] =
        "User tidak bisa dihapus karena sudah memiliki transaksi.";

    header("Location: index.php");
    exit;
}

$delete = mysqli_query(
    $conn,
    "DELETE FROM users
     WHERE id_user = '$id'"
);

/* =========================
   RESULT
========================= */
if ($delete) {

    $_SESSION['success'] =
        "User berhasil dihapus.";
} else {

    $_SESSION['error'] =
        "Gagal menghapus user.";
}

header("Location: index.php");
exit;
