<?php
// session_start(); // Hapus atau komentari baris ini
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

// Ambil semua transaksi + produk dengan status 'sold'
$query = "
  SELECT transaksi.*, produk.nama_produk 
  FROM transaksi 
  JOIN produk ON transaksi.produk_id = produk.id 
  WHERE transaksi.status = 'sold'
  ORDER BY transaksi.tanggal DESC
";
$riwayat = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Transaksi</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      padding: 40px;
      color: #333;
    }
    .riwayat-container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 24px #2b67771a;
      padding: 32px 28px 28px 28px;
    }
    h2 {
      margin-bottom: 24px;
      font-size: 1.6rem;
      color: #2b6777;
      letter-spacing: 1px;
      text-align: left;
    }
    .riwayat-actions {
      display: flex;
      gap: 12px;
      margin-bottom: 22px;
    }
    .riwayat-actions a {
      padding: 9px 18px;
      border-radius: 7px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      display: inline-block;
      transition: background 0.18s, box-shadow 0.18s;
      box-shadow: 0 2px 8px #38b6ff22;
    }
    .riwayat-actions a.export-excel {
      background: #2b6777;
      color: #fff;
    }
    .riwayat-actions a.export-excel:hover {
      background: #38b6ff;
      color: #fff;
    }
    .riwayat-actions a.export-print {
      background: #38b6ff;
      color: #fff;
    }
    .riwayat-actions a.export-print:hover {
      background: #2b6777;
      color: #fff;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 3px 8px rgba(0,0,0,0.03);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 13px 10px;
      border-bottom: 1px solid #f0f4f8;
      text-align: left;
      font-size: 1.04rem;
    }
    th {
      background-color: #e3f4ff;
      color: #2b6777;
      font-weight: 700;
      border-bottom: 2px solid #bde0fe;
    }
    tr:last-child td {
      border-bottom: none;
    }
    .total {
      font-weight: 700;
      color: #38b6ff;
    }
    @media (max-width: 700px) {
      .riwayat-container { padding: 12px 2vw; }
      table, th, td { font-size: 0.97rem; }
      .riwayat-actions { flex-direction: column; gap: 8px; }
    }
  </style>
</head>
<body>
  <div class="riwayat-container">
    <h2>📘 Riwayat Transaksi</h2>
    <div class="riwayat-actions">
      <a href="export_excel.php" class="export-excel">📊 Export ke Excel</a>
      <a href="export_print.php" target="_blank" class="export-print">🖨️ Print Riwayat</a>
    </div>
    <table>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Produk</th>
        <th>Jumlah</th>
        <th>Total</th>
        <th>Status</th>
      </tr>
      <?php $no = 1; while ($row = mysqli_fetch_assoc($riwayat)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
        <td><?= htmlspecialchars($row['nama_produk']) ?></td>
        <td><?= $row['jumlah'] ?></td>
        <td class="total">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
