<?php
session_start();
include "config.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['pesanan_id'])) {
    $pesanan_id = intval($_GET['pesanan_id']);
    $user_id = $_SESSION['user_id'];

    // Update status pesanan menjadi 'cancelled'
    $query = "UPDATE riwayat_pesanan SET status = 'cancelled' WHERE id = $pesanan_id AND user_id = $user_id";
    if (mysqli_query($conn, $query)) {
        header("Location: riwayat.php?alert=cancelled");
        exit;
    } else {
        header("Location: riwayat.php?alert=cancel_failed");
        exit;
    }
} else {
    header("Location: riwayat.php?alert=notfound");
    exit;
}
