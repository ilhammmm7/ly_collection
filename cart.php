<?php
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "lycollection_db");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Memproses penghapusan produk dari keranjang
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit;
}

// Memproses update kuantitas melalui AJAX
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];

    // Ambil stok produk dari database
    $stock_query = $conn->query("SELECT stock FROM products WHERE id = " . intval($product_id));
    $stock_row = $stock_query->fetch_assoc();
    $available_stock = $stock_row['stock'];

    if ($new_quantity > 0 && $new_quantity <= $available_stock) {
        $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Stok tidak mencukupi untuk kuantitas yang diminta.'
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'total_price' => calculateTotalPrice()
    ]);
    exit;
}

// Fungsi menghitung total harga
function calculateTotalPrice() {
    $total_price = 0;
    foreach ($_SESSION['cart'] as $product) {
        $total_price += $product['price'] * $product['quantity'];
    }
    return $total_price;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - LyCollection</title>
    <link rel="stylesheet" href="./assets/css/style-prefix.css">
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <img src="./assets/images/logo.png" alt="LyCollection Logo">
        </div>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a></li>
        </ul>
    </nav>
</header>

<div class="cart-container">
    <h2>Your Cart</h2>
    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $product_id => $product): 
                    $product_total = $product['price'] * $product['quantity'];
                ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td>Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                    <td>
                        <form class="cart-quantity-form" onsubmit="return false;">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" min="1" onchange="updateQuantity(this, '<?php echo $product_id; ?>')">
                        </form>
                    </td>
                    <td>Rp<span id="total-<?php echo $product_id; ?>"><?php echo number_format($product_total, 0, ',', '.'); ?></span></td>
                    <td><a href="cart.php?remove=<?php echo $product_id; ?>" class="cart-remove-btn">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-total">
            <h3>Total Price: Rp<span id="cart-total"><?php echo number_format(calculateTotalPrice(), 0, ',', '.'); ?></span></h3>
            <a href="co.php" class="checkout-btn">Checkout</a>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

<div id="error-message" style="color: red; text-align: center; margin-top: 20px;"></div>

<script>
// AJAX untuk update kuantitas dan total harga tanpa refresh
function updateQuantity(input, productId) {
    const quantity = input.value;
    const formData = new FormData();
    formData.append('update_quantity', true);
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update total per produk
            const productRow = input.closest('tr');
            const price = parseInt(productRow.querySelector('td:nth-child(2)').innerText.replace('Rp', '').replace(/\./g, ''));
            const newTotal = price * quantity;
            document.getElementById(`total-${productId}`).innerText = newTotal.toLocaleString('id-ID');

            // Update total keranjang
            document.getElementById('cart-total').innerText = data.total_price.toLocaleString('id-ID');

            // Clear any previous error messages
            document.getElementById('error-message').innerText = '';
        } else {
            // Display error message
            document.getElementById('error-message').innerText = data.message;
            // Reset input to previous value if there's an error
            input.value = <?php echo json_encode($_SESSION['cart'][$product_id]['quantity']); ?>;
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>