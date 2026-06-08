<?php

session_start();

require_once '../../config/config.php';
require_once '../../config/koneksi.php';

/* =========================
   VALIDASI METHOD
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;
}

/* =========================
   AMBIL DATA
========================= */
$id       = (int) $_POST['id_user'];
$nama     = trim(htmlspecialchars($_POST['nama']));
$username = trim(htmlspecialchars($_POST['username']));
$password = trim(htmlspecialchars($_POST['password']));
$role     = trim($_POST['role']);

/* =========================
   VALIDASI INPUT
========================= */
if (
    empty($id) ||
    empty($nama) ||
    empty($username) ||
    empty($role)
) {

    $_SESSION['error'] =
        "Semua field wajib diisi.";

    header("Location: edit.php?id=$id");
    exit;
}

/* =========================
   ESCAPE STRING
========================= */
$nama     = mysqli_real_escape_string($conn, $nama);
$username = mysqli_real_escape_string($conn, $username);
$role     = mysqli_real_escape_string($conn, $role);

/* =========================
   CEK USER
========================= */
$get_user = mysqli_query(
    $conn,
    "SELECT * FROM users
     WHERE id_user = '$id'"
);

$user_lama = mysqli_fetch_assoc($get_user);

if (!$user_lama) {

    $_SESSION['error'] =
        "User tidak ditemukan.";

    header("Location: index.php");
    exit;
}

/* =========================
   CEK USERNAME DUPLIKAT
========================= */
$check_username = mysqli_query(
    $conn,
    "SELECT * FROM users
     WHERE username = '$username'
     AND id_user != '$id'"
);

if (mysqli_num_rows($check_username) > 0) {

    $_SESSION['error'] =
        "Username sudah digunakan.";

    header("Location: edit.php?id=$id");
    exit;
}

/* =========================
   PASSWORD
========================= */
$update_password = "";

if (!empty($password)) {

    $password_hash =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );

    $update_password =
        ", password = '$password_hash'";
}

/* =========================
   AVATAR
========================= */
$update_avatar = "";

/* jika upload avatar baru */
if (
    isset($_FILES['avatar']) &&
    $_FILES['avatar']['error'] === 0
) {

    $target_dir =
        "../../assets/uploads/avatar/";

    /* buat folder jika belum ada */
    if (!is_dir($target_dir)) {

        mkdir($target_dir, 0777, true);
    }

    /* validasi ukuran */
    $max_size = 2 * 1024 * 1024;

    if ($_FILES['avatar']['size'] > $max_size) {

        $_SESSION['error'] =
            "Ukuran avatar maksimal 2MB.";

        header("Location: edit.php?id=$id");
        exit;
    }

    /* extension */
    $extension =
        strtolower(
            pathinfo(
                $_FILES['avatar']['name'],
                PATHINFO_EXTENSION
            )
        );

    /* validasi extension */
    $allowed =
        ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $allowed)) {

        $_SESSION['error'] =
            "Format avatar tidak valid.";

        header("Location: edit.php?id=$id");
        exit;
    }

    /* generate nama file */
    $avatar =
        uniqid('avatar_', true) .
        '.' .
        $extension;

    $target_file =
        $target_dir . $avatar;

    /* upload file */
    if (
        move_uploaded_file(
            $_FILES['avatar']['tmp_name'],
            $target_file
        )
    ) {

        /* =========================
           HAPUS AVATAR LAMA
        ========================= */
        if (
            !empty($user_lama['avatar']) &&
            file_exists(
                "../../assets/uploads/avatar/" .
                    $user_lama['avatar']
            )
        ) {

            unlink(
                "../../assets/uploads/avatar/" .
                    $user_lama['avatar']
            );
        }

        $update_avatar =
            ", avatar = '$avatar'";
    } else {

        $_SESSION['error'] =
            "Gagal upload avatar.";

        header("Location: edit.php?id=$id");
        exit;
    }
}

/* =========================
   UPDATE USER
========================= */
$query = mysqli_query(
    $conn,
    "UPDATE users SET

        nama = '$nama',
        username = '$username',
        role = '$role'

        $update_password
        $update_avatar

     WHERE id_user = '$id'"
);

/* =========================
   RESULT
========================= */
if ($query) {

    $_SESSION['success'] =
        "User berhasil diperbarui.";
} else {

    $_SESSION['error'] =
        "Gagal memperbarui user.";
}

header("Location: index.php");
exit;
