<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Trik Vercel: Pindahkan data dari Cookie ke Session jika Session kosong
if (isset($_COOKIE['login']) && $_COOKIE['login'] === 'true') {
    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $_COOKIE['id_user'] ?? '';
    $_SESSION['nama'] = $_COOKIE['nama'] ?? '';
    $_SESSION['username'] = $_COOKIE['username'] ?? '';
    $_SESSION['role'] = $_COOKIE['role'] ?? '';
    $_SESSION['avatar'] = $_COOKIE['avatar'] ?? '';
}

// Cek apakah akhirnya user sudah login
if (!isset($_SESSION['login'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}