<?php
// session_start(); // Hapus atau komentari baris ini
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

// Tambah user
if (isset($_POST['tambah'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  mysqli_query($conn, "INSERT INTO user (username, password) VALUES ('$username', '$password')");
  header("Location: manajemen_user.php");
  exit;
}

// Hapus user
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  mysqli_query($conn, "DELETE FROM user WHERE id = $id");
  header("Location: manajemen_user.php");
  exit;
}

// Ambil semua user
$user = mysqli_query($conn, "SELECT * FROM user ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen User</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      padding: 40px;
      color: #333;
    }

    h2 {
      margin-bottom: 20px;
    }

    form, table {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    input {
      padding: 10px;
      margin: 8px 0;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      padding: 10px 16px;
      background: #38b6ff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #e3f4ff;
    }

    a.btn-hapus {
      color: white;
      background: #e74c3c;
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
    }

    a.btn-hapus:hover {
      background: #c0392b;
    }
  </style>
</head>
<body>

<h2>👥 Manajemen User</h2>

<!-- Form tambah user -->
<form method="POST">
  <h3>➕ Tambah User</h3>
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit" name="tambah">Simpan</button>
</form>

<!-- Tabel daftar user -->
<table>
  <tr>
    <th>No</th>
    <th>Username</th>
    <th>Aksi</th>
  </tr>
  <?php $no = 1; while ($row = mysqli_fetch_assoc($user)): ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td>
      <a class="btn-hapus" href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>
