<?php
session_start();
include "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"]; // ID owner yang login

// Total item milik owner
$item_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM items WHERE owner_id = $user_id");
$total_item = mysqli_fetch_assoc($item_result)['total'] ?? 0;

// Total pesanan verified untuk item milik owner
$rented_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM riwayat_pesanan rp
    JOIN items i ON rp.item_id = i.id
    WHERE i.owner_id = $user_id AND rp.status = 'verified'
");
$total_rented = mysqli_fetch_assoc($rented_result)['total'] ?? 0;

// Total pemasukan dari pembayaran untuk pesanan item milik owner
$salary_result = mysqli_query($conn, "
    SELECT SUM(p.total_bayar) AS total_salary
    FROM pembayaran p
    JOIN riwayat_pesanan rp ON rp.id = p.pesanan_id
    JOIN items i ON rp.item_id = i.id
    WHERE i.owner_id = $user_id
");
$total_salary = mysqli_fetch_assoc($salary_result)['total_salary'] ?? 0;
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="style.css">

</head>
<body class="d-flex flex-column min-vh-100">
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    const dashboardChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Total Salary', 'Total Rented', 'Total Item'],
        datasets: [{
          label: 'Statistik',
          data: [<?= $total_salary ?>, <?= $total_rented ?>, <?= $total_item ?>],
          backgroundColor: ['#4b6cb7', '#38b000', '#00b4d8'],
          borderRadius: 8,
          barThickness: 40 // ✅ Batasi ketebalan bar agar tidak memanjang
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true, // ✅ Aktifkan kembali agar tidak stretch
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });
  });
</script>



   <!-- Navbar Start -->
       <nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
  </div>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
      <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
      <li class="nav-item"><a class="nav-link" href="review.php">Review</a></li>
      <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
    </ul>
  </div>
  <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
      <i class="fa fa-user-circle"></i> Profile
    </a>
  </div>
</nav>
    <!-- Navbar End -->

<div class="container mt-5 mb-4">
  <p style="font-weight:700; color:#0f1a3c; margin-bottom: 0; font-size: 25px;">Welcome, Owner !</p>
  <h2 style="font-weight:800; color:#0f1a3c; margin-top: 0; letter-spacing: 1px;">DASHBOARD</h2>
  <p class="text-muted">Here is your overview summary</p>
</div>

<!-- Dashboard Content -->
<div class="container mb-5">
  <div class="row justify-content-center g-4">

    <!-- Card 1: Total Salary -->
    <div class="col-lg-4 col-md-6">
      <div class="bg-white rounded shadow-sm p-4 position-relative border-start border-4" style="border-color: #4b6cb7;">
        <div class="text-uppercase text-primary small fw-bold mb-2">Total Salary</div>
        <div class="h5 fw-bold text-dark">Rp. <?= number_format($total_salary, 0, ',', '.') ?></div>
        <i class="fas fa-money-bill-wave fa-2x text-secondary position-absolute" style="bottom: 15px; right: 15px;"></i>
      </div>
    </div>

    <!-- Card 2: Total Rented -->
    <div class="col-lg-4 col-md-6">
      <div class="bg-white rounded shadow-sm p-4 position-relative border-start border-4" style="border-color: #38b000;">
        <div class="text-uppercase text-success small fw-bold mb-2">Total Rented</div>
        <div class="h5 fw-bold text-dark"><?= $total_rented ?></div>
        <i class="fas fa-users fa-2x text-secondary position-absolute" style="bottom: 15px; right: 15px;"></i>
      </div>
    </div>

    <!-- Card 3: Total Item -->
    <div class="col-lg-4 col-md-6">
      <div class="bg-white rounded shadow-sm p-4 position-relative border-start border-4" style="border-color: #00b4d8;">
        <div class="text-uppercase text-info small fw-bold mb-2">Total Item</div>
        <div class="h5 fw-bold text-dark"><?= $total_item ?></div>
        <i class="fas fa-box fa-2x text-secondary position-absolute" style="bottom: 15px; right: 15px;"></i>
      </div>
    </div>

  </div>
</div>
<!-- Grafik Statistik -->
<div class="container mb-5">
  <div class="card shadow-sm p-4">
    <h5 class="fw-bold text-dark mb-4">Statistik Penyewaan</h5>
    <!-- Tambahkan style: max-width dan height agar tidak memanjang -->
        <div style="max-width: 500px; width: 100%;"></div>
      <canvas id="dashboardChart"></canvas>
    </div>
  </div>
</div>

 <!-- Footer -->
    <footer class="footer text-center py-4">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
    </footer>

<!-- JS updated path -->
<script src="../lib/vendor/jquery/jquery.min.js"></script>
<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../lib/js/sb-admin-2.min.js"></script>

</body>
</html>