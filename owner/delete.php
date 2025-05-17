<?php
include '../config.php'; // perbaiki path ini sesuai lokasi koneksi.php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data dari tabel kategori
    $sql = "DELETE FROM categories WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    echo "ID kategori tidak ditemukan.";
}
?>