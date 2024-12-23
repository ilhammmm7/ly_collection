<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "lycollection_db";

// Membuat koneksi ke database
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form pendaftaran
$username = $_POST['username'];
$password = $_POST['password'];

// Sanitasi input
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Simpan password yang dienkripsi
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Tambahkan user ke database dengan role 'user' secara default
$sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')";

if ($conn->query($sql) === TRUE) {
    // Redirect ke halaman login dengan pesan sukses
    header("Location: login.html?register_success=true");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
