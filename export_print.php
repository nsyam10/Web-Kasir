<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

$query = "
  SELECT transaksi.*, produk.nama_produk 
  FROM transaksi 
  JOIN produk ON transaksi.produk_id = produk.id 
  ORDER BY transaksi.tanggal DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Riwayat Transaksi</title>
  <style>
    body { font-family: 'Inter', Arial, sans-serif; color: #222; background: #fff; }
    h2 { text-align: center; margin-bottom: 24px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { border: 1px solid #bbb; padding: 8px 12px; }
    th { background: #e3f4ff; }
    .total { font-weight: 600; color: #2b6777; }
    @media print {
      button { display: none; }
      body { background: #fff; }
    }
  </style>
</head>
<body onload="window.print()">

<h2>Riwayat Transaksi</h2>
<table>
  <tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Nama Produk</th>
    <th>Jumlah</th>
    <th>Total</th>
  </tr>
  <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
    <td><?= $row['jumlah'] ?></td>
    <td class="total">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
  </tr>
  <?php endwhile; ?>
</table>
<button onclick="window.print()" style="padding:8px 18px;background:#38b6ff;color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Print</button>
</body>
</html>