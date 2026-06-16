<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AleMart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/login.css">
</head>

<body>
    <div class="login-container">

        <div class="login-card shadow-lg">

            <div class="login-form">

                <div class="mb-4">
                    <h2 class="fw-bold">Masuk ke AleMart</h2>
                    <p class="text-muted">
                        Silakan login untuk mengakses sistem manajemen toko.
                    </p>
                </div>

                <?php if (isset($_SESSION['error'])) : ?>

                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; ?>
                    </div>

                <?php
                    unset($_SESSION['error']);
                endif;
                ?>

                <form id="loginForm" method="POST" action="proses_login.php">

                    <div class="mb-3">
                        <label class="form-label" for="username">
                            Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username">
                    </div>

                    <div class="mb-3">

                        <label class="form-label" for="password">
                            Password
                        </label>

                        <div class="input-group">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Masukkan password">

                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="togglePassword">

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>

                    <button
                        type="submit"
                        name="submit"
                        class="btn btn-success w-100">
                        Login
                    </button>

                </form>

            </div>

            <div class="welcome-panel">

                <div class="welcome-overlay"></div>

                <div class="welcome-content">

                    <h3>
                        Kelola Toko Lebih Mudah
                    </h3>

                    <p>
                        Kelola stok barang, supplier,
                        pembelian, dan transaksi penjualan
                        dalam satu sistem yang cepat,
                        praktis, dan terintegrasi.
                    </p>

                </div>

            </div>

        </div>

    </div>

    <script src="<?= BASE_URL; ?>/assets/js/login.js"></script>

</body>

</html>