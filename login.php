<?php
// Informasi koneksi database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "lycollection_db"; // Ganti dengan nama database yang sesuai

// Membuat koneksi ke database
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Cek apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Sanitasi input
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Pertama, periksa apakah user ada di tabel admin
$sql_admin = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
$result_admin = $conn->query($sql_admin);

if ($result_admin->num_rows > 0) {
    // User ditemukan di tabel admin
    header("Location: ./admin/admin_dashboard.php"); // Halaman dashboard admin
    exit();
}

// Jika tidak ditemukan di tabel admin, periksa tabel users
$sql_user = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    // User ditemukan di tabel users
    header("Location: index.html"); // Halaman dashboard user
    exit();
}

// Jika username dan password tidak cocok
echo "Username atau password salah!";

// Tutup koneksi database
$conn->close();
?>
