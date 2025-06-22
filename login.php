<?php
// login.php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Kasir</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary: #38b6ff;
      --primary-dark: #2b6777;
      --danger: #e74c3c;
      --success: #27ae60;
      --text: #333;
      --text-light: #777;
      --bg: #f5f7fa;
      --card: #ffffff;
      --input: #f1f1f1;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    @media (prefers-color-scheme: dark) {
      :root {
        --text: #f0f0f0;
        --text-light: #aaa;
        --bg: #1a1a1a;
        --card: #2c2c2c;
        --input: #3a3a3a;
      }
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: var(--bg);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: var(--text);
    }

    .auth-card {
      background: var(--card);
      width: 100%;
      max-width: 380px;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: var(--shadow);
      text-align: center;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 1.5rem;
      border-radius: 50%;
      border: 3px solid var(--primary);
      padding: 5px;
    }

    h1 {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 1rem;
      text-align: left;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: var(--text-light);
    }

    input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: none;
      border-radius: 8px;
      background: var(--input);
      color: var(--text);
      font-size: 0.95rem;
      transition: all 0.2s;
    }

    input:focus {
      outline: 2px solid var(--primary);
      background: var(--card);
    }

    .btn {
      width: 100%;
      padding: 0.75rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 0.5rem;
    }

    .btn:hover {
      background: var(--primary-dark);
    }

    .btn-block {
      display: block;
      width: 100%;
    }

    .alert {
      padding: 0.75rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }

    .alert-danger {
      background: rgba(231, 76, 60, 0.1);
      color: var(--danger);
    }

    .alert-success {
      background: rgba(39, 174, 96, 0.1);
      color: var(--success);
    }

    .auth-links {
      margin-top: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .auth-links a {
      color: var(--primary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.2s;
    }

    .auth-links a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .footer {
      margin-top: 1.5rem;
      font-size: 0.8rem;
      color: var(--text-light);
    }
  </style>
</head>
<body>
  <div class="auth-card">
    <img src="img/logo.png" alt="Logo" class="logo">
    <h1>Login Kasir</h1>
    
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Masukkan username" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
      </div>
      
      <button type="submit" class="btn">Login</button>
    </form>

    <div class="auth-links">
      <a href="register.php">Belum punya akun? Daftar</a>
      <a href="forgot_password.php">Lupa password?</a>
    </div>

    <p class="footer">© 2025 Aplikasi Kasir</p>
  </div>
</body>
</html>