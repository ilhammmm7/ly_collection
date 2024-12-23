<?php
session_start();
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "lycollection_db");

// Pastikan ada order_id dalam session
if (!isset($_SESSION['order_details']) || !isset($_SESSION['order_details']['order_id'])) {
    header("Location: co.php");
    exit;
}

$order_id = $_SESSION['order_details']['order_id'];

// Ambil data order dari database
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    header("Location: index.html");
    exit;
}

// Ambil item-item order dari database
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = $row;
}

// Array untuk informasi pembayaran
$payment_methods = [
    'bca' => ['name' => 'BCA', 'account' => '1234-5678-9012', 'holder' => 'LY COLLECTION'],
    'bri' => ['name' => 'BRI', 'account' => '9876-5432-1098', 'holder' => 'LY COLLECTION'],
    'dana' => ['name' => 'DANA', 'account' => '0812-3456-7890', 'holder' => 'LY COLLECTION']
];

// Setelah halaman ini selesai, hapus order_details dari session
unset($_SESSION['order_details']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - LyCollection</title>
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
            color: var(--secondary-color);
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .confirmation-header i {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }

        .confirmation-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title i {
            width: 24px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .detail-item {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .detail-label {
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            color: #666;
        }

        .payment-info {
            background-color: #fff3cd;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }

        .order-items {
            margin-top: 1rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border-color);
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        @media print {
            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="confirmation-header">
            <i class="fas fa-check-circle"></i>
            <h1>Pesanan Dikonfirmasi!</h1>
            <p>Terima kasih atas pembelian Anda. Pesanan Anda telah berhasil dibuat.</p>
        </div>

        <div class="section">
            <h2 class="section-title"><i class="fas fa-file-alt"></i> Informasi Pesanan</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">ID Pesanan</div>
                    <div class="detail-value"><?php echo $order['order_id']; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tanggal Pesanan</div>
                    <div class="detail-value"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title"><i class="fas fa-user"></i> Detail Pelanggan</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nama</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_address']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Provinsi</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['province']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Kota/Kabupaten</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_city']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Kode Pos</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_zip']); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title"><i class="fas fa-credit-card"></i> Informasi Pembayaran</h2>
            <div class="payment-info">
                <p>Metode Pembayaran: <?php echo strtoupper($order['payment_method']); ?></p>
                <?php if (isset($payment_methods[$order['payment_method']])): ?>
                    <p>Nama Rekening: <?php echo $payment_methods[$order['payment_method']]['holder']; ?></p>
                    <p>Nomor Rekening: <?php echo $payment_methods[$order['payment_method']]['account']; ?></p>
                    <p style="color: var(--primary-color); font-weight: bold;">
                        Silakan lakukan pembayaran ke rekening di atas untuk menyelesaikan pesanan Anda.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title"><i class="fas fa-shopping-cart"></i> Ringkasan Pesanan</h2>
            <div class="order-items">
                <?php foreach ($cart as $item): ?>
                    <div class="order-item">
                        <div><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)</div>
                        <div>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="total">
                <div>Total Pembayaran:</div>
                <div>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></div>
            </div>
        </div>

        <div class="actions">
            <a href="index.html" class="btn btn-secondary">Kembali ke Toko</a>
            <button onclick="window.print();" class="btn btn-primary">Cetak Pesanan</button>
        </div>
    </div>
</body>
</html>