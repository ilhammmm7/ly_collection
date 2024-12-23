<?php
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "lycollection_db");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil kategori untuk filter produk
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Query untuk mengambil produk berdasarkan kategori (jika ada)
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";
if ($category_filter) {
    $sql .= " WHERE c.name = '" . $conn->real_escape_string($category_filter) . "'";
}
$result = $conn->query($sql);

// Memproses penambahan ke keranjang
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    // Ambil stok produk dari database
    $stock_query = $conn->query("SELECT stock FROM products WHERE id = " . intval($product_id));
    $stock_row = $stock_query->fetch_assoc();
    $available_stock = $stock_row['stock'];

    // Jika keranjang belum ada, buat array keranjang
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Tambahkan produk ke keranjang atau tambahkan kuantitas jika sudah ada
    if (isset($_SESSION['cart'][$product_id])) {
        if ($_SESSION['cart'][$product_id]['quantity'] < $available_stock) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            echo "<script>alert('Stok tidak cukup untuk menambah produk ini.');</script>";
        }
    } else {
        if ($available_stock > 0) {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => 1
            ];
        } else {
            echo "<script>alert('Stok tidak tersedia untuk produk ini.');</script>";
        }
    }

    header("Location: products.php");
    exit;
}

// Menghitung total item di keranjang
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - LyCollection</title>
    <link rel="stylesheet" href="./assets/css/style-prefix.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <img src="./assets/images/logo.png" alt="LyCollection Logo">
        </div>
        <ul class="nav-links">
            <li><a href="index.html" class="nav-link">Home</a></li>
            <li><a href="products.php" class="nav-link">Products</a></li>
            <li><a href="cart.php" class="nav-link">Cart (<?php echo $total_items; ?>)</a></li>
        </ul>
    </nav>
</header>

<div class="filter-category">
    <h2>Category</h2>
    <ul class="category-list">
        <li><a href="products.php" class="category-link">All</a></li>
        <li><a href="products.php?category=Hijab" class="category-link">Hijab</a></li>
        <li><a href="products.php?category=Jacket" class="category-link">Jacket</a></li>
        <li><a href="products.php?category=Baju_Wanita" class="category-link">Baju Wanita</a></li>
        <li><a href="products.php?category=Baju_pria" class="category-link">Baju Pria</a></li>
        <li><a href="products.php?category=Cosmetic" class="category-link">Cosmetic</a></li>
        <li><a href="products.php?category=Other" class="category-link">Other</a></li>
    </ul>
</div>

<div class="products">
    <h2>Our Products</h2>
    <div class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-item'>";
                echo "<a href='product_detail.php?id=" . $row['id'] . "'>";
                echo "<img src='./assets/images/products/" . basename($row['image']) . "' alt='" . $row['name'] . "' class='product-image'>";                echo "<div class='product-info'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p class='product-price'>Rp" . number_format($row['price'], 0, ',', '.') . "</p>";
                echo "<p class='product-stock'>Stock: " . $row['stock'] . "</p>"; // Menampilkan stok
                echo "</div>"; // product-info
                echo "</a>";
                
                // Tombol "Add to Cart" hanya aktif jika stok tersedia
                echo "<form action='products.php' method='POST' class='cart-form'>";
                echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                echo "<input type='hidden' name='product_name' value='" . $row['name'] . "'>";
                echo "<input type='hidden' name='product_price' value='" . $row['price'] . "'>";
                if ($row['stock'] > 0) {
                    echo "<button type='submit' class='add-to-cart-btn'>Add to Cart</button>";
                } else {
                    echo "<button type='button' class='add-to-cart-btn out-of-stock' disabled>Out of Stock</button>";
                }
                echo "</form>";
                echo "</div>"; // product-item
            }
        } else {
            echo "<p class='no-products'>No products available.</p>";
        }
        ?>
    </div>
</div>

<style>
/* Tambahan CSS untuk tampilan stok */
.product-stock {
    color: #666;
    font-size: 0.9em;
    margin: 5px 0;
}

.out-of-stock {
    background-color: #ccc;
    cursor: not-allowed;
}

.add-to-cart-btn {
    width: 100%;
    padding: 8px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.add-to-cart-btn:hover:not(.out-of-stock) {
    background-color: #45a049;
}

.add-to-cart-btn.out-of-stock {
    background-color: #cccccc;
}

.no-products {
    text-align: center;
    padding: 20px;
    font-size: 1.2em;
    color: #666;
}
</style>

</body>
</html>