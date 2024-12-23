<?php
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "lycollection_db");

// Cek apakah keranjang kosong
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Fungsi untuk menghitung total harga
function calculateTotalPrice() {
    $total_price = 0;
    foreach ($_SESSION['cart'] as $product) {
        $total_price += $product['price'] * $product['quantity'];
    }
    return $total_price;
}

// Total harga keseluruhan
$total_price = calculateTotalPrice();

// Array untuk informasi pembayaran
$payment_methods = [
    'bca' => [
        'name' => 'BCA',
        'icon' => 'fas fa-bank',
        'account' => '1234-5678-9012',
        'holder' => 'LY COLLECTION'
    ],
    'bri' => [
        'name' => 'BRI',
        'icon' => 'fas fa-bank',
        'account' => '9876-5432-1098',
        'holder' => 'LY COLLECTION'
    ],
    'dana' => [
        'name' => 'DANA',
        'icon' => 'fas fa-wallet',
        'account' => '0812-3456-7890',
        'holder' => 'LY COLLECTION'
    ]
];

// Cek apakah formulir dikirimkan
// Dalam bagian validasi PHP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validasi input
    if (empty($_POST['fullname'])) $errors[] = "Nama Lengkap harus diisi";
    if (empty($_POST['email'])) $errors[] = "Email harus diisi";
    if (empty($_POST['address'])) $errors[] = "Alamat harus diisi";
    if (empty($_POST['province'])) $errors[] = "Provinsi harus dipilih";
    if (empty($_POST['city'])) $errors[] = "Kota/Kabupaten harus dipilih";
    if (empty($_POST['zip'])) $errors[] = "Kode Pos harus diisi";
    if (empty($_POST['payment'])) $errors[] = "Metode pembayaran harus dipilih";
    
    if (empty($errors)) {
        // Generate order ID
        $order_id = 'ORD-' . date('Ymd') . '-' . substr(uniqid(), -4);

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert order into database
            $stmt = $conn->prepare("INSERT INTO orders (order_id, customer_name, customer_email, customer_address, customer_city, province, customer_zip, payment_method, total_amount, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssssssd", $order_id, $_POST['fullname'], $_POST['email'], $_POST['address'], $_POST['city'], $_POST['province'], $_POST['zip'], $_POST['payment'], $total_price);
            $stmt->execute();

            // Insert order items and update stock
            foreach ($_SESSION['cart'] as $product_id => $product) {
                $quantity = $product['quantity'];
                $price = $product['price'];
                
                // Insert order item
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sisid", $order_id, $product_id, $product['name'], $quantity, $price);
                $stmt->execute();
                
                // Update stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $stmt->bind_param("iii", $quantity, $product_id, $quantity);
                $stmt->execute();
                
                if ($stmt->affected_rows == 0) {
                    throw new Exception("Insufficient stock for product " . $product['name']);
                }
            }

            // Commit transaction
            $conn->commit();

            // Store order details in session
            $_SESSION['order_details'] = [
                'order_id' => $order_id,
                'customer' => [
                    'name' => $_POST['fullname'],
                    'email' => $_POST['email'],
                    'address' => $_POST['address'],
                    'city' => $_POST['city'],
                    'province' => $_POST['province'],
                    'zip' => $_POST['zip']
                ],
                'payment' => $_POST['payment'],
                'total' => $total_price,
                'order_date' => date('Y-m-d H:i:s'),
                'cart_items' => $_SESSION['cart']
            ];

            // Clear cart
            unset($_SESSION['cart']);

            // Redirect to order confirmation page
            header("Location: order-confirmation.php");
            exit;

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $errors[] = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - LyCollection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff4e5c;
            --secondary-color: #2c3e50;
            --background-color: #f8f9fa;
            --border-color: #e0e0e0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: var(--background-color);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .checkout-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .payment-methods {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background-color: #fff5f5;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: #fff5f5;
        }

        .payment-method input[type="radio"] {
            margin-right: 1rem;
        }

        .payment-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .payment-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .order-summary {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 2rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-total {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border-color);
        }

        .checkout-btn {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 2rem;
        }

        .checkout-btn:hover {
            background-color: #e44452;
        }

        .account-details {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            display: none;
        }

        .account-details.active {
            display: block;
        }

        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="checkout-container">
            <div class="checkout-section">
                <h2 class="section-title"><i class="fas fa-user-circle"></i> Checkout</h2>
                <form id="checkout-form" method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </label>
                        <input type="text" class="form-control" name="fullname" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Alamat Lengkap
                        </label>
                        <textarea class="form-control" name="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-signs"></i> Provinsi
                        </label>
                        <select id="province" name="province" class="form-control" required>
                            <option value="">Pilih Provinsi</option>
                            <option value="jawa_barat">Jawa Barat</option>
                            <option value="jawa_tengah">Jawa Tengah</option>
                            <option value="jawa_timur">Jawa Timur</option>
                            <option value="dki_jakarta">DKI Jakarta</option>
                            <option value="banten">Banten</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-city"></i> Kota / Kabupaten
                        </label>
                        <select id="city" name="city" class="form-control" required>
                            <option value="">Pilih Kota / Kabupaten</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-pin"></i> Kode Pos
                        </label>
                        <input type="text" class="form-control" name="zip" required>
                    </div>
                </form>
            </div>

            <div class="checkout-section">
                <h2 class="section-title"><i class="fas fa-credit-card"></i> Pembayaran</h2>
                <div class="payment-methods">
                    <?php foreach ($payment_methods as $method => $details): ?>
                        <div class="payment-method">
                            <input type="radio" id="<?php echo $method; ?>" name="payment" value="<?php echo $method; ?>" form="checkout-form" required>
                            <label for="<?php echo $method; ?>">
                                <i class="<?php echo $details['icon']; ?> payment-icon"></i>
                                <?php echo $details['name']; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="account-details">
                    <h3>Account Details</h3>
                    <p id="account-number"></p>
                    <p id="account-holder"></p>
                </div>

                <div class="order-summary">
                    <h3>Rincian Pesanan</h3>
                    <?php foreach ($_SESSION['cart'] as $product_id => $product): ?>
                        <div class="summary-item">
                            <span><?php echo $product['name']; ?> (x<?php echo $product['quantity']; ?>)</span>
                            <span>Rp<?php echo number_format($product['price'] * $product['quantity'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>Rp<?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <button type="submit" class="checkout-btn" form="checkout-form">Buat Pesanan</button>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

<script>
    // Data provinsi dan kota
    const provinceCityMap = {
        'jawa_barat': [
            'Bandung', 'Bekasi', 'Bogor', 'Depok', 'Cirebon', 'Tasikmalaya',
            'Garut', 'Sukabumi', 'Ciamis', 'Kuningan', 'Majalengka', 'Sumedang',
            'Indramayu', 'Subang', 'Purwakarta', 'Karawang', 'Cianjur'
        ],
        'jawa_tengah': [
            'Semarang', 'Solo', 'Magelang', 'Pekalongan', 'Tegal', 'Purwokerto',
            'Cilacap', 'Kudus', 'Salatiga', 'Surakarta', 'Kendal', 'Brebes',
            'Pemalang', 'Purworejo', 'Klaten', 'Boyolali'
        ],
        'jawa_timur': [
            'Surabaya', 'Malang', 'Sidoarjo', 'Banyuwangi', 'Kediri', 'Jember',
            'Probolinggo', 'Pasuruan', 'Mojokerto', 'Madiun', 'Blitar', 'Gresik',
            'Lumajang', 'Tuban', 'Lamongan'
        ],
        'dki_jakarta': [
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan',
            'Jakarta Timur', 'Kepulauan Seribu'
        ],
        'banten': [
            'Serang', 'Tangerang', 'Cilegon', 'Tangerang Selatan', 'Pandeglang',
            'Lebak', 'Rangkasbitung'
        ]
    };

    // Fungsi untuk mengupdate pilihan kota
    function updateCities(selectedProvince) {
        const citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">Pilih Kota / Kabupaten</option>';
        
        if (selectedProvince && provinceCityMap[selectedProvince]) {
            provinceCityMap[selectedProvince].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    }

    // Event listener untuk perubahan provinsi
    document.getElementById('province').addEventListener('change', function() {
        updateCities(this.value);
    });

    // Payment method handling
    const paymentMethods = document.querySelectorAll('.payment-method');
    const accountDetails = document.querySelector('.account-details');

    paymentMethods.forEach((method) => {
        method.addEventListener('click', () => {
            const selectedMethod = method.querySelector('input[type="radio"]').value;
            const accountNumber = document.getElementById('account-number');
            const accountHolder = document.getElementById('account-holder');

            accountDetails.classList.add('active');

            switch (selectedMethod) {
                case 'bca':
                    accountNumber.textContent = 'Nomor Rekening: <?php echo $payment_methods['bca']['account']; ?>';
                    accountHolder.textContent = 'Atas Nama: <?php echo $payment_methods['bca']['holder']; ?>';
                    break;
                case 'bri':
                    accountNumber.textContent = 'Nomor Rekening: <?php echo $payment_methods['bri']['account']; ?>';
                    accountHolder.textContent = 'Atas Nama: <?php echo $payment_methods['bri']['holder']; ?>';
                    break;
                case 'dana':
                    accountNumber.textContent = 'Nomor DANA: <?php echo $payment_methods['dana']['account']; ?>';
                    accountHolder.textContent = 'Atas Nama: <?php echo $payment_methods['dana']['holder']; ?>';
                    break;
            }
        });
    });
</script>
</body>
</html>