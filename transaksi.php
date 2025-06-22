<?php
ob_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

// Ambil data produk
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY nama_produk");

// Proses simpan transaksi
$sukses = false;
if (isset($_POST['simpan'])) {
  $produk_id = intval($_POST['produk']);
  $jumlah = intval($_POST['jumlah']);

  $data_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id = $produk_id"));
  $total = $data_produk['harga'] * $jumlah;

  mysqli_query($conn, "INSERT INTO transaksi (produk_id, jumlah, total, status) VALUES ($produk_id, $jumlah, $total, 'pending')");

  // Update stok
  mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id = $produk_id");

  $sukses = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Transaksi Penjualan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f8;
      padding: 40px;
    }
    h2 {
      margin-bottom: 20px;
    }
    form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      max-width: 500px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }
    select, input {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      padding: 10px 16px;
      background: #38b6ff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background: #319edb;
    }
    .alert {
      background: #d1e7dd;
      color: #0f5132;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      max-width: 500px;
    }
  </style>
  <script>
    function updateTotal() {
      const harga = parseInt(document.querySelector('select option:checked').dataset.harga || 0);
      const qty = parseInt(document.getElementById('jumlah').value) || 0;
      const total = harga * qty;
      document.getElementById('total').value = 'Rp ' + total.toLocaleString();
    }
  </script>
</head>
<body>
  <h2>🛒 Transaksi Penjualan</h2>
  <?php if ($sukses): ?>
    <div class="alert">✅ Transaksi berhasil disimpan.
      <script>
        setTimeout(function() {
          window.location.href = 'riwayat.php';
        }, 2000);
      </script>
    </div>
  <?php endif; ?>

  <form method="POST">
    <label for="produk">Pilih Produk</label>
    <select name="produk" id="produk" onchange="updateTotal()" required>
      <option value="">-- Pilih Produk --</option>
      <?php while ($row = mysqli_fetch_assoc($produk)): ?>
        <option value="<?= $row['id'] ?>" data-harga="<?= $row['harga'] ?>">
          <?= htmlspecialchars($row['nama_produk']) ?> (Stok: <?= $row['stok'] ?>)
        </option>
      <?php endwhile; ?>
    </select>

    <label for="jumlah">Jumlah</label>
    <input type="number" name="jumlah" id="jumlah" min="1" oninput="updateTotal()" required>

    <label for="total">Total Harga</label>
    <input type="text" id="total" disabled>

    <button type="submit" name="simpan">Simpan Transaksi</button>
  </form>
</body>
</html>
<?php ob_end_flush(); ?>
