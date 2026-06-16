<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Cek Cookie Vercel terlebih dahulu
if (isset($_COOKIE['login']) && $_COOKIE['login'] === 'true') {
    header("Location: " . BASE_URL . "/pages/dashboard/index.php");
    exit;
}

if (isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "/pages/dashboard/index.php");
} else {
    header("Location: " . BASE_URL . "/auth/login.php");
}
exit;