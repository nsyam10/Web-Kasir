<?php
// session_start(); // Hapus atau komentari baris ini
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

// Ambil data total penjualan per produk
$query = "
  SELECT produk.nama_produk, SUM(transaksi.total) AS total_penjualan
  FROM transaksi
  JOIN produk ON transaksi.produk_id = produk.id
  GROUP BY transaksi.produk_id
  ORDER BY total_penjualan DESC
";
$result = mysqli_query($conn, $query);

// Siapkan data untuk grafik per produk
$labels = [];
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $labels[] = $row['nama_produk'];
  $data[] = $row['total_penjualan'];
}

// Ambil data total penjualan per bulan
$query_bulan = "
  SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, SUM(total) AS total_bulanan
  FROM transaksi
  GROUP BY bulan
  ORDER BY bulan ASC
";
$result_bulan = mysqli_query($conn, $query_bulan);

// Siapkan data untuk grafik per bulan
$labels_bulan = [];
$data_bulan = [];
while ($row = mysqli_fetch_assoc($result_bulan)) {
  // Format bulan ke "Mei 2024" dst
  $labels_bulan[] = date('M Y', strtotime($row['bulan'] . '-01'));
  $data_bulan[] = $row['total_bulanan'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Grafik Penjualan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f6f8fb;
      padding: 40px;
    }
    h2 { margin-bottom: 20px; }
    .chart-container {
      width: 100%;
      max-width: 800px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      margin-bottom: 36px;
    }
    canvas { margin-top: 20px; }
  </style>
</head>
<body>

<h2>📊 Grafik Penjualan per Produk</h2>
<div class="chart-container">
  <canvas id="grafikPenjualan"></canvas>
</div>

<h2>📈 Grafik Penjualan per Bulan</h2>
<div class="chart-container">
  <canvas id="grafikBulanan"></canvas>
</div>

<script>
  // Grafik per produk (ubah type ke 'line')
  const ctx = document.getElementById('grafikPenjualan').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($labels); ?>,
      datasets: [{
        label: 'Total Penjualan (Rp)',
        data: <?= json_encode($data); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.15)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.3,
        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString();
            }
          }
        }
      }
    }
  });

  // Grafik per bulan (tetap 'line')
  const ctxBulan = document.getElementById('grafikBulanan').getContext('2d');
  const chartBulan = new Chart(ctxBulan, {
    type: 'line',
    data: {
      labels: <?= json_encode($labels_bulan); ?>,
      datasets: [{
        label: 'Total Penjualan per Bulan (Rp)',
        data: <?= json_encode($data_bulan); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.15)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.3,
        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
</script>

</body>
</html>
