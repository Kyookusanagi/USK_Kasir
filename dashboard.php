<?php session_start(); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Kasir</title>
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
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="me-3 text-secondary">Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?></span>
                <a class="btn btn-outline-secondary btn-sm" href="login.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="bg-white rounded-4 p-4 shadow-sm">
                <h2>Dashboard</h2>
                <p class="text-muted">Ringkasan data kasir berdasarkan database kamu.</p>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Total Produk</h6>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Total Pelanggan</h6>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Total Transaksi</h6>
            </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
