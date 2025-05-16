<?php
session_start();

if (isset($_GET['id']) && isset($_SESSION['cart'])) {
    $id = $_GET['id'];
    $hapusSemua = isset($_GET['all']) && $_GET['all'] === 'true';

    foreach ($_SESSION['cart'] as $index => &$item) {
        if ($item['id'] == $id) {
            if ($hapusSemua) {
                // Hapus langsung item seluruhnya
                unset($_SESSION['cart'][$index]);
            } else {
                // Kurangi qty
                if ($item['qty'] > 1) {
                    $item['qty']--;
                } else {
                    unset($_SESSION['cart'][$index]);
                }
            }
            break;
        }
    }
    unset($item); // lepas reference

    $_SESSION['cart'] = array_values($_SESSION['cart']); // reset indeks
    $cart = $_SESSION['cart'];

    if (count($cart) == 0) {
        echo "<script>alert('Keranjang kosong!'); window.location='index.php';</script>";
        exit;
    } else {
        header("Location: rent.php");
        exit;
    }
}
?>
