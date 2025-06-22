<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=riwayat_transaksi.xls");

echo "No\tTanggal\tNama Produk\tJumlah\tTotal\n";

$query = "
  SELECT transaksi.*, produk.nama_produk 
  FROM transaksi 
  JOIN produk ON transaksi.produk_id = produk.id 
  ORDER BY transaksi.tanggal DESC
";
$result = mysqli_query($conn, $query);
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo $no++ . "\t";
    echo date('d-m-Y H:i', strtotime($row['tanggal'])) . "\t";
    echo $row['nama_produk'] . "\t";
    echo $row['jumlah'] . "\t";
    echo $row['total'] . "\n";
}
exit;