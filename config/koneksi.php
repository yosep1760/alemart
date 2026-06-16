<?php
// Inisialisasi koneksi MySQLi
$conn = mysqli_init();

// TiDB mewajibkan SSL. Kode ini memberi tahu PHP untuk menggunakan koneksi aman.
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Masukkan kredensial TiDB Cloud kamu di sini
$host     = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$username = "e1aHBgKkkYs5ecU.root";                           
$password = "j8fnX6U6qOYDDicd";
$database = "alemart_db";                            
$port     = 4000;

// Lakukan koneksi
mysqli_real_connect($conn, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL);

if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}