<?php
session_start();
include "../config.php";

$active_page = "user";

// Cek apakah ada input pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Siapkan query dasar
$query = "SELECT id, username, email, alamat FROM users WHERE role = 'user'";

// Jika ada input pencarian, tambahkan kondisi LIKE
if (!empty($search)) {
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (username LIKE '%$escaped_search%' OR email LIKE '%$escaped_search%')";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$renters = [];

while ($row = mysqli_fetch_assoc($result)) {
    $renter_id = $row['id'];

    // Hitung jumlah total barang yang pernah disewa oleh renter ini
    $item_result = mysqli_query($conn, "
        SELECT COUNT(DISTINCT item_id) AS total_disewa
        FROM riwayat_pesanan
        WHERE user_id = $renter_id
    ");
    $item_count = ($item_result && mysqli_num_rows($item_result) > 0) ? mysqli_fetch_assoc($item_result)['total_disewa'] : 0;

    // Hitung total pembayaran yang pernah dilakukan oleh renter ini
    $income_result = mysqli_query($conn, "
        SELECT SUM(p.total_bayar) AS total_bayar
        FROM pembayaran p
        JOIN riwayat_pesanan rp ON p.pesanan_id = rp.id
        WHERE rp.user_id = $renter_id
    ");
    $monthly_income = ($income_result && mysqli_num_rows($income_result) > 0) ? mysqli_fetch_assoc($income_result)['total_bayar'] : 0;

    $renters[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'email' => $row['email'],
        'alamat' => $row['alamat'],
        'item_count' => $item_count,
        'monthly_income' => $monthly_income
    ];
}
?>




<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Owner Management - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .owner-card {
      display: flex;
      align-items: center;
      background: #fff;
      padding: 20px;
      margin-bottom: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .owner-info {
      display: flex;
      align-items: center;
      flex: 1;
    }
    .owner-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }
    .owner-details h3 {
      margin: 0;
      font-size: 18px;
      color: #1a1f36;
    }
    .owner-details p {
      margin: 0;
      font-size: 14px;
      color: #6c757d;
    }
    .owner-stats, .income, .view-btn {
      text-align: center;
    }
    .stat-value {
      font-size: 18px;
      font-weight: bold;
      color: #1a1f36;
    }
    .stat-label {
      font-size: 12px;
      color: #6c757d;
    }
    .income {
      color: #2ecc71;
      font-size: 18px;
      font-weight: bold;
      width: 180px;
    }
    .view-btn {
      font-size: 20px;
      color: #2b3a67;
      width: 40px;
    }
    .owner-link {
      text-decoration: none;
      color: inherit;
      display: contents;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
  </div>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">User</a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUser">
          <li><a class="dropdown-item" href="owner.php">Owner</a></li>
          <li><a class="dropdown-item" href="renter.php">Renter</a></li>
        </ul>
      </li>
      <li class="nav-item"><a class="nav-link" href="management.php">Management</a></li>
    </ul>
  </div>
  <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
      <i class="fa fa-user-circle"></i> Profile
    </a>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4 fw-bold text-dark">Daftar Renter</h2>

  <div class="mb-4">
    <form method="GET" class="d-flex">
      <input type="text" name="search" class="form-control me-2" placeholder="Cari nama renter..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-primary">Cari</button>
    </form>
  </div>

  <?php if (empty($renters)): ?>
    <div class="alert alert-info">Tidak ada renter ditemukan.</div>
  <?php else: ?>
    <?php foreach ($renters as $renter): ?>
      <div class="owner-card">
        <div class="owner-info">
          <img src="../img/admin.jpg" class="owner-avatar" alt="<?= htmlspecialchars($renter['username']) ?>">
          <div class="owner-details">
            <h3><?= htmlspecialchars($renter['username']) ?></h3>
            <p><?= htmlspecialchars($renter['email']) ?></p>
          </div>
        </div>

        <div class="owner-stats">
          <div class="stat-item">
            <div class="stat-value"><?= $renter['item_count'] ?></div>
            <div class="stat-label">Item Rented</div>
          </div>
        </div>

        <div class="income">
          Rp. <?= number_format($renter['monthly_income'], 0, ',', '.') ?>
          <div class="stat-label">Monthly</div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<footer class="footer text-center py-4 mt-auto">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>

</body>
</html>
