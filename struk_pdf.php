<?php
session_start();

if (!isset($_SESSION['struk_data']) || !isset($_SESSION['username'])) {
    header("Location: menu_makanan.php");
    exit;
}

// Include koneksi database
include 'koneksi.php';

// Ambil data user dari database
$username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT nama FROM user WHERE username = '$username'");
$user = mysqli_fetch_assoc($query);

$struk = $_SESSION['struk_data'];
$send_wa = isset($_GET['whatsapp']) ? true : false;

// Gunakan nama user dari database sebagai nama kasir
$nama_kasir = $user['nama'] ?? $username; // Fallback ke username jika nama tidak ada

// Update nama kasir di data struk
$struk['kasir'] = $nama_kasir;

// Generate teks struk untuk WhatsApp
$whatsapp_text = "*STRUK PEMBAYARAN*\n";
$whatsapp_text .= "Tanggal: " . $struk['tanggal'] . "\n";
$whatsapp_text .= "Kasir: " . $nama_kasir . "\n\n";
$whatsapp_text .= "*DETAIL PEMBELIAN:*\n";

foreach ($struk['items'] as $id => $qty) {
    $produk = $struk['produk'][$id];
    $subtotal = $produk['harga'] * $qty;
    $whatsapp_text .= "- " . $produk['nama_produk'] . " (" . $qty . "x) Rp " . number_format($produk['harga'], 0, ',', '.') . " = Rp " . number_format($subtotal, 0, ',', '.') . "\n";
}

$whatsapp_text .= "\n*TOTAL: Rp " . number_format($struk['total'], 0, ',', '.') . "*\n";
$whatsapp_text .= "Uang Bayar: Rp " . number_format($struk['uang_bayar'], 0, ',', '.') . "\n";
$whatsapp_text .= "Kembalian: Rp " . number_format($struk['kembalian'], 0, ',', '.') . "\n\n";
$whatsapp_text .= "Terima kasih telah berbelanja!";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .struk-container {
            max-width: 300px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .item {
            margin-bottom: 5px;
        }
        .item-name {
            float: left;
        }
        .item-price {
            float: right;
        }
        .clear {
            clear: both;
        }
        .total-section {
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-weight: bold;
        }
        .whatsapp-section {
            margin-top: 20px;
            text-align: center;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        input[type="tel"] {
            padding: 10px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        button {
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            min-width: 120px;
        }
        .btn-print {
            background-color: #2b6777;
        }
        .btn-whatsapp {
            background-color: #25D366;
        }
        .btn-menu {
            background-color: #f44336;
        }
        .btn-back {
            background-color: #ff9800;
        }
        .no-print {
            margin-top: 20px;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                background-color: white;
                padding: 0;
            }
            .struk-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <div class="header">
            <h2>N&H Shop</h2>
            <p>Jl. Cinta ku padamu</p>
            <p>Tanggal: <?= $struk['tanggal'] ?></p>
            <p>Kasir: <?= htmlspecialchars($nama_kasir) ?></p>
        </div>

        <div class="items">
            <?php foreach ($struk['items'] as $id => $qty): ?>
                <?php $produk = $struk['produk'][$id]; ?>
                <div class="item">
                    <div class="item-name"><?= htmlspecialchars($produk['nama_produk']) ?> (<?= $qty ?>x)</div>
                    <div class="item-price">Rp <?= number_format($produk['harga'] * $qty, 0, ',', '.') ?></div>
                    <div class="clear"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="total-section">
            <div>Total: Rp <?= number_format($struk['total'], 0, ',', '.') ?></div>
            <div>Bayar: Rp <?= number_format($struk['uang_bayar'], 0, ',', '.') ?></div>
            <div>Kembali: Rp <?= number_format($struk['kembalian'], 0, ',', '.') ?></div>
        </div>

        <div class="footer" style="text-align: center; margin-top: 15px;">
            <p>Terima kasih telah berbelanja</p>
        </div>
    </div>

    <div class="no-print">
        <?php if ($send_wa): ?>
            <div class="whatsapp-section">
                <h3>Kirim Struk ke WhatsApp</h3>
                <form id="waForm">
                    <input type="tel" id="waNumber" placeholder="Nomor WhatsApp (628...)" required>
                    <button type="button" class="btn-whatsapp" onclick="sendToWhatsApp()">Kirim</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="button-group">
            <button class="btn-print" onclick="window.print()">Cetak Struk</button>
            <button class="btn-menu" onclick="location.href='http://localhost/kasir/dashboard.php?page=menu_makanan'">Kembali ke Menu</button>
            <?php if (!$send_wa): ?>
                <button class="btn-back" onclick="window.history.back()">Kembali</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($send_wa): ?>
        <script>
            function sendToWhatsApp() {
                const phone = document.getElementById('waNumber').value;
                if (!phone) {
                    alert('Silakan masukkan nomor WhatsApp');
                    return;
                }
                
                // Validasi format nomor
                if (!/^[0-9]{10,15}$/.test(phone)) {
                    alert('Format nomor WhatsApp tidak valid. Gunakan format 628xxxxxxx');
                    return;
                }
                
                const message = `<?= addslashes($whatsapp_text) ?>`;
                const encodedMessage = encodeURIComponent(message);
                window.open(`https://wa.me/${phone}?text=${encodedMessage}`, '_blank');
            }
            
            // Fokus ke input nomor setelah halaman load
            window.onload = function() {
                const waInput = document.getElementById('waNumber');
                if (waInput) waInput.focus();
            };
        </script>
    <?php endif; ?>
</body>
</html>