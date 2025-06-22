<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Ambil data produk dari database
$menu = [];
$q = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($q)) {
    $menu[] = [
        'id' => $row['id'],
        'nama' => $row['nama_produk'],
        'kategori' => $row['kategori'] ?? 'Makanan/Minuman',
        'harga' => $row['harga'],
        'gambar' => !empty($row['gambar']) ? $row['gambar'] : 'img/default_menu.png',
        'deskripsi' => $row['deskripsi'] ?? '' // Tambahkan deskripsi jika ada
    ];
}

// Proses keranjang
if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];

if (isset($_POST['tambah_keranjang'])) {
    $id = (int)$_POST['id'];
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
    
    if (!isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id] = $qty;
    } else {
        $_SESSION['keranjang'][$id] += $qty;
    }
    echo "<script>window.location='?page=menu_makanan';</script>";
    exit;
}

if (isset($_POST['hapus_keranjang'])) {
    $id = (int)$_POST['id'];
    unset($_SESSION['keranjang'][$id]);
    echo "<script>window.location='?page=menu_makanan';</script>";
    exit;
}

if (isset($_POST['update_keranjang'])) {
    $id = (int)$_POST['id'];
    $qty = max(0, (int)$_POST['qty']);
    
    if ($qty > 0) {
        $_SESSION['keranjang'][$id] = $qty;
    } else {
        unset($_SESSION['keranjang'][$id]);
    }
    echo "<script>window.location='?page=menu_makanan';</script>";
    exit;
}
?>

