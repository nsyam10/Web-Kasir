<?php
session_start();

// Hapus data keranjang dan pembayaran dari session
unset($_SESSION['struk_keranjang']);
unset($_SESSION['uang_bayar']);

// Arahkan kembali ke halaman riwayat
header("Location: riwayat.php");
exit;
