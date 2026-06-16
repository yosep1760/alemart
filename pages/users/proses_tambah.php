<?php
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../auth/auth_check.php';
include __DIR__ . '/../../auth/isAdmin.php';
require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama     = trim(htmlspecialchars($_POST['nama']));
$username = trim(htmlspecialchars($_POST['username']));
$password = trim(htmlspecialchars($_POST['password']));
$role     = trim(htmlspecialchars($_POST['role']));

if (empty($nama) || empty($username) || empty($password) || empty($role)) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: tambah.php");
    exit;
}

$username_check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
if (mysqli_num_rows($username_check) > 0) {
    $_SESSION['error'] = "Username sudah digunakan.";
    header("Location: tambah.php");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$avatar = null;

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $target_dir = __DIR__ . "/../../assets/uploads/avatar/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $max_size = 2 * 1024 * 1024;
    if ($_FILES['avatar']['size'] > $max_size) {
        $_SESSION['error'] = "Ukuran avatar maksimal 2MB.";
        header("Location: tambah.php");
        exit;
    }

    $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowed)) {
        $_SESSION['error'] = "Format avatar tidak valid.";
        header("Location: tambah.php");
        exit;
    }

    $avatar = uniqid('avatar_', true) . '.' . $extension;
    $target_file = $target_dir . $avatar;

    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
        $_SESSION['error'] = "Gagal upload avatar.";
        header("Location: tambah.php");
        exit;
    }
}

$query = mysqli_query($conn, "INSERT INTO users (nama, username, password, role, avatar) VALUES ('$nama', '$username', '$password_hash', '$role', " . ($avatar ? "'$avatar'" : "NULL") . ")");

if ($query) {
    $_SESSION['success'] = "User berhasil ditambahkan.";
} else {
    // Perbaikan BUG path jika proses database gagal maka file terhapus dengan path yang benar
    if ($avatar && file_exists(__DIR__ . "/../../assets/uploads/avatar/" . $avatar)) {
        unlink(__DIR__ . "/../../assets/uploads/avatar/" . $avatar);
    }
    $_SESSION['error'] = "Gagal menambahkan user.";
}

header("Location: index.php");
exit;
?>