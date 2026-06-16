<?php
session_start();

// Panggil konfigurasi dan koneksi dengan absolute path __DIR__
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| Cek Request Method
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Input
|--------------------------------------------------------------------------
*/
$username = trim(htmlspecialchars($_POST['username'] ?? ''));
$password = trim(htmlspecialchars($_POST['password'] ?? ''));

/*
|--------------------------------------------------------------------------
| Validasi Kosong
|--------------------------------------------------------------------------
*/
if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Username dan password wajib diisi.";
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Cari User
|--------------------------------------------------------------------------
*/
$username = mysqli_real_escape_string($conn, $username);
$query = "
    SELECT *
    FROM users
    WHERE username = '$username'
    LIMIT 1
";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Username atau password salah.";
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Verifikasi Password
|--------------------------------------------------------------------------
*/
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Username atau password salah.";
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Simpan Session
|--------------------------------------------------------------------------
*/
$_SESSION['login'] = true;
$_SESSION['id_user'] = $user['id_user'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$_SESSION['avatar'] = $user['avatar'];

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/
header('Location: ' . BASE_URL . '/pages/dashboard/index.php');
exit;
?>