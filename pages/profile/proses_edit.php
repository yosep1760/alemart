<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id       = (int) $_POST['id_user'];
$nama     = trim(htmlspecialchars($_POST['nama']));
$username = trim(htmlspecialchars($_POST['username']));

if (empty($id) || empty($nama) || empty($username)) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: edit.php?id=$id");
    exit;
}

$nama     = mysqli_real_escape_string($conn, $nama);
$username = mysqli_real_escape_string($conn, $username);

$get_user = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id'");
$user_lama = mysqli_fetch_assoc($get_user);

if (!$user_lama) {
    $_SESSION['error'] = "User tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$check_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND id_user != '$id'");
if (mysqli_num_rows($check_username) > 0) {
    $_SESSION['error'] = "Username sudah digunakan.";
    header("Location: edit.php?id=$id");
    exit;
}

$update_avatar = "";

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    // Perbaikan path upload dengan __DIR__
    $target_dir = __DIR__ . "/../../assets/uploads/avatar/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $max_size = 2 * 1024 * 1024;
    if ($_FILES['avatar']['size'] > $max_size) {
        $_SESSION['error'] = "Ukuran avatar maksimal 2MB.";
        header("Location: edit.php?id=$id");
        exit;
    }

    $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowed)) {
        $_SESSION['error'] = "Format avatar tidak valid.";
        header("Location: edit.php?id=$id");
        exit;
    }

    $avatar = uniqid('avatar_', true) . '.' . $extension;
    $target_file = $target_dir . $avatar;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
        // Perbaikan path unlink dengan __DIR__
        if (!empty($user_lama['avatar']) && file_exists(__DIR__ . "/../../assets/uploads/avatar/" . $user_lama['avatar'])) {
            unlink(__DIR__ . "/../../assets/uploads/avatar/" . $user_lama['avatar']);
        }
        $update_avatar = ", avatar = '$avatar'";
    } else {
        $_SESSION['error'] = "Gagal upload avatar.";
        header("Location: edit.php?id=$id");
        exit;
    }
}

$query = mysqli_query($conn, "UPDATE users SET nama = '$nama', username = '$username' $update_avatar WHERE id_user = '$id'");

if ($query) {
    $_SESSION['nama'] = $nama;
    $_SESSION['username'] = $username;
    if (!empty($avatar)) {
        $_SESSION['avatar'] = $avatar;
    }
    $_SESSION['success'] = "Profil berhasil diperbarui.";
} else {
    $_SESSION['error'] = "Gagal memperbarui profil.";
}

header("Location: index.php");
exit;
?>