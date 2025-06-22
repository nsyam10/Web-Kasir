<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

// Cek apakah ada parameter ID
if (!isset($_GET['id'])) {
  header("Location: produk.php");
  exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
$produk = mysqli_fetch_assoc($result);

// Jika tidak ada data, kembali ke produk
if (!$produk) {
  echo "Data tidak ditemukan.";
  exit;
}

// Proses update
if (isset($_POST['update'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $harga = intval($_POST['harga']);
  $stok = intval($_POST['stok']);

  mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', harga=$harga, stok=$stok WHERE id=$id");
  header("Location: dashboard.php?page=produk");
  exit;
}

// Ambil data user untuk sidebar
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE username='{$_SESSION['username']}'"));
$profile_dir = 'img/profile/';
$default_img = 'img/logo.png';
$profile_img = $default_img;
foreach (['png', 'jpg', 'jpeg'] as $ext) {
  $path = $profile_dir . $_SESSION['username'] . '.' . $ext;
  if (file_exists($path)) {
    $profile_img = $path;
    break;
  }
}
$page = 'produk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Produk</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 260px;
      background: linear-gradient(135deg, #2b6777 60%, #38b6ff 100%);
      color: #fff;
      padding: 36px 24px 24px 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      box-shadow: 2px 0 16px rgba(44,103,119,0.10);
      border-top-right-radius: 32px;
      border-bottom-right-radius: 32px;
      min-height: 100vh;
    }
    .profile-img {
      width: 84px;
      height: 84px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #fff;
      margin-bottom: 12px;
      box-shadow: 0 4px 18px #38b6ff44, 0 1.5px 8px #2b677755;
      background: #fff;
      display: block;
      transition: transform 0.18s;
      margin-left: auto;
      margin-right: auto;
    }
    .profile-img:hover {
      transform: scale(1.06) rotate(-3deg);
    }
    .profile-name {
      margin-top: 10px;
      margin-bottom: 26px;
      text-align: center;
      font-size: 1.13rem;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(90deg, #38b6ff 40%, #2b6777 100%);
      padding: 9px 0 9px 0;
      border-radius: 14px;
      width: 100%;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px #38b6ff22;
      text-shadow: 0 2px 8px #2b6777cc;
      border: 1.5px solid #fff3;
      transition: background 0.2s;
    }
    .sidebar h2 {
      margin-bottom: 32px;
      font-size: 2rem;
      letter-spacing: 1px;
      text-shadow: 1px 2px 8px #1b3c47a0;
      text-align: center;
      width: 100%;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      padding: 12px 18px;
      margin-bottom: 10px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 1.08rem;
      font-weight: 500;
      transition: background 0.18s, transform 0.12s;
      position: relative;
      width: 100%;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255,255,255,0.18);
      transform: translateX(6px) scale(1.03);
      box-shadow: 0 2px 12px #38b6ff33;
    }
    .sidebar a.logout {
      margin-top: auto;
      background: #e74c3c;
      color: #fff;
      font-weight: 600;
      text-align: center;
      justify-content: center;
      transition: background 0.18s;
    }
    .sidebar a.logout:hover {
      background: #c0392b;
    }
    .content {
      flex: 1;
      padding: 48px 5vw;
      overflow-y: auto;
      background: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: flex-start;
      border-radius: 18px;
      box-shadow: 0 3px 16px rgba(44,103,119,0.07);
      margin: 32px 0 32px 0;
    }
    form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      max-width: 400px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      width: 100%;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }
    input {
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
    a.kembali {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #333;
    }
    @media (max-width: 900px) {
      .sidebar { width: 100px; padding: 24px 8px; }
      .sidebar h2, .sidebar a span { display: none; }
      .sidebar a { justify-content: center; padding: 12px 0; }
      .profile-img { width: 48px; height: 48px; margin-bottom: 6px; }
      .profile-name { font-size: 0.97rem; padding: 4px 0; margin-bottom: 10px; }
      .content { padding: 32px 2vw; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <a href="?page=profile" style="text-decoration:none;display:flex;flex-direction:column;align-items:center;">
      <img src="<?= $profile_img ?>?t=<?= time() ?>" alt="Profile" class="profile-img">
      <div class="profile-name">
        <?= htmlspecialchars($user['nama'] ?? $user['username']) ?>
      </div>
    </a>
    <h2><i class="fa-solid fa-cash-register"></i> Kasir</h2>
    <a href="dashboard.php?page=ringkasan" class="<?= $page=='ringkasan'?'active':'' ?>"><i class="fa-solid fa-chart-line"></i> <span>Ringkasan</span></a>
    <a href="dashboard.php?page=produk" class="<?= $page=='produk'?'active':'' ?>"><i class="fa-solid fa-box"></i> <span>Manajemen Produk</span></a>
    <a href="dashboard.php?page=transaksi" class="<?= $page=='transaksi'?'active':'' ?>"><i class="fa-solid fa-money-bill-wave"></i> <span>Transaksi Penjualan</span></a>
    <a href="dashboard.php?page=riwayat" class="<?= $page=='riwayat'?'active':'' ?>"><i class="fa-solid fa-receipt"></i> <span>Riwayat Transaksi</span></a>
    <a href="dashboard.php?page=grafik" class="<?= $page=='grafik'?'active':'' ?>"><i class="fa-solid fa-chart-bar"></i> <span>Grafik Penjualan</span></a>
    <a href="dashboard.php?page=menu_makanan" class="<?= $page=='menu_makanan'?'active':'' ?>"><i class="fa-solid fa-utensils"></i> <span>Menu Makanan</span></a>
    <a href="logout.php" class="logout"><i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span></a>
  </div>
  <div class="content">
    <h2>✏️ Edit Produk</h2>
    <form method="POST">
      <label for="nama">Nama Produk</label>
      <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>

      <label for="harga">Harga</label>
      <input type="number" name="harga" id="harga" value="<?= $produk['harga'] ?>" required>

      <label for="stok">Stok</label>
      <input type="number" name="stok" id="stok" value="<?= $produk['stok'] ?>" required>

      <button type="submit" name="update">Simpan Perubahan</button>
      <a href="produk.php" class="kembali">← Kembali</a>
    </form>
  </div>
</body>
</html>
