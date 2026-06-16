<?php
session_start();

if (isset($_SESSION['id_user'])) {
    header("Location: /pages/dashboard/index.php");
} else {
    header("Location: /auth/login.php");
}
exit;