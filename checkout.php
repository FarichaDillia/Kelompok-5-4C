<?php
session_start();
include "config.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$user_query = mysqli_query($conn, "SELECT username, email, alamat FROM users WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);

// Ambil isi keranjang
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<script>alert('Keranjang kosong!'); window.location='navbar.php';</script>";
    exit;
}

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === 'form') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validasi tanggal
    if (strtotime($end_date) < strtotime($start_date)) {
        echo "<script>alert('End Date tidak boleh sebelum Start Date.'); window.history.back();</script>";
        exit;
    }

    $durasi = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;

    $_SESSION['checkout_data'] = [
        'nama'       => mysqli_real_escape_string($conn, $_POST['nama']),
        'email'      => mysqli_real_escape_string($conn, $_POST['email']),
        'alamat'     => mysqli_real_escape_string($conn, $_POST['alamat']),
        'start_date' => $start_date,
        'end_date'   => $end_date,
        'durasi'     => $durasi,
    ];

    $selected_ids = $_POST['selected_items'] ?? [];
    if (empty($selected_ids)) {
        echo "<script>alert('Tidak ada item yang dipilih untuk checkout.'); window.location='rent.php';</script>";
        exit;
    }

    $filtered = array_filter($cart, function ($item) use ($selected_ids) {
        return in_array($item['id'], $selected_ids);
    });

    if (empty($filtered)) {
        echo "<script>alert('Item tidak ditemukan dalam keranjang.'); window.location='rent.php';</script>";
        exit;
    }

    $_SESSION['checkout_items'] = array_values($filtered);
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
    body {
      background-color: #77acc7;
    }
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

        <!-- Kirim ulang selected_items -->
        <?php if (isset($_POST['selected_items'])): ?>
            <?php foreach ($_POST['selected_items'] as $id): ?>
                <input type="hidden" name="selected_items[]" value="<?= htmlspecialchars($id) ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <script>alert("Akses tidak valid, silakan kembali."); window.location = 'rent.php';</script>
        <?php endif; ?>

        <!-- Nama -->
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user_data['username']) ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" required>
        </div>

        <!-- Alamat -->
        <div class="mb-3">
            <label class="form-label">Alamat Pengiriman</label>
            <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($user_data['alamat']) ?></textarea>
        </div>

        <!-- Start Date -->
        <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
        </div>

        <!-- End Date -->
        <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" required value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>

        <!-- Submit -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary px-5 py-2">Konfirmasi Pesanan</button>
        </div>
    </form>
  </div>
</div>

</body>
</html>
