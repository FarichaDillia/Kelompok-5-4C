<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['pesanan_id'])) {
    $id = intval($_GET['pesanan_id']);
    $user_id = $_SESSION['user_id'];

    // Pastikan status pesanan adalah 'cancelled'
    $check = mysqli_query($conn, "SELECT * FROM riwayat_pesanan WHERE id = $id AND user_id = $user_id AND LOWER(status) = 'cancelled'");
    if (mysqli_num_rows($check) > 0) {
        if (mysqli_query($conn, "DELETE FROM riwayat_pesanan WHERE id = $id")) {
            header("Location: riwayat.php?alert=deleted");
            exit;
        } else {
            header("Location: riwayat.php?alert=delete_failed");
            exit;
        }
    } else {
        header("Location: riwayat.php?alert=not_cancelled");
        exit;
    }
} else {
    header("Location: riwayat.php?alert=notfound");
    exit;
}