<style>
/* Main Container */
.menu-container {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

/* Cart Section */
.cart-section {
    flex: 0 0 350px;
}

.keranjang-box {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(43, 103, 119, 0.1);
    padding: 20px;
    margin-bottom: 25px;
    position: sticky;
    top: 20px;
}

.keranjang-box h3 {
    margin-top: 0;
    color: #2b6777;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.keranjang-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.keranjang-table th, .keranjang-table td {
    padding: 10px 8px;
    text-align: left;
    border-bottom: 1px solid #e3f4ff;
}

.keranjang-table th {
    background: #f8fcff;
    font-weight: 600;
}

.keranjang-table td.hapus, .keranjang-table td.qty {
    text-align: center;
}

.qty-input {
    width: 50px;
    text-align: center;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-hapus {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-hapus:hover {
    background: #c0392b;
}

.btn-checkout {
    background: #2b6777;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 0;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    width: 100%;
    display: block;
    text-align: center;
}

.btn-checkout:hover {
    background: #38b6ff;
}

.empty-cart {
    text-align: center;
    padding: 20px;
    color: #777;
}

.empty-cart i {
    font-size: 40px;
    margin-bottom: 10px;
    color: #ddd;
}

/* Menu Section */
.menu-section {
    flex: 1;
}

.menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.search-box {
    display: flex;
    align-items: center;
    background: white;
    border-radius: 8px;
    padding: 8px 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.search-box input {
    border: none;
    outline: none;
    padding: 5px;
    width: 250px;
}

.search-box i {
    color: #777;
    margin-right: 10px;
}

.category-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.category-btn {
    background: white;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 8px 15px;
    cursor: pointer;
    transition: all 0.2s;
}

.category-btn.active, .category-btn:hover {
    background: #2b6777;
    color: white;
    border-color: #2b6777;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

.menu-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(56, 182, 255, 0.2);
}

.menu-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.menu-content {
    padding: 15px;
}

.menu-nama {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 5px;
    color: #333;
}

.menu-kategori {
    color: #38b6ff;
    font-size: 0.9rem;
    margin-bottom: 8px;
    display: inline-block;
    background: #f0f9ff;
    padding: 3px 10px;
    border-radius: 20px;
}

.menu-harga {
    color: #2b6777;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.menu-desc {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.menu-actions {
    display: flex;
    gap: 10px;
}

.qty-selector {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
}

.qty-selector button {
    background: #f5f5f5;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    font-weight: bold;
}

.qty-selector input {
    width: 40px;
    text-align: center;
    border: none;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    padding: 5px;
}

.btn-add {
    background: #38b6ff;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    flex: 1;
}

.btn-add:hover {
    background: #2b6777;
}

/* Responsive */
@media (max-width: 992px) {
    .menu-container {
        flex-direction: column;
    }
    
    .cart-section {
        flex: 0 0 auto;
    }
    
    .keranjang-box {
        position: static;
    }
}

@media (max-width: 576px) {
    .menu-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .menu-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .search-box {
        width: 100%;
    }
}
</style>

<div class="menu-container">
    <!-- Cart Section -->
    <div class="cart-section">
        <div class="keranjang-box">
            <h3><i class="fas fa-shopping-cart"></i> Keranjang</h3>
            <table class="keranjang-table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th class="qty">Qty</th>
                        <th>Subtotal</th>
                        <th class="hapus"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $cart_empty = empty($_SESSION['keranjang']);
                    
                    foreach ($_SESSION['keranjang'] as $id => $qty):
                        foreach ($menu as $m) {
                            if ($m['id'] == $id) {
                                $subtotal = $m['harga'] * $qty;
                                $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($m['nama']) ?></td>
                                    <td class="qty">
                                        <form method="post" style="display:flex;gap:5px;">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="number" name="qty" value="<?= $qty ?>" min="1" class="qty-input">
                                            <button type="submit" name="update_keranjang" style="display:none;"></button>
                                        </form>
                                    </td>
                                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                    <td class="hapus">
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <button type="submit" name="hapus_keranjang" class="btn-hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    endforeach;
                    
                    if ($cart_empty): ?>
                        <tr>
                            <td colspan="4" class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <div>Keranjang kosong</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th colspan="2">Rp <?= number_format($total, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
            
           <?php if (!$cart_empty): ?>
            <form action="checkout.php" method="post">
              <input type="hidden" name="total" value="<?= $total ?>">
             <button type="submit" class="btn-checkout">
              <i class="fas fa-credit-card"></i> Checkout (Rp <?= number_format($total, 0, ',', '.') ?>)
             </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Menu Section -->
    <div class="menu-section">
        <div class="menu-header">
            <h2>Menu Makanan & Minuman</h2>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari menu..." id="search-input">
            </div>
        </div>
        
        <div class="category-filter">
            <button class="category-btn active" data-category="all">Semua</button>
            <button class="category-btn" data-category="Makanan">Makanan</button>
            <button class="category-btn" data-category="Minuman">Minuman</button>
            <button class="category-btn" data-category="Snack">Snack</button>
        </div>
        
        <div class="menu-grid" id="menu-grid">
            <?php foreach ($menu as $m): ?>
                <div class="menu-card" data-category="<?= htmlspecialchars($m['kategori']) ?>">
                    <img src="<?= $m['gambar'] ?>" alt="<?= htmlspecialchars($m['nama']) ?>" class="menu-img">
                    <div class="menu-content">
                        <div class="menu-nama"><?= htmlspecialchars($m['nama']) ?></div>
                        <div class="menu-kategori"><?= htmlspecialchars($m['kategori']) ?></div>
                        <div class="menu-harga">Rp <?= number_format($m['harga'], 0, ',', '.') ?></div>
                        <div class="menu-desc" title="<?= htmlspecialchars($m['deskripsi']) ?>">
                            <?= htmlspecialchars($m['deskripsi']) ?>
                        </div>
                        <form method="post" class="menu-actions">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <div class="qty-selector">
                                <button type="button" class="qty-minus">-</button>
                                <input type="number" name="qty" value="1" min="1" class="qty-value">
                                <button type="button" class="qty-plus">+</button>
                            </div>
                            <button type="submit" name="tambah_keranjang" class="btn-add">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Quantity Selector
document.querySelectorAll('.qty-minus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.nextElementSibling;
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    });
});

document.querySelectorAll('.qty-plus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        input.value = parseInt(input.value) + 1;
    });
});

// Auto submit when quantity changes in cart
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        this.closest('form').submit();
    });
});

// Category Filter
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const category = this.dataset.category;
        const menuItems = document.querySelectorAll('.menu-card');
        
        menuItems.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Search Functionality
document.getElementById('search-input').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const menuItems = document.querySelectorAll('.menu-card');
    
    menuItems.forEach(item => {
        const menuName = item.querySelector('.menu-nama').textContent.toLowerCase();
        if (menuName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>