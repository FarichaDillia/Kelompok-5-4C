<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: item.php?alert=notfound");
    exit;
}

$id = intval($_GET['id']);
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE id=$id"));

if (!$item) {
    header("Location: item.php?alert=notfound");
    exit;
}

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

$redirect = 'item.php?success=added';
if (isset($_GET['from'])) {
    if ($_GET['from'] === 'navbar') {
        $redirect = 'navbar.php?success=added';
    } elseif ($_GET['from'] === 'detail') {
        $redirect = 'item-detail.php?id=' . $id . '&success=added';
    }
}

header("Location: $redirect");
exit;
?>
