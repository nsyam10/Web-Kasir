<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

include 'koneksi.php';

// Tambah produk
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    // Proses upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'img/menu_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar);
    }

    mysqli_query($conn, "INSERT INTO produk (nama_produk, harga, stok, gambar) VALUES ('$nama', $harga, $stok, '$gambar')");
    echo "<script>window.location='dashboard.php?page=produk';</script>";
    exit;
}

// Hapus produk
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi WHERE produk_id = $id");
    $cekData = mysqli_fetch_assoc($cek);
    if ($cekData['jml'] > 0) {
        echo "<script>alert('Produk tidak dapat dihapus karena sudah ada transaksi!');window.location='dashboard.php?page=produk';</script>";
        exit;
    }
    mysqli_query($conn, "DELETE FROM produk WHERE id = $id");
    echo "<script>window.location='dashboard.php?page=produk';</script>";
    exit;
}

// Ambil data produk
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Produk</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      padding: 30px;
      color: #333;
    }

    h2 {
      margin-bottom: 20px;
    }

    form {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    input {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      padding: 8px 14px;
      background: #38b6ff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background: #319edb;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background-color: #e3f4ff;
    }

    a {
      text-decoration: none;
      color: #38b6ff;
      margin-right: 8px;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<h2>🛒 Manajemen Produk</h2>

<form method="POST" enctype="multipart/form-data">
  <input type="text" name="nama" placeholder="Nama Produk" required>
  <input type="number" name="harga" placeholder="Harga" required>
  <input type="number" name="stok" placeholder="Stok" required>
  <input type="file" name="gambar" accept="image/*">
  <button type="submit" name="tambah">Tambah Produk</button>
</form>

<table>
  <tr>
    <th>No</th>
    <th>Nama Produk</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Aksi</th>
  </tr>
  <?php $no = 1; while ($row = mysqli_fetch_assoc($produk)): ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
    <td><?= $row['stok'] ?></td>
    <td>
      <a href="edit_produk.php?id=<?= $row['id'] ?>">Edit</a>
      <a href="produk.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>
