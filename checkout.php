<?php
session_start();

// Redirect jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Redirect jika keranjang kosong
if (empty($_SESSION['keranjang'])) {
    header("Location: menu_makanan.php");
    exit;
}

include 'koneksi.php';

// Ambil data user
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE username='{$_SESSION['username']}'"));

// Ambil data produk dari keranjang
$menu = [];
$ids = implode(',', array_keys($_SESSION['keranjang']));
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id IN ($ids)");
while ($row = mysqli_fetch_assoc($query)) {
    $menu[$row['id']] = $row;
}

// Hitung total belanja
$total = 0;
foreach ($_SESSION['keranjang'] as $id => $qty) {
    $total += $menu[$id]['harga'] * $qty;
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['uang_bayar'])) {
        $uang_bayar = (int)$_POST['uang_bayar'];

        if ($uang_bayar >= $total) {
            // Simpan transaksi ke database
            foreach ($_SESSION['keranjang'] as $id => $qty) {
                $subtotal = $menu[$id]['harga'] * $qty;
                mysqli_query($conn, "INSERT INTO transaksi 
                    (tanggal, username, produk_id, jumlah, total, status) 
                    VALUES (NOW(), '{$_SESSION['username']}', $id, $qty, $subtotal, 'sold')");
            }

            // Data untuk struk
            $_SESSION['struk_data'] = [
                'items' => $_SESSION['keranjang'],
                'produk' => $menu,
                'total' => $total,
                'uang_bayar' => $uang_bayar,
                'kembalian' => $uang_bayar - $total,
                'tanggal' => date('d-m-Y H:i'),
                'kasir' => $_SESSION['username']
            ];

            unset($_SESSION['keranjang']);
            
            // Redirect ke halaman struk dengan opsi WhatsApp
            header("Location: struk_pdf.php?whatsapp=1");
            exit;
        } else {
            $error = "Uang bayar kurang Rp " . number_format($total - $uang_bayar, 0, ',', '.');
        }
    } else {
        $error = "Silakan masukkan jumlah uang bayar";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body {
            background-color: #f5f7fa;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .checkout-container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2b6777;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2b6777;
            color: #fff;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #2b6777;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            margin-bottom: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        button {
            background: linear-gradient(to right, #2b6777, #38b6ff);
            border: none;
            padding: 14px;
            width: 100%;
            font-size: 1rem;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .btn-whatsapp {
            background: linear-gradient(to right, #25D366, #128C7E);
        }

        .change-text {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .whatsapp-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 8px;
            display: none;
        }

        .whatsapp-section.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout Pembayaran</h1>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['keranjang'] as $id => $qty): ?>
                    <tr>
                        <td><?= htmlspecialchars($menu[$id]['nama_produk']) ?></td>
                        <td><?= $qty ?></td>
                        <td>Rp <?= number_format($menu[$id]['harga'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($menu[$id]['harga'] * $qty, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">Total: Rp <?= number_format($total, 0, ',', '.') ?></div>

        <form method="post" onsubmit="return validateBayar()">
            <label>Uang Bayar:</label>
            <input type="number" name="uang_bayar" id="uang_bayar" min="<?= $total ?>" required>
            <div class="change-text" id="change-text"></div>

            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <button type="submit">Proses Pembayaran</button>
            
            <!-- Checkbox untuk mengirim struk ke WhatsApp -->
            <div style="margin: 15px 0;">
                <input type="checkbox" id="send_wa" name="send_wa" checked>
                <label for="send_wa">Kirim struk ke WhatsApp setelah pembayaran</label>
            </div>
        </form>
    </div>

    <script>
        const inputBayar = document.getElementById('uang_bayar');
        const changeText = document.getElementById('change-text');
        const total = <?= $total ?>;

        function updateChange() {
            const bayar = parseInt(inputBayar.value) || 0;
            const kembalian = bayar - total;
            if (bayar >= total) {
                changeText.textContent = `Kembalian: Rp ${kembalian.toLocaleString('id-ID')}`;
                changeText.style.color = '#2b6777';
            } else {
                changeText.textContent = `Kurang: Rp ${(total - bayar).toLocaleString('id-ID')}`;
                changeText.style.color = 'red';
            }
        }

        function validateBayar() {
            const bayar = parseInt(inputBayar.value) || 0;
            if (bayar < total) {
                alert("Uang bayar tidak mencukupi.");
                return false;
            }
            return true;
        }

        inputBayar.addEventListener('input', updateChange);
        window.onload = () => {
            inputBayar.focus();
            updateChange();
        };
    </script>
</body>
</html>