<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: item.php");
    exit;
}

$id = intval($_GET['id']);
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE id=$id"));

if (!$item) {
    echo "<script>alert('Item tidak ditemukan'); window.location='item.php';</script>";
    exit;
}

// Tambahkan item ke keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];



}

$found = false;
foreach ($_SESSION['cart'] as &$cartItem) {
    if ($cartItem['id'] == $id) {
        $cartItem['qty'] += 1;
        $found = true;
        break;
    }
}
unset($cartItem);

if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $item['id'],
        'nama' => $item['nama'],
        'harga' => $item['harga'],
        'gambar' => $item['gambar'],
        'qty' => 1
    ];
}

// Tentukan asal redirect
$redirect = 'item.php';
if (isset($_GET['from']) && $_GET['from'] === 'index') {
    $redirect = 'index.php';
}

echo "<script>alert('Item berhasil ditambahkan ke keranjang!'); window.location='$redirect';</script>";
exit;
?>
