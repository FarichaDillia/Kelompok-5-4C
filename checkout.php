<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<script>alert('Keranjang kosong!'); window.location='navbar.php';</script>";
    exit;
}

// Jika belum mengirim form checkout, tampilkan form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['step']) && $_POST['step'] === 'form') {
    // Simpan data user ke session
    $_SESSION['checkout_data'] = [
        'nama'    => mysqli_real_escape_string($conn, $_POST['nama']),
        'email'   => mysqli_real_escape_string($conn, $_POST['email']),
        'alamat'  => mysqli_real_escape_string($conn, $_POST['alamat']),
        'tanggal' => $_POST['tanggal'],
        'durasi'  => (int) $_POST['durasi'],
    ];

    // Ambil item yang dicentang
    $selected_ids = $_POST['selected_items'] ?? [];

    if (empty($selected_ids)) {
        echo "<script>alert('Tidak ada item yang dipilih untuk checkout.'); window.location='rent.php';</script>";
        exit;
    }

    // Filter data cart hanya untuk item terpilih
    $filtered = array_filter($cart, function($item) use ($selected_ids) {
        return in_array($item['id'], $selected_ids);
    });

    if (empty($filtered)) {
        echo "<script>alert('Item tidak ditemukan dalam keranjang.'); window.location='rent.php';</script>";
        exit;
    }

    // Simpan item ke session
    $_SESSION['checkout_items'] = array_values($filtered);

    // Redirect ke pembayaran
    header("Location: pembayaran.php");
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
  <style>
    .checkout-container {
      background-color: #fff;
      border-radius: 15px;
      padding: 40px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: #333;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .form-label {
      font-weight: 600;
      color: #333;
    }

    .form-control {
      border-radius: 10px;
      padding: 12px;
      font-size: 15px;
    }

    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(43, 58, 103, 0.25);
      border-color: #2b3a67;
    }

    .btn-primary {
      background-color: #2b3a67;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #1f2a4d;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="checkout-container">
    <h2 class="text-center">Formulir Checkout</h2>

    <form method="POST">
        <input type="hidden" name="step" value="form">

        <!-- Ambil ulang selected_items dari rent.php jika dikirim -->
        <?php if (isset($_POST['selected_items'])): ?>
            <?php foreach ($_POST['selected_items'] as $id): ?>
                <input type="hidden" name="selected_items[]" value="<?= htmlspecialchars($id) ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <script>alert("Akses tidak valid, silakan kembali."); window.location = 'rent.php';</script>
        <?php endif; ?>

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
            <button type="submit" class="btn btn-primary px-5 py-2">Konfirmasi Pesanan</button>
        </div>
    </form>
  </div>
</div>
</body>
</html>
