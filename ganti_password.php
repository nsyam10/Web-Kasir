<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];
$pesan = '';

if (isset($_POST['submit'])) {
  $lama = $_POST['password_lama'];
  $baru = $_POST['password_baru'];

  // Ambil data user
  $query = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
  $user = mysqli_fetch_assoc($query);

  // Cek password lama
  if (password_verify($lama, $user['password'])) {
    $hashBaru = password_hash($baru, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE user SET password='$hashBaru' WHERE username='$username'");
    $pesan = "✅ Password berhasil diubah!";
  } else {
    $pesan = "❌ Password lama salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ganti Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f7f9fc;
      padding: 50px;
    }

    form {
      background: white;
      max-width: 400px;
      margin: auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 10px rgba(0,0,0,0.05);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input {
      padding: 12px;
      width: 100%;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #38b6ff;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
    }

    .msg {
      margin-top: 10px;
      text-align: center;
      font-weight: bold;
      color: #d63031;
    }
  </style>
</head>
<body>

<form method="POST">
  <h2>🔐 Ganti Password</h2>
  <input type="password" name="password_lama" placeholder="Password Lama" required>
  <input type="password" name="password_baru" placeholder="Password Baru" required>
  <button type="submit" name="submit">Ubah Password</button>
  <?php if ($pesan): ?>
    <div class="msg"><?= $pesan ?></div>
  <?php endif; ?>
</form>

</body>
</html>
