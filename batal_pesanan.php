<?php
session_start();
include "config.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['pesanan_id'])) {
    $pesanan_id = $_GET['pesanan_id'];
    $query = "UPDATE riwayat_pesanan SET status = 'cancelled' WHERE id = $pesanan_id AND user_id = {$_SESSION['user_id']}";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pesanan berhasil dibatalkan!'); window.location='riwayat.php';</script>";
    } else {
        echo "<script>alert('Gagal membatalkan pesanan!'); window.location='riwayat.php';</script>";
    }
} else {
    echo "<script>alert('Pesanan tidak ditemukan!'); window.location='riwayat.php';</script>";
}
