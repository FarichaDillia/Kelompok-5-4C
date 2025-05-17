<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Cek apakah ID item ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data item berdasarkan ID
    $query = "SELECT * FROM items WHERE id = $id";
    $result = mysqli_query($conn, $query);

    // Jika item tidak ditemukan
    if (mysqli_num_rows($result) == 0) {
        echo "<script>alert('Item tidak ditemukan.'); window.location.href='item.php';</script>";
        exit;
    }

    // Ambil data gambar dari item yang akan dihapus
    $item = mysqli_fetch_assoc($result);
    $gambar = $item['gambar'];

    // Hapus gambar dari folder jika ada
    if (file_exists("../img/" . $gambar) && $gambar != "") {
        unlink("../img/" . $gambar);
    }

    // Hapus item dari database
    $delete_query = "DELETE FROM items WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Item berhasil dihapus!'); window.location.href='item.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus item.'); window.location.href='item.php';</script>";
    }
} else {
    echo "<script>alert('ID item tidak ditemukan.'); window.location.href='item.php';</script>";
}
?>