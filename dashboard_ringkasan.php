<?php
// session_start(); // Hapus atau komentari baris ini
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

// Total produk
$produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk"))['total'];

// Total transaksi
$transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi"))['total'];

// Total omset semua
$omset = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS total FROM transaksi"))['total'];

// Omset hari ini
$tanggal_hari_ini = date('Y-m-d');
$omset_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS total FROM transaksi WHERE DATE(tanggal) = '$tanggal_hari_ini'"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Ringkasan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f2f4f7;
      padding: 40px;
    }

    h2 {
      margin-bottom: 30px;
    }

    .card-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
      padding: 20px;
      flex: 1 1 200px;
    }

    .card h3 {
      margin: 0;
      font-size: 18px;
      color: #555;
    }

    .card p {
      font-size: 24px;
      font-weight: bold;
      margin-top: 10px;
      color: #2b6777;
    }

    .card .icon {
      font-size: 32px;
      float: right;
      color: #38b6ff;
    }
  </style>
</head>
<body>

<h2>📊 Ringkasan Dashboard</h2>

<div class="card-container">
  <div class="card">
    <span class="icon">📦</span>
    <h3>Jumlah Produk</h3>
    <p><?= $produk ?></p>
  </div>

  <div class="card">
    <span class="icon">🧾</span>
    <h3>Jumlah Transaksi</h3>
    <p><?= $transaksi ?></p>
  </div>

  <div class="card">
    <span class="icon">💰</span>
    <h3>Total Omset</h3>
    <p>Rp <?= number_format($omset ?? 0, 0, ',', '.') ?></p>
  </div>

  <div class="card">
    <span class="icon">📅</span>
    <h3>Omset Hari Ini</h3>
    <p>Rp <?= number_format($omset_hari_ini ?? 0, 0, ',', '.') ?></p>
  </div>
</div>

</body>
</html>
