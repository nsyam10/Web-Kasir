<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Registrasi Kasir</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-color: #f5f7fa;
      --card-bg: #ffffff;
      --text-color: #222;
      --input-bg: #f1f1f1;
      --btn-bg: #38b6ff;
      --btn-hover: #319edb;
    }

    @media (prefers-color-scheme: dark) {
      :root {
        --bg-color: #1a1a1a;
        --card-bg: #2c2c2c;
        --text-color: #f0f0f0;
        --input-bg: #3a3a3a;
        --btn-bg: #38b6ff;
        --btn-hover: #319edb;
      }
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: var(--bg-color);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-container {
      background: var(--card-bg);
      padding: 2.5rem;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      width: 360px;
      text-align: center;
      color: var(--text-color);
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .register-container img {
      width: 80px;
      margin-bottom: 20px;
      border-radius: 50%;
    }

    .register-container h2 {
      margin-bottom: 1.5rem;
      font-size: 1.8rem;
      font-weight: 600;
    }

    .register-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      background: var(--input-bg);
      border: none;
      border-radius: 8px;
      color: var(--text-color);
    }

    .register-container input:focus {
      outline: 2px solid var(--btn-bg);
    }

    .register-container button {
      width: 100%;
      padding: 12px;
      background: var(--btn-bg);
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 10px;
    }

    .register-container button:hover {
      background: var(--btn-hover);
    }

    .footer-text {
      margin-top: 1rem;
      font-size: 0.85rem;
      color: #888;
    }

    .back-link {
      display: block;
      margin-top: 10px;
      font-size: 0.9rem;
      color: var(--btn-bg);
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <img src="logo.png" alt="Logo Toko">
    <h2>Registrasi User</h2>
    <form action="register_process.php" method="POST">
      <input type="text" name="username" placeholder="Buat Username" required />
      <input type="password" name="password" placeholder="Buat Password" required />
      <button type="submit">Daftar</button>
    </form>
    <a href="login.php" class="back-link">← Kembali ke Login</a>
    <p class="footer-text">© 2025 Aplikasi Kasir</p>
  </div>
</body>
</html>
