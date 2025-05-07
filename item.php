<?php
include "config.php";

// Ambil semua item dari database
$items = mysqli_query($conn, "SELECT * FROM items");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <meta name="keywords" content="Rentify, sewa barang, marketplace penyewaan, rental online">
    <meta name="description" content="Rentify adalah platform penyewaan berbasis web yang memudahkan pengguna mencari dan menyewa berbagai barang dengan mudah dan hemat.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- File CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <title>Item</title>
</head>
<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#item">Item</a></li>
        </ul>
    </div>
    <div class="account">
        <a href="login.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Account</a>
    </div>
    </nav>
    <!-- Navbar End -->

    <!-- Item -->
    <section class="item-selection text-center py-5">
      <div class="container">
        <h2 class="mb-5"><i class="fas fa-box-open"></i> Item Selection</h2>
        <div class="row">
          <?php while ($item = mysqli_fetch_assoc($items)): ?>
          <div class="col-md-3 mb-4">
            <div class="card">
              <img src="img/<?= $item['gambar'] ?>" class="card-img-top" alt="<?= htmlspecialchars($item['nama']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                <p class="card-text">Rp. <?= number_format($item['harga']) ?> / Day</p>
                <p class="card-description"><?= htmlspecialchars($item['deskripsi']) ?></p>
                <p class="card-stock"><strong>Stok:</strong> <?= $item['stok'] ?> pcs</p>
                <a href="checkout.php?id=<?= $item['id'] ?>" class="btn btn-view">Rent</a>
                <i class="fas fa-heart favorite-icon"></i>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </section>
    <!-- Item End -->

    <!-- Footer -->
    <footer class="footer text-center py-4">
      <div class="container-fluid">
        <p>&copy; 2024 Rentify - Team 5. All rights reserved.</p>
      </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
