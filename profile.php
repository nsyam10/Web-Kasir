<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE username='$username'"));

$profile_dir = 'img/profile/';
$default_img = 'img/logo.png';
$profile_img = $default_img;
foreach (['png', 'jpg', 'jpeg'] as $ext) {
  $path = $profile_dir . $username . '.' . $ext;
  if (file_exists($path)) {
    $profile_img = $path;
    break;
  }
}

$success = '';
$error = '';

if (isset($_GET['success'])) {
  $success = "Profil berhasil diperbarui!";
}

if (isset($_POST['update'])) {
  $new_nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $new_bio = mysqli_real_escape_string($conn, $_POST['bio']);
  mysqli_query($conn, "UPDATE user SET nama='$new_nama', bio='$new_bio' WHERE username='$username'");
  header("Location: dashboard.php?page=profile&success=1");
  exit;
}

if (isset($_POST['upload'])) {
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];
    if (in_array($ext, $allowed)) {
      if (!is_dir($profile_dir)) mkdir($profile_dir, 0777, true);
      foreach (['png', 'jpg', 'jpeg'] as $e) {
        $old = $profile_dir . $username . '.' . $e;
        if (file_exists($old)) unlink($old);
      }
      $target = $profile_dir . $username . '.' . $ext;
      if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        $success = "Foto profil berhasil diubah!";
        $profile_img = $target;
      } else {
        $error = "Gagal mengupload gambar!";
      }
    } else {
      $error = "Format gambar harus JPG atau PNG!";
    }
  } else {
    $error = "Pilih file gambar terlebih dahulu!";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Profil Saya</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', Arial, sans-serif;
      background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
      margin: 0;
      padding: 0;
    }
    .profile-container {
      max-width: 420px;
      margin: 40px auto 0 auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 18px #38b6ff22;
      padding: 36px 32px 28px 32px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .profile-img-preview {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      border: 3px solid #38b6ff;
      background: #fff;
      box-shadow: 0 2px 12px #38b6ff33;
      margin-bottom: 12px;
      object-fit: cover;
      transition: box-shadow 0.2s;
    }
    .profile-container h2 {
      margin: 0 0 12px 0;
      color: #2b6777;
      font-weight: 600;
      font-size: 1.3rem;
      letter-spacing: 0.5px;
    }
    .profile-form, .profile-img-form {
      width: 100%;
      margin-bottom: 18px;
      text-align: center;
    }
    .profile-form table {
      width: 100%;
      margin-bottom: 18px;
    }
    .profile-form td {
      padding: 6px 0;
      vertical-align: top;
    }
    .profile-form textarea {
      width: 100%;
      padding: 8px 10px;
      border-radius: 7px;
      border: 1px solid #ddd;
      resize: vertical;
      font-family: inherit;
      font-size: 1rem;
    }
    .profile-form button, .profile-img-form button {
      padding: 10px 22px;
      background: #38b6ff;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.2s;
      border: none;
      cursor: pointer;
      margin-top: 6px;
      margin-bottom: 6px;
      font-size: 1rem;
    }
    .profile-form button:hover, .profile-img-form button:hover {
      background: #2b6777;
    }
    .profile-img-form input[type="file"] {
      margin-bottom: 8px;
      border: none;
    }
    .notif-success {
      color: #27ae60;
      margin-bottom: 10px;
      font-weight: 600;
    }
    .notif-error {
      color: #e74c3c;
      margin-bottom: 10px;
      font-weight: 600;
    }
    .profile-form input, .profile-form textarea {
      width: 100%;
      padding: 8px 10px;
      border-radius: 7px;
      border: 1px solid #ddd;
      font-family: inherit;
      font-size: 1rem;
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <h2>Profil Saya</h2>
    <form class="profile-img-form" method="post" enctype="multipart/form-data">
      <img src="<?= $profile_img ?>?t=<?= time() ?>" alt="Profile" class="profile-img-preview">
      <div>
        <input type="file" name="foto" accept="image/*">
      </div>
      <button type="submit" name="upload">Ganti Foto</button>
    </form>
    <?php if ($success): ?>
      <div class="notif-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="notif-error"><?= $error ?></div>
    <?php endif; ?>
    <form class="profile-form" method="post">
      <table>
        <tr>
          <td style="color:#555;font-weight:600;">Nama</td>
          <td>
            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>">
          </td>
        </tr>
        <tr>
          <td style="color:#555;font-weight:600;">Bio</td>
          <td>
            <textarea name="bio" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
          </td>
        </tr>
      </table>
      <button type="submit" name="update">Simpan Perubahan</button>
    </form>
  </div>
</body>
</html>
