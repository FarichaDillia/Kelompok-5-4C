<?php
session_start();
include "../config.php";

$active_page = "user";

// Tangkap pencarian jika ada
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$escaped_search = mysqli_real_escape_string($conn, $search);

// Query ambil data owner (dengan pencarian jika ada)
$query = "SELECT id, username, email, alamat FROM users WHERE role = 'owner'";
if (!empty($escaped_search)) {
    $query .= " AND (username LIKE '%$escaped_search%' OR email LIKE '%$escaped_search%')";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$owners = [];

while ($row = mysqli_fetch_assoc($result)) {
    $owner_id = $row['id'];

    // Hitung jumlah item milik owner
    $item_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM items WHERE owner_id = $owner_id");
    $item_count = mysqli_fetch_assoc($item_result)['total'] ?? 0;

    // Hitung pemasukan bulan ini dari item owner
    $income_query = "
        SELECT SUM(p.total_bayar) AS total 
        FROM pembayaran p
        JOIN riwayat_pesanan rp ON rp.id = p.pesanan_id
        JOIN items i ON rp.item_id = i.id
        WHERE i.owner_id = $owner_id
        AND MONTH(p.tanggal_bayar) = MONTH(CURRENT_DATE())
        AND YEAR(p.tanggal_bayar) = YEAR(CURRENT_DATE())
    ";
    $income_result = mysqli_query($conn, $income_query);
    $monthly_income = mysqli_fetch_assoc($income_result)['total'] ?? 0;

    $owners[] = [
        'id' => $owner_id,
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
      justify-content: space-between; 
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
    a {
  cursor: pointer;
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
  <h2 class="mb-4 fw-bold text-dark">Daftar Owner</h2>

  <div class="mb-4">
    <form method="GET" class="d-flex">
      <input type="text" name="search" id="searchBox" class="form-control me-2" placeholder="Cari nama owner..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-primary">Cari</button>
    </form>
  </div>

  <?php if (empty($owners)): ?>
    <div class="alert alert-info">Tidak ada owner ditemukan.</div>
  <?php else: ?>
    <?php foreach ($owners as $owner): ?>
  <div class="owner-card">
    <!-- Info Owner -->
    <div class="owner-info">
      <img src="../img/admin.jpg" class="owner-avatar" alt="<?= htmlspecialchars($owner['username']) ?>">
      <div class="owner-details">
        <h3><?= htmlspecialchars($owner['username']) ?></h3>
        <p><?= htmlspecialchars($owner['email']) ?></p>
      </div>
    </div>

    <!-- Item Count -->
    <div class="owner-stats">
      <div class="stat-item">
        <div class="stat-value"><?= $owner['item_count'] ?></div>
        <div class="stat-label">Item Rented</div>
      </div>
    </div>

    <!-- Monthly Income -->
    <div class="income">
      Rp. <?= number_format($owner['monthly_income'], 0, ',', '.') ?>
      <div class="stat-label">Monthly</div>
    </div>

  </div>
<?php endforeach; ?>

 </div>
 <?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<footer class="footer text-center py-4 mt-auto">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>

</body>
</html>