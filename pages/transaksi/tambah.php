<?php
include __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Transaksi Baru';
$page = 'transaksi';

/* ========================= GET PRODUK ========================= */
$produk_query = mysqli_query($conn,
    "SELECT p.*, k.nama_kategori
     FROM produk p
     JOIN kategori k ON p.id_kategori = k.id_kategori
     WHERE p.stok > 0
     ORDER BY p.nama_produk ASC"
);

$daftar_produk = [];
while ($p = mysqli_fetch_assoc($produk_query)) {
    $daftar_produk[] = $p;
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Transaksi Baru</h2>
            <p class="text-muted mb-0">Buat transaksi penjualan baru</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-box-seam me-2 text-success"></i>Pilih Produk</h5>

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchProduk" class="form-control border-start-0"
                            placeholder="Cari produk...">
                    </div>

                    <div class="row g-2" id="produkGrid">
                        <?php foreach ($daftar_produk as $p): ?>
                            <div class="col-sm-6 col-md-4 produk-item"
                                data-nama="<?= strtolower(htmlspecialchars($p['nama_produk'])); ?>">
                                <div class="card border h-100 produk-card"
                                    style="cursor:pointer; transition: all 0.15s;"
                                    data-id="<?= $p['id_produk']; ?>"
                                    data-nama="<?= htmlspecialchars($p['nama_produk']); ?>"
                                    data-harga="<?= $p['harga_jual']; ?>"
                                    data-stok="<?= $p['stok']; ?>"
                                    data-satuan="<?= $p['satuan']; ?>">
                                    <div class="card-body p-3">
                                        <p class="fw-semibold mb-1 small"><?= htmlspecialchars($p['nama_produk']); ?></p>
                                        <p class="text-success fw-bold mb-1 small">
                                            Rp <?= number_format($p['harga_jual'], 0, ',', '.'); ?>
                                        </p>
                                        <span class="badge bg-light text-dark border small stok-badge-<?= $p['id_produk']; ?>">
                                            Stok: <?= $p['stok']; ?> <?= $p['satuan']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($daftar_produk)): ?>
                            <div class="col-12 text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada produk tersedia
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: calc(var(--navbar-height, 70px) + 16px); z-index: 100;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-cart3 me-2 text-success"></i>Keranjang</h5>

                    <div id="keranjangList" style="min-height: 120px; max-height: 320px; overflow-y: auto;"></div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span class="text-success" id="totalDisplay">Rp 0</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Bayar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="inputBayar" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="d-flex gap-2 mt-2 flex-wrap" id="quickPay"></div>
                    </div>

                    <div class="d-flex justify-content-between fw-semibold mb-4">
                        <span>Kembalian</span>
                        <span id="kembalianDisplay" class="text-primary">Rp 0</span>
                    </div>

                    <button type="button" id="btnBayar" class="btn btn-success w-100 fw-bold py-2" disabled>
                        <i class="bi bi-cash-stack me-2"></i> Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light rounded-3 p-3 mb-3" id="modalRingkasan"></div>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-success" id="modalTotal"></span>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span>Bayar</span>
                    <span id="modalBayar"></span>
                </div>
                <div class="d-flex justify-content-between fw-semibold text-primary">
                    <span>Kembalian</span>
                    <span id="modalKembalian"></span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnKonfirmasi" class="btn btn-success fw-bold">
                    <i class="bi bi-check-lg me-1"></i> Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="proses_tambah.php" id="formTransaksi">
    <input type="hidden" name="total_harga" id="inputTotal">
    <input type="hidden" name="bayar" id="inputBayarHidden">
    <input type="hidden" name="kembalian" id="inputKembalian">
    <input type="hidden" name="items" id="inputItems">
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
    let keranjang = [];

    const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

    /* ========================= SEARCH PRODUK ========================= */
    document.getElementById('searchProduk').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.produk-item').forEach(el => {
            el.style.display = el.dataset.nama.includes(q) ? '' : 'none';
        });
    });

    /* ========================= TAMBAH KE KERANJANG ========================= */
    // Pasang event listener sekali saja ke grid (event delegation)
    document.getElementById('produkGrid').addEventListener('click', function (e) {
        const card = e.target.closest('.produk-card');
        if (!card) return;

        const id    = card.dataset.id;
        const nama  = card.dataset.nama;
        const harga = parseInt(card.dataset.harga);
        const stok  = parseInt(card.dataset.stok);

        const existing = keranjang.find(i => i.id === id);
        if (existing) {
            if (existing.jumlah >= stok) {
                Swal.fire({ icon: 'warning', title: 'Stok tidak cukup', timer: 1500, showConfirmButton: false });
                return;
            }
            existing.jumlah++;
        } else {
            keranjang.push({ id, nama, harga, stok, jumlah: 1 });
        }

        renderKeranjang();
    });

    /* ========================= RENDER KERANJANG ========================= */
    function renderKeranjang() {
        const list = document.getElementById('keranjangList');

        if (keranjang.length === 0) {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-cart-x fs-2 d-block mb-2"></i>
                    Keranjang masih kosong
                </div>`;
            updateTotal();
            return;
        }

        list.innerHTML = keranjang.map((item, idx) => `
            <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded-3">
                <div class="flex-grow-1">
                    <div class="fw-semibold small">${item.nama}</div>
                    <div class="text-success small">${formatRp(item.harga)}</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-2 btn-kurang" data-idx="${idx}">−</button>
                    <span class="fw-bold px-1">${item.jumlah}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-2 btn-tambah" data-idx="${idx}">+</button>
                </div>
                <div class="text-end" style="min-width:80px">
                    <div class="fw-bold small">${formatRp(item.harga * item.jumlah)}</div>
                    <button type="button" class="btn btn-link text-danger p-0 small btn-hapus-item" data-idx="${idx}">
                         <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

        // Event delegation untuk tombol +/-/hapus di keranjang
        list.querySelectorAll('.btn-kurang').forEach(btn => {
            btn.addEventListener('click', () => ubahJumlah(parseInt(btn.dataset.idx), -1));
        });

        list.querySelectorAll('.btn-tambah').forEach(btn => {
            btn.addEventListener('click', () => ubahJumlah(parseInt(btn.dataset.idx), 1));
        });

        list.querySelectorAll('.btn-hapus-item').forEach(btn => {
            btn.addEventListener('click', () => hapusItem(parseInt(btn.dataset.idx)));
        });

        updateTotal();
    }

    function ubahJumlah(idx, delta) {
        keranjang[idx].jumlah += delta;

        if (keranjang[idx].jumlah <= 0) {
            keranjang.splice(idx, 1);
        } else if (keranjang[idx].jumlah > keranjang[idx].stok) {
            keranjang[idx].jumlah = keranjang[idx].stok;
            Swal.fire({ icon: 'warning', title: 'Stok tidak cukup', timer: 1500, showConfirmButton: false });
        }
        renderKeranjang();
    }

    function hapusItem(idx) {
        keranjang.splice(idx, 1);
        renderKeranjang();
    }

    /* ========================= HITUNG TOTAL ========================= */
    function getTotal() {
        return keranjang.reduce((sum, i) => sum + i.harga * i.jumlah, 0);
    }

    function updateTotal() {
        const total = getTotal();
        document.getElementById('totalDisplay').textContent = formatRp(total);
        hitungKembalian();
        renderQuickPay(total);
        validasiBayar();
    }

    /* ========================= QUICK PAY ========================= */
    function renderQuickPay(total) {
        const container = document.getElementById('quickPay');
        container.innerHTML = '';
        if (total <= 0) return;

        const pecahan = [1000, 2000, 5000, 10000, 20000, 50000, 100000];
        const options = new Set();
        options.add(total);
        pecahan.forEach(n => {
            const mul = Math.ceil(total / n) * n;
            options.add(mul);
        });

        [...options].sort((a, b) => a - b).slice(0, 4).forEach(val => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-success btn-sm';
            btn.textContent = formatRp(val);
            btn.addEventListener('click', () => {
                document.getElementById('inputBayar').value = val;
                hitungKembalian();
                validasiBayar();
            });
            container.appendChild(btn);
        });
    }

    /* ========================= KEMBALIAN ========================= */
    function hitungKembalian() {
        const total = getTotal();
        const bayar = parseInt(document.getElementById('inputBayar').value) || 0;
        const kembalian = bayar - total;
        const el = document.getElementById('kembalianDisplay');
        el.textContent = formatRp(Math.max(kembalian, 0));
        el.className = kembalian >= 0 ? 'text-primary fw-semibold' : 'text-danger fw-semibold';
    }

    document.getElementById('inputBayar').addEventListener('input', () => {
        hitungKembalian();
        validasiBayar();
    });

    /* ========================= VALIDASI TOMBOL BAYAR ========================= */
    function validasiBayar() {
        const total = getTotal();
        const bayar = parseInt(document.getElementById('inputBayar').value) || 0;
        document.getElementById('btnBayar').disabled = !(keranjang.length > 0 && bayar >= total && total > 0);
    }

    /* ========================= MODAL KONFIRMASI ========================= */
    document.getElementById('btnBayar').addEventListener('click', function () {
        const total = getTotal();
        const bayar = parseInt(document.getElementById('inputBayar').value);
        const kembalian = bayar - total;

        document.getElementById('modalRingkasan').innerHTML = keranjang.map(i => `
            <div class="d-flex justify-content-between small">
                <span>${i.nama} x${i.jumlah}</span>
                <span>${formatRp(i.harga * i.jumlah)}</span>
            </div>
        `).join('');

        document.getElementById('modalTotal').textContent = formatRp(total);
        document.getElementById('modalBayar').textContent = formatRp(bayar);
        document.getElementById('modalKembalian').textContent = formatRp(kembalian);

        new bootstrap.Modal(document.getElementById('modalKonfirmasi')).show();
    });

    /* ========================= SUBMIT ========================= */
    document.getElementById('btnKonfirmasi').addEventListener('click', function () {
        const total = getTotal();
        const bayar = parseInt(document.getElementById('inputBayar').value);
        const kembalian = bayar - total;

        document.getElementById('inputTotal').value = total;
        document.getElementById('inputBayarHidden').value = bayar;
        document.getElementById('inputKembalian').value = kembalian;
        document.getElementById('inputItems').value = JSON.stringify(
            keranjang.map(i => ({ id: i.id, jumlah: i.jumlah, harga: i.harga }))
        );

        document.getElementById('formTransaksi').submit();
    });

    // Init keranjang kosong
    renderKeranjang();
</script>

<?php include __DIR__ . '/../../includes/footer_script.php'; ?>