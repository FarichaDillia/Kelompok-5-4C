<?php
include "config.php";
$items = mysqli_query($conn, "SELECT * FROM items LIMIT 8");

// Hitung total user
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user'"))['total'];

// Hitung total item
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items"))['total'];

// Hitung pesanan terverifikasi
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status='verified'"))['total'];

// Years growth (bisa statis atau ambil dari setting database)
$years_growth = 5;

// Tangkap input dari form
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query pencarian
if (!empty($search)) {
    $query = "SELECT * FROM items WHERE nama LIKE '%$search%' OR deskripsi LIKE '%$search%'";
} else {
    $query = "SELECT * FROM items LIMIT 8";
}

$items = mysqli_query($conn, $query); 

?>
<!DOCTYPE html>
<html lang="en">

<style>
  .search-form {
  display: flex;
  justify-content: center;
  padding: 50px;
}

.search-wrapper {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 1500px;  
  gap: 25px;
  padding: 0 2rem;
}

.search-input {
  flex: 1;
  padding: 14px 22px;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  background-color: #ffffff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  outline: none;
}

.search-btn {
  padding: 14px 30px;
  border: none;
  border-radius: 12px;
  background-color: #2b3a67;     
  color: white !important;       
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;        
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  transition: all 0.3s ease;
}


.search-btn:hover {
  background-color: #1f2a4d;
  transform: translateY(-2px);
}

</style>

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
                 <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
                 <li class="nav-item"><a class="nav-link" href="riwayat.php">History</a></li>
            </ul>
        </div>
        <!-- Profil -->
        <div class="profile">
    <a href="profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
        <i class="fa fa-user-circle"></i> Profile
    </a>
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
        <h2><span class="counter" data-target="<?= $total_users ?>"><?= $total_users ?></span>+</h2>
        <p>Happy Clients</p>
      </div>
      <div class="col-md-3">
        <h2><span class="counter" data-target="<?= $total_items ?>"><?= $total_items ?></span>+</h2>
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
    <h2 class="mb-5 fw-bold">Categories</h2>
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

<!-- Search Section -->
<form action="index.php" method="GET" class="search-form">
  <div class="search-wrapper">
    <input type="text" name="search" class="search-input" placeholder="Search Item">
    <button type="submit" class="search-btn">Search</button>

  </div>
</form>


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
                <a href="add_to_cart.php?id=<?= $item['id'] ?>&from=index" class="btn btn-view">Rent</a>
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

    <!-- Find an Owner Near You -->
<section class="find-owner py-5 text-center">
  <div class="container">
    <h2 class="mb-5 fw-bold">Find an Owner Near You</h2>
    
    <div class="owner-row d-flex justify-content-center flex-wrap gap-3">
      <!-- Repeat 5 cards below -->
      <div class="owner-card card">
        <img src="img/alpha.png" class="card-img-top" alt="Muhammad Alpha">
        <div class="card-body text-start">
          <h5 class="card-title mb-1">Muhammad Alpha</h5>
          <p class="card-text"><i class="fas fa-map-marker-alt"></i> Bekasi, Indonesia</p>
        </div>
      </div>

      <div class="owner-card card">
        <img src="img/icha.png" class="card-img-top" alt="Faricha Dilla">
        <div class="card-body text-start">
          <h5 class="card-title mb-1">Faricha Dilla</h5>
          <p class="card-text"><i class="fas fa-map-marker-alt"></i> Bogor, Indonesia</p>
        </div>
      </div>

      <div class="owner-card card">
        <img src="img/resya.png" class="card-img-top" alt="Resya Hidayatunnisa">
        <div class="card-body text-start">
          <h5 class="card-title mb-1">Resya Hidayatunnisa</h5>
          <p class="card-text"><i class="fas fa-map-marker-alt"></i> Karawang, Indonesia</p>
        </div>
      </div>

      <div class="owner-card card">
        <img src="img/roma.png" class="card-img-top" alt="Roma Ulina">
        <div class="card-body text-start">
          <h5 class="card-title mb-1">Roma Ulina</h5>
          <p class="card-text"><i class="fas fa-map-marker-alt"></i> Bogor, Indonesia</p>
        </div>
      </div>

      <div class="owner-card card">
        <img src="img/dimas.png" class="card-img-top" alt="Dimas Hadi">
        <div class="card-body text-start">
          <h5 class="card-title mb-1">Dimas Hadi</h5>
          <p class="card-text"><i class="fas fa-map-marker-alt"></i> Bekasi, Indonesia</p>
        </div>
      </div>
    </div>
  </div>
</section>




<!-- Footer -->
    <footer class="footer text-center py-4">
      <div class="container-fluid">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
      </div>
    </footer>

<!-- File JavaScript -->
<script src="script.js"></script>
</body>

</html>