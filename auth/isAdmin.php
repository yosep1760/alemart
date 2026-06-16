<?php

if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['role'] !== 'admin'
) {

    $_SESSION['error'] = "Anda tidak memiliki akses.";
    header("Location: " . BASE_URL . "/pages/dashboard/index.php");
    exit;
}

?>