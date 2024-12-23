-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Des 2024 pada 03.51
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lycollection_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(6) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `reg_date`, `full_name`, `email`) VALUES
(2, 'ilham', '12345', '2024-10-30 06:11:12', 'Ilham Hakiki', 'ilham212@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Hijab'),
(2, 'Baju_Wanita'),
(3, 'Other'),
(4, 'Jacket'),
(5, 'Baju_pria'),
(8, 'Sepatu'),
(9, 'FlatShoes'),
(10, 'Cosmetic');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `customer_zip` varchar(10) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_status` varchar(20) DEFAULT 'pending',
  `district` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_name`, `customer_email`, `customer_address`, `customer_city`, `province`, `customer_zip`, `payment_method`, `total_amount`, `order_date`, `order_status`, `district`) VALUES
(1, 'ORD-20241103-6400', 'ikham', 'ilhamhakiki505@gmail.com', 'jaja', 'jzjaj', 'jjwj', 'wjjw', 'dana', '100000.00', '2024-11-03 22:10:11', 'pending', ''),
(7, 'ORD-20241104-e7dd', 'ham', 'ilhamhakiki505@gmail.com', 'jdkwjk', 'Tuban', NULL, '1299', 'dana', '200000.00', '2024-11-04 21:45:32', 'pending', ''),
(8, 'ORD-20241104-203b', 'ham', 'ilhamhakiki505@gmail.com', 'ndmnka', 'Gresik', NULL, '9900', 'dana', '100000.00', '2024-11-04 21:47:05', 'pending', ''),
(16, 'ORD-20241106-343b', 'ham', 'ilhamhakiki505@gmail.com', 'mdnq', 'Tangerang', 'banten', '12121', 'dana', '110000.00', '2024-11-06 11:22:27', 'pending', ''),
(17, 'ORD-20241106-7f9f', 'ham', 'ilhamhakiki505@gmail.com', 'ajdk', 'Sukabumi', 'jawa_barat', 'bdjwh', 'bri', '110000.00', '2024-11-06 11:29:05', 'pending', ''),
(18, 'ORD-20241222-95fe', 'fufufafa', 'shjss@hshdhs.com', 'solo', 'Solo', 'jawa_tengah', '12121', 'dana', '255000.00', '2024-12-22 21:15:59', 'pending', ''),
(19, 'ORD-20241222-46f0', 'nia kurnialasair', 'shjss@hshdhs.com', 'padang', 'Klaten', 'jawa_tengah', '1212', 'bri', '1150000.00', '2024-12-22 21:17:35', 'pending', ''),
(20, 'ORD-20241222-5596', 'ham', 'ilhamhakiki505@gmail.com', 'sukabumi', 'Sukabumi', 'jawa_barat', '1212', 'bca', '1175000.00', '2024-12-22 22:31:33', 'pending', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`) VALUES
(1, 'ORD-20241103-6400', 24, 'pasmina', 1, '100000.00'),
(21, 'ORD-20241106-343b', 30, 'Pashmina Warna Hitam', 1, '55000.00'),
(22, 'ORD-20241106-343b', 31, 'Pashmina Warna Smoke Grey', 1, '55000.00'),
(23, 'ORD-20241106-7f9f', 37, 'Pashmina Warna Stone', 1, '55000.00'),
(24, 'ORD-20241106-7f9f', 34, 'Pashmina Warna Blush', 1, '55000.00'),
(25, 'ORD-20241222-95fe', 31, 'Pashmina Warna Smoke Grey', 1, '55000.00'),
(26, 'ORD-20241222-95fe', 45, 'Jacket Pria ', 1, '200000.00'),
(27, 'ORD-20241222-46f0', 30, 'Pashmina Warna Hitam', 4, '55000.00'),
(28, 'ORD-20241222-46f0', 52, 'Kemeja Pria', 3, '200000.00'),
(29, 'ORD-20241222-46f0', 36, 'Pashmina Warna Pewter', 6, '55000.00'),
(30, 'ORD-20241222-5596', 64, 'Kaos Oversize', 4, '100000.00'),
(31, 'ORD-20241222-5596', 34, 'Pashmina Warna Blush', 2, '55000.00'),
(32, 'ORD-20241222-5596', 31, 'Pashmina Warna Smoke Grey', 3, '55000.00'),
(33, 'ORD-20241222-5596', 42, 'Pengaharum Ruangan', 2, '20000.00'),
(34, 'ORD-20241222-5596', 63, 'Heals', 2, '120000.00'),
(35, 'ORD-20241222-5596', 76, 'lisptik wardah', 4, '55000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `description`, `category_id`, `stock`) VALUES
(30, 'Pashmina Warna Hitam', '55000.00', '../assets/images/products/1.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 5),
(31, 'Pashmina Warna Smoke Grey', '55000.00', '../assets/images/products/4.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 5),
(32, 'Pashmina Warna Broken White', '55000.00', '../assets/images/products/5.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 10),
(33, 'Pashmina Warna Ivory', '55000.00', '../assets/images/products/6.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 10),
(34, 'Pashmina Warna Blush', '55000.00', '../assets/images/products/7.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 7),
(36, 'Pashmina Warna Pewter', '55000.00', '../assets/images/products/9.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 4),
(37, 'Pashmina Warna Stone', '55000.00', '../assets/images/products/10.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 9),
(40, 'Pashmina Warna Stone', '55000.00', '../assets/images/products/14.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 10),
(41, 'Pashmina Warna Brown', '54998.00', '../assets/images/products/hero2.jpg', 'Pashmina Lembut\r\nnyaman dipakai\r\ntidak kusut', 1, 10),
(42, 'Pengaharum Ruangan', '20000.00', '../assets/images/products/WhatsApp Image 2024-10-20 at 13.30.34.jpeg', 'Harum ', 3, 18),
(43, 'Crownek Wanita', '80000.00', '../assets/images/products/clothes-1.jpg', 'Jaket Lembut\r\nnyaman dipakai\r\ntidak kusut', 4, 11),
(44, 'Atasan Wanita', '99999.00', '../assets/images/products/clothes-2.jpg', 'Bahan lembut', 2, 10),
(45, 'Jacket Pria ', '200000.00', '../assets/images/products/jacket-1.jpg', 'Bahan enak', 4, 29),
(46, 'Jacket Pria Kulit', '99999.00', '../assets/images/products/jacket-2.jpg', 'Bahan Enak', 4, 10),
(47, 'Jacket Pria Kulit', '500000.00', '../assets/images/products/jacket-3.jpg', 'Bahan enak', 4, 29),
(48, 'Jacket pria/wanita', '349999.00', '../assets/images/products/jacket-5.jpg', 'Bahan enak', 4, 2),
(49, 'Gantungan', '25000.00', '../assets/images/products/jewellery-1.jpg', 'accecoris', 3, 20),
(50, 'Ring Hijab', '15000.00', '../assets/images/products/jewellery-2.jpg', 'acc', 3, 19),
(51, 'Kemeja Pria', '200000.00', '../assets/images/products/shirt-1.jpg', 'Lembut', 5, 20),
(52, 'Kemeja Pria', '200000.00', '../assets/images/products/shirt-2.jpg', 'Lembut', 5, 17),
(53, 'Kaos Wanita', '20000.00', '../assets/images/products/shorts-1.jpg', 'Enak', 2, 30),
(54, 'Bucket', '400000.00', '../assets/images/products/WhatsApp Image 2024-10-20 at 13.29.47.jpeg', 'Bucket', 3, 10),
(55, 'FlatShoes', '65000.00', '../assets/images/products/13.-Otha-Shoes-Flat-Shoes-Wanita-300x300@2x.jpg', 'Nyaman dipakai untuk kemana aja', 9, 40),
(56, 'Sneakers Converse', '179999.99', '../assets/images/products/converse.jpg', 'Sapatu dengan Sol yang Empuk', 8, 50),
(57, 'FlatShoes ', '65000.00', '../assets/images/products/flatshoes.jpg', 'Cantik dan Elegan', 9, 33),
(58, 'Sneakers Nike', '250000.00', '../assets/images/products/model-jenis-desain-sepatu-sneakers-nike-sejarah-history-populer-favorit-terbaru_09.jpg', 'Fashion', 8, 40),
(59, 'Sneakers Cowok', '180000.00', '../assets/images/products/shoe-1.jpg', 'Enak untuk dipakai Saat Kantor', 8, 29),
(60, 'Sneaker Sport', '160000.00', '../assets/images/products/sports-1.jpg', 'Nyaman digunakan untuk lari', 8, 16),
(61, 'Sneakers Sport', '150000.00', '../assets/images/products/sports-2.jpg', 'Ayoo berlari dengan sepatu kerenmu', 8, 19),
(62, 'Sneaker Wanita', '155000.00', '../assets/images/products/th (3).jpg', 'Cantik dan nyaman dipakai dengan sol yang empuk', 8, 54),
(63, 'Heals', '120000.00', '../assets/images/products/th (4).jpg', 'Enak dipakai Saat Kondangan\r\n', 9, 16),
(64, 'Kaos Oversize', '100000.00', '../assets/images/products/th (1).jpg', 'Nyman untuk dipakai Cowok/Cewek', 2, 5),
(65, 'Atasan Wanita', '120000.00', '../assets/images/products/th (6).jpg', 'Baju Import', 2, 54),
(66, 'Atasan Wanita', '150000.00', '../assets/images/products/th (7).jpg', 'Baju Import', 2, 35),
(67, 'Atasan Wanita', '150000.00', '../assets/images/products/th (8).jpg', 'Atasan Wanita Korea', 2, 18),
(68, 'Atasan Wanita', '135000.00', '../assets/images/products/th (9).jpg', 'Bahan Lembut', 2, 14),
(69, 'Atasan Wanita', '180000.00', '../assets/images/products/th (10).jpg', 'Fashion Korea', 2, 29),
(70, 'Kaos Oversize', '85000.00', '../assets/images/products/th.jpg', 'Bahan Lembut dan Nyaman dipakai', 2, 10),
(71, 'Sunscreen Wardah', '50000.00', '../assets/images/products/sunscreen.jpg', 'Melindungi Wajah dari Sinar Matahari', 10, 44),
(72, 'Foundetion Liquid', '35000.00', '../assets/images/products/fondetion liquid.jpg', 'Ganti dari cusion', 10, 14),
(73, 'Bedak Wardah', '65000.00', '../assets/images/products/th (13).jpg', 'Ringan Dipakai', 10, 12),
(74, 'SkinTint Wardah', '35000.00', '../assets/images/products/th (14).jpg', 'Creame Ringan Dipakai', 10, 55),
(75, 'Cushion Wardah', '120000.00', '../assets/images/products/Wardah-Colorfit-Perfect-Glow-Cushion-SPF-33-PA.jpg', 'Mudah diaplikasikan,Ringan diwajah  dan tidak Lengket', 10, 14),
(76, 'lisptik wardah', '55000.00', '../assets/images/products/lisptik.jpg', 'Ringan diaplikasikan dan tidak Lengket', 10, 31),
(77, 'kemeja corcola pria', '65000.00', '../assets/images/products/kemeja corcola.png', 'Bahan adem enak dipakai untuk santai', 5, 75),
(78, 'Pashmina Warna Light', '55000.00', '../assets/images/products/3.jpg', 'Pashmina lembut\r\nHarga murmer\r\n', 1, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `reg_date`, `role`) VALUES
(3, 'ilham', 'hakiki', '2024-10-15 08:26:25', ''),
(12, 'indah', 'ganteng', '2024-10-30 09:01:15', 'user'),
(14, 'irvan', '12345', '2024-11-06 04:37:02', 'user'),
(15, 'uus', '12345', '2024-12-23 02:02:52', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
