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

    // Hapus hanya jika pesanan milik user & sudah canceled
    $check = mysqli_query($conn, "SELECT * FROM riwayat_pesanan WHERE id = $id AND user_id = $user_id AND status = 'cancelled'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM riwayat_pesanan WHERE id = $id");
        echo "<script>alert('Pesanan berhasil dihapus.'); window.location='riwayat.php';</script>";
    } else {
        echo "<script>alert('Pesanan tidak ditemukan atau belum dibatalkan.'); window.location='riwayat.php';</script>";
    }
} else {
    header("Location: riwayat.php");
    exit;
}
?>
