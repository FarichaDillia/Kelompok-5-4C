<?php
session_start();
include "config.php";

// Pastikan user sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Ambil id item dari URL
if (!isset($_GET['id'])) {
    echo "<script>alert('Item tidak ditemukan!'); window.location='item.php';</script>";
    exit;
}

$id = $_GET['id'];
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE id=$id"));

if (!$item) {
    echo "<script>alert('Item tidak ditemukan di database!'); window.location='item.php';</script>";
    exit;
}

// Proses submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $alamat = mysqli_real_escape_string($conn, $_POST["alamat"]);
    $tanggal = $_POST["tanggal"];
    $durasi = (int) $_POST["durasi"];
    $jumlah = 1; // default sewa 1 item

    mysqli_query($conn, "INSERT INTO pesanan (user_id, item_id, jumlah, status) VALUES ({$_SESSION['user_id']}, $id, $jumlah, 'pending')");

    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='item.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="keywords" content="Rentify, sewa barang, marketplace penyewaan, rental online">
  <meta name="description" content="Rentify - Platform penyewaan barang online">
  <title>Checkout - Rentify</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- File CSS -->
  <link rel="stylesheet" href="style.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
  </div>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
    </ul>
  </div>
  <div class="account">
    <a href="logout.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Account</a>
  </div>
</nav>
<!-- Navbar End -->

<!-- Checkout Section -->
<section class="checkout-section py-5">
  <div class="container">
    <h2 class="text-center mb-4"><i class="fas fa-shopping-cart"></i> Checkout Item</h2>

    <!-- Detail Item -->
    <div class="card mb-4 mx-auto" style="max-width: 500px;">
      <div class="row g-0">
        <div class="col-md-4">
          <img src="img/<?= htmlspecialchars($item['gambar']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($item['nama']) ?>">
        </div>
        <div class="col-md-8">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
            <p class="card-text">Rp. <?= number_format($item['harga']) ?> / Day</p>
            <p class="card-text"><small class="text-muted">Stok tersedia: <?= $item['stok'] ?> pcs</small></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Checkout -->
    <form method="POST" class="mx-auto" style="max-width: 500px;">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama anda" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Masukkan email anda" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat pengiriman" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Tanggal Sewa</label>
        <input type="date" name="tanggal" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Durasi (hari)</label>
        <input type="number" name="durasi" class="form-control" min="1" value="1" required>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-view px-4 py-2">Konfirmasi Sewa</button>
      </div>
    </form>
  </div>
</section>
<!-- Checkout End -->

<!-- Footer -->
<footer class="footer text-center py-4">
  <div class="container-fluid">
    <p>&copy; 2024 Rentify - Team 5. All rights reserved.</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
