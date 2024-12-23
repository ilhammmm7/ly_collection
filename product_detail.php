<?php
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "lycollection_db");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Memproses penambahan ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    // Jika keranjang belum ada, buat array keranjang
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Tambahkan produk ke keranjang
    if (isset($_SESSION['cart'][$product_id])) {
        // Jika produk sudah ada di keranjang, tambahkan kuantitas
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        // Jika produk belum ada di keranjang, tambahkan produk baru
        $_SESSION['cart'][$product_id] = [
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1
        ];
    }

    // Redirect ke halaman keranjang atau tetap di halaman ini
    header("Location: product_detail.php?id=" . $product_id);
    exit;
}

// Mendapatkan ID produk dan menampilkan detail produk
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found!";
        exit;
    }
} else {
    echo "Invalid product ID!";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Product Detail</title>
    <link rel="stylesheet" href="./assets/css/style-prefix.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <img src="./assets/images/logo.png" alt="LyCollection Logo">
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a></li>
        </ul>
    </nav>
</header>

<div class="product-detail-container">
    <div class="product-detail-image">
        <img src="<?php echo './assets/images/products/' . basename($product['image']); ?>" alt="<?php echo $product['name']; ?>" class="product-image-detail">    </div>
    <div class="product-detail-info">
        <h1><?php echo $product['name']; ?></h1>
        <p class="product-price-detail">Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></p>
        <p><?php echo $product['description']; ?></p>

        <form action="product_detail.php?id=<?php echo $product_id; ?>" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
        </form>

        <a href="products.php" class="back-to-products">Back to Products</a>
    </div>
</div>

</body>
</html>
