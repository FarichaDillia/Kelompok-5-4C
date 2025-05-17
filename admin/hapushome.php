<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Cek apakah ID pesanan diberikan
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus pesanan dari database
    $query = "DELETE FROM pesanan WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pesanan berhasil dihapus!'); window.location.href='rent.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pesanan.'); window.location.href='rent.php';</script>";
    }
} else {
    echo "<script>alert('ID pesanan tidak ditemukan.'); window.location.href='rent.php';</script>";
}
?>