<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$today = date('Y-m-d');
$produkList = [];
$pelangganList = [];

$produkResult = mysqli_query($koneksi, "SELECT ProdukID, NamaProduk, Harga, Stok FROM produk ORDER BY NamaProduk");
if ($produkResult) {
    while ($row = mysqli_fetch_assoc($produkResult)) {
        $produkList[] = $row;
    }
}

$pelangganResult = mysqli_query($koneksi, "SELECT PelangganID, NamaPelanggan FROM pelanggan ORDER BY NamaPelanggan");
if ($pelangganResult) {
    while ($row = mysqli_fetch_assoc($pelangganResult)) {
        $pelangganList[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produkID = intval($_POST['ProdukID'] ?? 0);
    $tanggalPenjualan = trim($_POST['TanggalPenjualan'] ?? '');
    $jumlahProduk = intval($_POST['JumlahProduk'] ?? 0);
    $pelangganID = intval($_POST['PelangganID'] ?? 0);

    if ($produkID <= 0 || $jumlahProduk <= 0 || empty($tanggalPenjualan) || $pelangganID <= 0) {
        $message = 'Lengkapi pilihan produk, tanggal, jumlah, dan pelanggan sebelum menyimpan.';
    } else {
        $tanggalPenjualan = mysqli_real_escape_string($koneksi, $tanggalPenjualan);
        $queryProduct = "SELECT Harga FROM produk WHERE ProdukID = $produkID LIMIT 1";
        $productQueryResult = mysqli_query($koneksi, $queryProduct);

        if (!$productQueryResult || mysqli_num_rows($productQueryResult) === 0) {
            $message = 'Produk tidak ditemukan di database.';
        } else {
            $product = mysqli_fetch_assoc($productQueryResult);
            $totalHarga = floatval($product['Harga']) * $jumlahProduk;
            $formattedTotal = number_format($totalHarga, 2, '.', '');

            $insertQuery = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID) VALUES ('$tanggalPenjualan', $formattedTotal, $pelangganID)";
            if (mysqli_query($koneksi, $insertQuery)) {
                $message = 'Transaksi berhasil disimpan ke riwayat.';
                header('Location: riwayat_transaksi.php?msg=' . urlencode($message));
                exit;
            } else {
                $message = 'Gagal menyimpan transaksi: ' . mysqli_error($koneksi);
            }
        }
    }
}

function formatRp($value) {
    return 'Rp ' . number_format($value, 2, ',', '.');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Kasir</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link active" href="transaksi.php">Transaksi</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="me-3 text-secondary">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a class="btn btn-outline-secondary btn-sm" href="login.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card p-4 shadow-sm">
                <h4>Transaksi</h4>
                <?php if ($message): ?>
                    <div class="alert alert-warning py-2"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <form id="transaksiForm" method="post">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select id="produkSelect" name="ProdukID" class="form-select" required>
                            <option value="">Pilih produk</option>
                            <?php foreach ($produkList as $produk): ?>
                                <option value="<?= $produk['ProdukID'] ?>" data-price="<?= $produk['Harga'] ?>" <?= isset($_POST['ProdukID']) && intval($_POST['ProdukID']) === intval($produk['ProdukID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($produk['NamaProduk']) ?> - <?= formatRp($produk['Harga']) ?> (Stok <?= htmlspecialchars($produk['Stok']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal penjualan</label>
                        <input type="date" name="TanggalPenjualan" class="form-control" required value="<?= htmlspecialchars($_POST['TanggalPenjualan'] ?? $today) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah produk</label>
                        <input type="number" id="jumlahProduk" name="JumlahProduk" class="form-control" min="1" value="<?= htmlspecialchars($_POST['JumlahProduk'] ?? 1) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <select name="PelangganID" class="form-select" required>
                            <option value="">Pilih pelanggan</option>
                            <?php foreach ($pelangganList as $pelanggan): ?>
                                <option value="<?= $pelanggan['PelangganID'] ?>" <?= isset($_POST['PelangganID']) && intval($_POST['PelangganID']) === intval($pelanggan['PelangganID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pelanggan['NamaPelanggan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card p-4 shadow-sm">
                <h4>Total Harga</h4>
                <div class="p-4 mb-4 border rounded bg-light text-center fs-3" id="totalDisplay">Rp 0,00</div>
                <button type="submit" form="transaksiForm" class="btn btn-primary w-100" <?= empty($produkList) || empty($pelangganList) ? 'disabled' : '' ?>>SIMPAN</button>
                <?php if (empty($produkList) || empty($pelangganList)): ?>
                    <div class="text-muted mt-3">Tambahkan produk dan pelanggan terlebih dahulu untuk menyimpan transaksi.</div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-3">
                <a href="riwayat_transaksi.php" class="btn btn-link">Lihat riwayat penjualan</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const produkSelect = document.getElementById('produkSelect');
    const jumlahInput = document.getElementById('jumlahProduk');
    const totalDisplay = document.getElementById('totalDisplay');

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 2 }).format(value);
    }

    function updateTotal() {
        const selected = produkSelect.selectedOptions[0];
        const price = selected ? parseFloat(selected.dataset.price || 0) : 0;
        const qty = parseInt(jumlahInput.value, 10) || 0;
        const total = price * qty;
        totalDisplay.textContent = formatRupiah(total);
    }

    produkSelect.addEventListener('change', updateTotal);
    jumlahInput.addEventListener('input', updateTotal);
    updateTotal();
</script>
</body>
</html>
