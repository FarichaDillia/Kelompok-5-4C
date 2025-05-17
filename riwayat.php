<?php
session_start();
include "config.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil riwayat pesanan pengguna
$user_id = $_SESSION['user_id'];
$query = "SELECT rp.id, rp.item_id, rp.jumlah, rp.total_harga, rp.status, rp.tanggal_pesan, i.nama AS item_name 
          FROM riwayat_pesanan rp
          JOIN items i ON rp.item_id = i.id
          WHERE rp.user_id = $user_id
          ORDER BY rp.tanggal_pesan DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Sewa - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <style>
        body {
        background-color: #77acc7; 
    }
    </style>

<!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
        </div>
         <!-- Logo End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
                 <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
                 <li class="nav-item"><a class="nav-link" href="ulasan.php">History</a></li>
            </ul>
        </div>
        <!-- Account -->
        <div class="account">
            <a href="login.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Account</a>
        </div>
        <!-- Account End -->
    </nav>
    <!-- Navbar End -->

    <div class="container rent-section py-5">
    
    <h2 class="section-title mb-5 fw-bold">
  <i class="fas fa-history"></i> Riwayat Sewa
</h2>


    <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Item</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp. <?= number_format($row['total_harga']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] == 'verified'): ?>
                            <a href="ulasan.php?pesanan_id=<?= $row['id'] ?>" class="btn btn-primary">Beri Ulasan</a>
                        <?php endif; ?>
                        <?php if ($row['status'] != 'verified'): ?>
                            <a href="batal_pesanan.php?pesanan_id=<?= $row['id'] ?>" class="btn btn-danger">Batalkan</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Footer -->
    <footer class="footer text-center py-4">
      <div class="container-fluid">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
      </div>
    </footer>

</body>
</html>
