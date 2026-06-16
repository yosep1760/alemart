<?php
session_start();

// Panggil konfigurasi dengan absolute path __DIR__
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "/pages/dashboard/index.php");
} else {
    header("Location: " . BASE_URL . "/auth/login.php");
}
exit;
?>