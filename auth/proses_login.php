<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$username = trim(htmlspecialchars($_POST['username'] ?? ''));
$password = trim(htmlspecialchars($_POST['password'] ?? ''));

if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Username dan password wajib diisi.";
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$username = mysqli_real_escape_string($conn, $username);
$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Username atau password salah.";
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Username atau password salah.";
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// AMANKAN DATA DARI NILAI NULL UNTUK COOKIE PHP 8+
$c_id       = isset($user['id_user']) ? (string)$user['id_user'] : '';
$c_nama     = isset($user['nama']) ? (string)$user['nama'] : '';
$c_username = isset($user['username']) ? (string)$user['username'] : '';
$c_role     = isset($user['role']) ? (string)$user['role'] : '';
$c_avatar   = isset($user['avatar']) ? (string)$user['avatar'] : '';

// SIMPAN SESSION (Untuk Local)
$_SESSION['login']    = true;
$_SESSION['id_user']  = $c_id;
$_SESSION['nama']     = $c_nama;
$_SESSION['username'] = $c_username;
$_SESSION['role']     = $c_role;
$_SESSION['avatar']   = $c_avatar;

// SIMPAN COOKIE (Solusi Ampuh untuk Vercel Serverless)
setcookie('login', 'true', time() + 86400, '/');
setcookie('id_user', $c_id, time() + 86400, '/');
setcookie('nama', $c_nama, time() + 86400, '/');
setcookie('username', $c_username, time() + 86400, '/');
setcookie('role', $c_role, time() + 86400, '/');
setcookie('avatar', $c_avatar, time() + 86400, '/');

header('Location: ' . BASE_URL . '/pages/dashboard/index.php');
exit;