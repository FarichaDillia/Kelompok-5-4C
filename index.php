<?php
include "config.php";
$items = mysqli_query($conn, "SELECT * FROM items LIMIT 8");

// Hitung total user
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user'"))['total'];

// Hitung total item
$total_item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items"))['total'];

// Hitung pesanan terverifikasi
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status='verified'"))['total'];

// Years growth (bisa statis atau ambil dari setting database)
$years_growth = 5;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO (Search Engine Optimization) -->
    <meta name="keywords" content="Rentify, sewa barang, marketplace penyewaan, rental online">
    <meta name="description"
        content="Rentify adalah platform penyewaan berbasis web yang memudahkan pengguna mencari dan menyewa berbagai barang dengan mudah dan hemat.">

    <title>Rentify - Team 5</title>

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
         <!-- Logo -->
         <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
        </div>
         <!-- Logo End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
            </ul>
        </div>
        <!-- Account -->
        <div class="account">
            <a href="login.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Account</a>
        </div>
        <!-- Account End -->
    </nav>
    <!-- Navbar End -->

     <!-- Carousel Start -->
     <div class="carousel">
    <div class="overlay"></div>
    <img src="img/rent.png" alt="Carousel">
    <div class="carousel-text">
        <h1>Why buy when you can rent?</h1>
        <p>Save your money and only pay when you really need something. Renting keeps life simple, your space clutter-free, and your wallet happy!</p>
    </div>
</div>
    <!-- Carousel End -->

    <!-- Statistic -->
<section class="stats text-white text-center py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <h2><span class="counter" data-target="<?= $total_user ?>"><?= $total_user ?></span>+</h2>
        <p>Happy Clients</p>
      </div>
      <div class="col-md-3">
        <h2><span class="counter" data-target="<?= $total_item ?>"><?= $total_item ?></span>+</h2>
        <p>Item Ready</p>
      </div>
      <div class="col-md-3">
        <h2><span class="counter" data-target="<?= $total_pesanan ?>"><?= $total_pesanan ?></span>+</h2>
        <p>Completed Orders</p>
      </div>
      <div class="col-md-3">
        <h2><span class="counter" data-target="<?= $years_growth ?>"><?= $years_growth ?></span>+</h2>
        <p>Years Growth</p>
      </div>
    </div>
  </div>
</section>
<!-- Statistic End -->

<!-- Categories -->
<section class="categories text-center py-5">
  <div class="container">
    <h2 class="mb-5">Categories</h2>
    <div class="row">
      <div class="col-md-2 offset-md-1">
        <i class="fas fa-car fa-3x mb-3"></i>
        <p>Vehicle</p>
      </div>
      <div class="col-md-2">
        <i class="fas fa-laptop fa-3x mb-3"></i>
        <p>Elektronik & Gadget</p>
      </div>
      <div class="col-md-2">
        <i class="fas fa-tshirt fa-3x mb-3"></i>
        <p>Cloth & Accessories</p>
      </div>
      <div class="col-md-2">
        <i class="fas fa-blender fa-3x mb-3"></i>
        <p>Home Appliances</p>
      </div>
      <div class="col-md-2">
        <i class="fas fa-football-ball fa-3x mb-3"></i>
        <p>Sports Equipment</p>
      </div>
    </div>
  </div>
</section>
<!-- Categories End-->

<!-- Item -->
<section class="item-selection text-center py-5">
  <div class="container">
    <h2 class="mb-5"><i class="fas fa-box-open"></i> Item Selection</h2>
    <div class="row">
      <?php while ($item = mysqli_fetch_assoc($items)): ?>
      <div class="col-md-3 mb-4">
        <div class="card">
          <img src="img/<?= $item['gambar'] ?>" class="card-img-top" alt="<?= $item['nama'] ?>">
          <div class="card-body">
            <h5 class="card-title"><?= $item['nama'] ?></h5>
            <p class="card-text">Rp. <?= number_format($item['harga']) ?> / Day</p>
            <p class="card-description"><?= $item['deskripsi'] ?></p>
            <p class="card-stock"><strong>Stok:</strong> <?= $item['stok'] ?> pcs</p>
            <a href="checkout.php?id=<?= $item['id'] ?>" class="btn btn-view">Rent</a>
            <i class="fas fa-heart favorite-icon"></i>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <div class="text-center mt-4">
      <a href="item.php" class="btn btn-view-all">View All</a>
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
<!-- Footer End-->

<!-- File JavaScript -->
<script src="script.js"></script>
</body>

</html>