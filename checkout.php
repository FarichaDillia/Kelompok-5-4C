<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<script>alert('Keranjang kosong!'); window.location='checkout.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $tanggal = $_POST['tanggal'];
    $durasi = (int) $_POST['durasi'];
    $user_id = $_SESSION['user_id'];

    foreach ($cart as $item) {
        $item_id = $item['id'];
        $jumlah = $item['qty'];
        $status = 'pending';

        mysqli_query($conn, "INSERT INTO pesanan (user_id, item_id, jumlah, status) 
                             VALUES ($user_id, $item_id, $jumlah, '$status')");
    }

    unset($_SESSION['cart']);
    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='checkout.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Form Checkout - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4">Formulir Checkout</h2>

    <form method="POST" class="mx-auto" style="max-width: 600px;">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat Pengiriman</label>
            <textarea name="alamat" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Sewa</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Durasi Sewa (hari)</label>
            <input type="number" name="durasi" class="form-control" min="1" value="1" required>
        </div>
      
        <div class="text-center">
        <a href="pembayaran.php" class="btn btn-primary px-5 py-2"> Konfirmasi Pesanan </a>
    </div>
    </form>
</div>
</body>
</html>