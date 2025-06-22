<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

require('fpdf/fpdf.php');
include 'koneksi.php';

$query = "
  SELECT transaksi.*, produk.nama_produk 
  FROM transaksi 
  JOIN produk ON transaksi.produk_id = produk.id 
  ORDER BY transaksi.tanggal DESC
";
$result = mysqli_query($conn, $query);

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Riwayat Transaksi', 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(227,244,255);
$pdf->Cell(10, 10, 'No', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Nama Produk', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'Jumlah', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Total', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 11);
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(10, 9, $no++, 1, 0, 'C');
    $pdf->Cell(40, 9, date('d-m-Y H:i', strtotime($row['tanggal'])), 1, 0, 'C');
    $pdf->Cell(80, 9, $row['nama_produk'], 1, 0, 'L');
    $pdf->Cell(25, 9, $row['jumlah'], 1, 0, 'C');
    $pdf->Cell(40, 9, 'Rp ' . number_format($row['total'], 0, ',', '.'), 1, 1, 'R');
}

$pdf->Output('D', 'riwayat_transaksi.pdf');
exit;
