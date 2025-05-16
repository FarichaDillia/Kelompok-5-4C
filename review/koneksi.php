<?php
$host     = "localhost";  // atau 127.0.0.1
$user     = "root";       // ganti jika user database Anda berbeda
$password = "";           // sesuaikan jika ada password
$database = "review";  // ganti dengan nama database Anda

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
