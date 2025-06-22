<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';
$page = $_GET['page'] ?? 'ringkasan';

// Proses update profile jika ada POST dari profile.php
if ($page == 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  include 'profile.php';
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Kasir</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #2b6777;
      --secondary-color: #38b6ff;
      --danger-color: #e74c3c;
      --sidebar-width: 260px;
      --sidebar-collapsed-width: 100px;
      --content-margin: 300px;
      --content-margin-collapsed: 116px;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
      display: flex;
      min-height: 100vh;
      color: #333;
    }
    
    /* ========== IMPROVED SIDEBAR STYLES ========== */
    .sidebar {
      width: var(--sidebar-width);
      background: linear-gradient(135deg, var(--primary-color) 60%, var(--secondary-color) 100%);
      color: #fff;
      padding: 25px 15px;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      box-shadow: 2px 0 20px rgba(56, 182, 255, 0.15);
      border-top-right-radius: 25px;
      border-bottom-right-radius: 25px;
      z-index: 1000;
      transition: width 0.3s ease;
    }
    
    .profile-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
      margin-bottom: 25px;
      text-decoration: none;
    }
    
    .profile-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255, 255, 255, 0.8);
      box-shadow: 0 0 0 4px rgba(56, 182, 255, 0.3);
      background: #fff;
      transition: all 0.3s ease;
    }
    
    .profile-name {
      margin-top: 12px;
      text-align: center;
      font-size: 1rem;
      font-weight: 600;
      color: #fff;
      background: rgba(255, 255, 255, 0.1);
      padding: 8px 15px;
      border-radius: 20px;
      width: 100%;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      transition: all 0.3s ease;
    }
    
    .sidebar-title {
      text-align: center;
      margin-bottom: 25px;
      font-size: 1.5rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    
    .nav-menu {
      display: flex;
      flex-direction: column;
      gap: 8px;
      flex-grow: 1;
    }
    
    .nav-item {
      color: #fff;
      text-decoration: none;
      padding: 12px 18px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.95rem;
      font-weight: 500;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }
    
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.12);
      border-left: 3px solid #fff;
    }
    
    .nav-item.active {
      background: rgba(255, 255, 255, 0.2);
      font-weight: 600;
      border-left: 3px solid #fff;
    }
    
    .nav-item i {
      font-size: 1.1rem;
      width: 22px;
      text-align: center;
    }
    
    .logout-btn {
      margin-top: auto;
      background: var(--danger-color);
      justify-content: center;
      font-weight: 600;
      border-radius: 10px;
      padding: 12px;
      border-left: none !important;
    }
    
    .logout-btn:hover {
      background: #c0392b;
    }
    
    /* ========== CONTENT AREA ========== */
    .content {
      flex: 1;
      padding: 30px 5%;
      overflow-y: auto;
      background: #fff;
      min-height: 100vh;
      margin-left: var(--content-margin);
      border-radius: 20px 0 0 20px;
      box-shadow: 0 3px 20px rgba(44, 103, 119, 0.08);
      transition: margin 0.3s ease;
    }
    
    /* ========== RESPONSIVE STYLES ========== */
    @media (max-width: 992px) {
      .sidebar {
        width: var(--sidebar-collapsed-width);
        padding: 20px 10px;
      }
      
      .sidebar-title,
      .profile-name,
      .nav-item span {
        display: none;
      }
      
      .profile-img {
        width: 50px;
        height: 50px;
      }
      
      .nav-item {
        justify-content: center;
        padding: 12px 0;
      }
      
      .content {
        margin-left: var(--content-margin-collapsed);
        padding: 25px 3%;
      }
    }
    
    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        flex-wrap: wrap;
        padding: 15px;
        border-radius: 0;
        min-height: auto;
      }
      
      .profile-section {
        flex-direction: row;
        gap: 10px;
        margin-bottom: 15px;
        width: auto;
      }
      
      .profile-img {
        width: 40px;
        height: 40px;
      }
      
      .profile-name {
        display: block;
        width: auto;
        padding: 6px 12px;
        margin-top: 0;
      }
      
      .sidebar-title {
        display: none;
      }
      
      .nav-menu {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
      }
      
      .nav-item {
        padding: 8px 12px;
        border-radius: 20px;
        border-left: none;
        border-bottom: 2px solid transparent;
      }
      
      .nav-item:hover,
      .nav-item.active {
        border-left: none;
        border-bottom: 2px solid #fff;
      }
      
      .logout-btn {
        margin-top: 0;
      }
      
      .content {
        margin-left: 0;
        border-radius: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<?php
// Get user data
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE username='{$_SESSION['username']}'"));
?>
<div class="sidebar">
  <a href="?page=profile" class="profile-section">
    <?php
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
    ?>
    <img src="<?= htmlspecialchars($profile_img) ?>?t=<?= time() ?>" alt="Profile" class="profile-img">
    <div class="profile-name">
      <?= htmlspecialchars($user['nama'] ?? $user['username']) ?>
    </div>
  </a>
  
  <h2 class="sidebar-title">
    <i class="fa-solid fa-cash-register"></i>
    <span>Kasir</span>
  </h2>
  
  <div class="nav-menu">
    <a href="?page=ringkasan" class="nav-item <?= $page=='ringkasan'?'active':'' ?>">
      <i class="fa-solid fa-chart-line"></i>
      <span>Ringkasan</span>
    </a>
    
    <a href="?page=produk" class="nav-item <?= $page=='produk'?'active':'' ?>">
      <i class="fa-solid fa-box"></i>
      <span>Produk</span>
    </a>
    
    <a href="?page=transaksi" class="nav-item <?= $page=='transaksi'?'active':'' ?>">
      <i class="fa-solid fa-money-bill-wave"></i>
      <span>Transaksi</span>
    </a>
    
    <a href="?page=riwayat" class="nav-item <?= $page=='riwayat'?'active':'' ?>">
      <i class="fa-solid fa-receipt"></i>
      <span>Riwayat</span>
    </a>
    
    <a href="?page=grafik" class="nav-item <?= $page=='grafik'?'active':'' ?>">
      <i class="fa-solid fa-chart-bar"></i>
      <span>Grafik</span>
    </a>
    
    <a href="?page=menu_makanan" class="nav-item <?= $page=='menu_makanan'?'active':'' ?>">
      <i class="fa-solid fa-utensils"></i>
      <span>Menu</span>
    </a>
    
    <a href="logout.php" class="nav-item logout-btn">
      <i class="fa-solid fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>
  </div>
</div>

<div class="content">
  <?php
    switch ($page) {
      case 'produk':
        include 'produk.php'; break;
      case 'transaksi':
        include 'transaksi.php'; break;
      case 'riwayat':
        include 'riwayat.php'; break;
      case 'grafik':
        include 'grafik.php'; break;
      case 'user':
        include 'manajemen_user.php'; break;
      case 'profile':
        include 'profile.php'; break;
      case 'menu_makanan':
        include 'menu_makanan.php'; break;
      default:
        include 'dashboard_ringkasan.php'; break;
    }
  ?>
</div>

</body>
</html>