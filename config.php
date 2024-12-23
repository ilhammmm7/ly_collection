<?php include 'config.php';

$host = "localhost"; // Biasanya localhost
$user = "root";      // Username MySQL (default XAMPP/MAMP)
$pass = "";          // Password MySQL (kosong untuk default)
$dbname = "lycollection_db";  // Nama database

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
