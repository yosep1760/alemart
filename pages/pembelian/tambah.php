<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Pembelian Baru';
$page = 'pembelian';

/* GET SUPPLIER & PRODUK */
$supplier_list = mysqli_query($conn, "SELECT * FROM supplier ORDER BY nama_supplier ASC");
$produk_list = mysqli_query($conn, "SELECT id_produk, nama_produk, harga_beli, satuan FROM produk ORDER BY nama_produk ASC");

$produk_data = [];

while ($p = mysqli_fetch_assoc($produk_list)) {
    $produk_data[] = $p;
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Pembelian Barang Masuk</h2>
            <p class="text-muted mb-0">Input stok dari supplier</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-box-seam text-primary me-2"></i>Tambah Produk ke List</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Produk</label>
                        <select id="pilihProduk" class="form-select">
                            <option value="">-- Pilih --</option>
                            <?php foreach($produk_data as $p): ?>
                                <option value="<?= $p['id_produk']; ?>" data-harga="<?= $p['harga_beli']; ?>" data-nama="<?= $p['nama_produk']; ?>">
                                    <?= $p['nama_produk']; ?> (<?= $p['satuan']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Harga Beli Satuan (Rp)</label>
                            <input type="number" id="hargaBeli" class="form-control" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jumlah Masuk</label>
                            <input type="number" id="jumlahBarang" class="form-control" placeholder="1" min="1">
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold">No. Batch (Opsional)</label>
                            <input type="text" id="noBatch" class="form-control" placeholder="Misal: BATCH-01">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tgl Expired (Opsional)</label>
                            <input type="date" id="tglExpired" class="form-control">
                        </div>
                    </div>

                    <button type="button" id="btnTambahList" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Masukkan ke Daftar
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-list-check text-success me-2"></i>Daftar Barang Masuk</h5>
                    
                    <form method="POST" action="proses_tambah.php" id="formPembelian">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Supplier <span class="text-danger">*</span></label>
                            <select name="id_supplier" id="idSupplier" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php while ($sup = mysqli_fetch_assoc($supplier_list)): ?>
                                    <option value="<?= $sup['id_supplier']; ?>"><?= htmlspecialchars($sup['nama_supplier']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center"><i class="bi bi-gear"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="listBody">
                                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada barang ditambahkan</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-4">
                            <span class="fw-bold fs-5 text-muted">Total Pembelian:</span>
                            <span class="fw-bold fs-4 text-success" id="totalDisplay">Rp 0</span>
                        </div>

                        <input type="hidden" name="total_pembelian" id="inputTotal">
                        <input type="hidden" name="items" id="inputItems">
                        
                        <button type="button" id="btnSimpan" class="btn btn-success w-100 fw-bold py-2" disabled>
                            <i class="bi bi-save me-1"></i> Simpan Data & Tambah Stok
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
    let daftarBarang = [];
    const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

    // Auto fill Harga Beli saat produk dipilih
    document.getElementById('pilihProduk').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if(selected.value !== "") {
            document.getElementById('hargaBeli').value = selected.dataset.harga;
            document.getElementById('jumlahBarang').value = 1;
        } else {
            document.getElementById('hargaBeli').value = "";
        }
    });

    document.getElementById('btnTambahList').addEventListener('click', function() {
        const selectProd = document.getElementById('pilihProduk');
        const idProd = selectProd.value;
        const namaProd = selectProd.options[selectProd.selectedIndex]?.dataset.nama;
        const harga = parseFloat(document.getElementById('hargaBeli').value);
        const jumlah = parseInt(document.getElementById('jumlahBarang').value);
        const batch = document.getElementById('noBatch').value;
        const exp = document.getElementById('tglExpired').value;

        if (!idProd || !harga || !jumlah || jumlah < 1) {
            Swal.fire({icon: 'warning', title: 'Data belum lengkap', text: 'Pilih produk, harga, dan jumlah yang valid.'});
            return;
        }

        daftarBarang.push({ id_produk: idProd, nama_produk: namaProd, harga_beli: harga, jumlah: jumlah, no_batch: batch, expired: exp });
        
        // Reset form
        selectProd.value = "";
        document.getElementById('hargaBeli').value = "";
        document.getElementById('jumlahBarang').value = "";
        document.getElementById('noBatch').value = "";
        document.getElementById('tglExpired').value = "";
        
        renderTabel();
    });

    function hapusItem(index) {
        daftarBarang.splice(index, 1);
        renderTabel();
    }

    function renderTabel() {
        const tbody = document.getElementById('listBody');
        let total = 0;

        if (daftarBarang.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada barang ditambahkan</td></tr>';
            document.getElementById('totalDisplay').innerText = "Rp 0";
            document.getElementById('btnSimpan').disabled = true;
            return;
        }

        tbody.innerHTML = daftarBarang.map((item, idx) => {
            const subtotal = item.harga_beli * item.jumlah;
            total += subtotal;
            return `
            <tr>
                <td>
                    <span class="fw-semibold d-block">${item.nama_produk}</span>
                    <small class="text-muted">Batch: ${item.no_batch || '-'} | Exp: ${item.expired || '-'}</small>
                </td>
                <td class="text-center fw-bold">${item.jumlah}</td>
                <td class="text-end">${formatRp(item.harga_beli)}</td>
                <td class="text-end fw-bold text-success">${formatRp(subtotal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusItem(${idx})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
        }).join('');

        document.getElementById('totalDisplay').innerText = formatRp(total);
        document.getElementById('inputTotal').value = total;
        document.getElementById('inputItems').value = JSON.stringify(daftarBarang);
        
        validasiSimpan();
    }

    function validasiSimpan() {
        const idSupplier = document.getElementById('idSupplier').value;
        document.getElementById('btnSimpan').disabled = !(daftarBarang.length > 0 && idSupplier !== "");
    }

    document.getElementById('idSupplier').addEventListener('change', validasiSimpan);

    document.getElementById('btnSimpan').addEventListener('click', function() {
        if(daftarBarang.length > 0) {
            document.getElementById('formPembelian').submit();
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer_script.php'; ?>