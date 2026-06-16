# 🏪 Sistem Informasi Toko Kelontong

Sistem Informasi Toko Kelontong merupakan aplikasi berbasis web yang dibuat menggunakan PHP Native dan MySQL untuk membantu pengelolaan toko secara lebih efektif dan terstruktur.

## ⚙️ Fitur Utama

- Login & Logout
- Dashboard
- CRUD Produk
- CRUD Kategori
- CRUD Supplier
- CRUD User
- Transaksi Penjualan
- Rekap Data

---

## 👥 Anggota Kelompok

- Ale Awaludin Walid - 241061170005
- Muhammad Rafisyah Rizkiyawan - 241061170091
- Muhammad Rivaldi Yusa - 241061170092
- Muhammad Zidane Akbari - 241061170094
- Muhamad Yosep Maulana - 241061170152

---

## 💻 Teknologi yang Digunakan

- PHP Native
- MySQL
- HTML
- CSS
- JavaScript
- Bootstrap

---

## 🗄️ Struktur Database

Database terdiri dari beberapa tabel utama:

- users
- kategori
- supplier
- produk
- transaksi
- detail_transaksi

---

## 👤 Role Pengguna

### Admin
- Mengelola seluruh data sistem
- Mengelola akun pengguna

### Kasir
- Mengelola transaksi penjualan
- Melihat data produk

---

## 📁 Struktur Folder

```
/toko-kelontong
│
├── index.php                         # Halaman awal / redirect ke login
│
├── /auth                             # Folder autentikasi pengguna
│   ├── login.php                     # Halaman form login
│   ├── proses_login.php              # Proses validasi login
│   └── logout.php                    # Menghapus session/logout
│
├── /config                           # Konfigurasi sistem
│   └── koneksi.php                   # Koneksi database MySQL
│
├── /assets                           # File pendukung frontend
│   │
│   ├── /css
│   │
│   ├── /js
│   │
│   ├── /img
│   │
│   ├── /vendor                       # Bootstrap / library tambahan
|   |
│   └── /uploads                      
|       └── /produk                   # gambar produk yang di-upload
│
├── /includes                         # Komponen yang dipakai berulang
│   ├── header.php                    # Bagian atas halaman
│   ├── navbar.php                    # Navbar/menu atas
│   ├── sidebar.php                   # Sidebar menu
│   ├── footer.php                    # Footer halaman
│   └── session.php                   # Proteksi halaman login/session
│
├── /pages                            # Seluruh halaman utama sistem
│   │
│   ├── dashboard.php                 # Dashboard / rekap data
│   │
│   ├── /produk                       # CRUD data produk
│   │   ├── index.php                 # Menampilkan data produk
│   │   ├── tambah.php                # Form tambah produk
│   │   ├── edit.php                  # Form edit produk
│   │   └── hapus.php                 # Proses hapus produk
│   │
│   ├── /kategori                     # CRUD kategori produk
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── /supplier                     # CRUD supplier
│   │   ├── index.php
│   │   ├── tambah.php
│   │   ├── edit.php
│   │   └── hapus.php
│   │
│   ├── /transaksi                    # Modul transaksi penjualan
│   │   ├── index.php                 # Daftar transaksi
│   │   ├── tambah.php                # Form transaksi baru
│   │   ├── detail.php                # Detail transaksi
│   │   └── cetak.php                 # Cetak struk/nota (opsional)
│   │
│   └── /users                        # CRUD akun pengguna
│       ├── index.php
│       ├── tambah.php
│       ├── edit.php
│       └── hapus.php
│
├── /database
│   └── db_kelontong.sql              # File export database MySQL
│
└── README.md                         # Dokumentasi project
```

---

## 👟 Cara Menjalankan Project

**1. Clone repository**

```bash
git clone https://github.com/Laviz07/alemart.git
```

Letakkan folder di dalam `htdocs/` (XAMPP) atau `www/` (Laragon).

**2. Import database**

Buka phpMyAdmin di browser:

```
http://localhost/phpmyadmin
```

- Buat database baru bernama `alemart`
- Klik tab **Import**
- Pilih file `alemart.sql`
- Klik **Go**

**3. Konfigurasi koneksi**

Buka file `includes/koneksi.php` dan sesuaikan:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // sesuaikan
define('DB_PASS', '');           // sesuaikan
define('DB_NAME', 'alemart');
```

**4. Jalankan aplikasi**

```
http://localhost/alemart
```

---

## 🔒 Keamanan Sistem

- Password menggunakan hashing
- Session login
- Validasi input
- Proteksi halaman admin

---

## 📄 Lisensi

Project ini dibuat untuk kebutuhan tugas mata kuliah Pemrograman Web.
