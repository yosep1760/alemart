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

// SIMPAN SESSION (Untuk Local)
$_SESSION['login'] = true;
$_SESSION['id_user'] = $user['id_user'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$_SESSION['avatar'] = $user['avatar'];

// SIMPAN COOKIE (Solusi Ampuh untuk Vercel Serverless)
setcookie('login', 'true', time() + 86400, '/');
setcookie('id_user', $user['id_user'], time() + 86400, '/');
setcookie('nama', $user['nama'], time() + 86400, '/');
setcookie('username', $user['username'], time() + 86400, '/');
setcookie('role', $user['role'], time() + 86400, '/');
setcookie('avatar', $user['avatar'], time() + 86400, '/');

header('Location: ' . BASE_URL . '/pages/dashboard/index.php');
exit;