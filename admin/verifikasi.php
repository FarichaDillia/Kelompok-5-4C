<?php
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['riwayat_id'])) {
    $riwayat_id = intval($_POST['riwayat_id']);

    $query1 = "UPDATE riwayat_pesanan SET status='verified' WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $riwayat_id);

    $query2 = "UPDATE pesanan SET status='verified' WHERE id=?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $riwayat_id);
    if ($stmt->execute()) {
        echo "<script>alert('Pesanan berhasil diverifikasi'); window.location='rent.php';</script>";
    } else {
        echo "<script>alert('Gagal verifikasi pesanan'); window.location='rent.php';</script>";
    }
}

?>
