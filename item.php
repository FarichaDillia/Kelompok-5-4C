<?php
include "config.php";

// Ambil semua item dari database
$items = mysqli_query($conn, "SELECT * FROM items");

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
                <li class="nav-item"><a class="nav-link" href="navbar.php">Home</a></li>
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

    <!-- Search Section -->
<form action="item.php" method="GET" class="search-form">
  <div class="search-wrapper">
    <input type="text" name="search" class="search-input" placeholder="Search Item">
    <button type="submit" class="search-btn">Search</button>
  </div>
</form>

    <!-- Item -->
          <section class="item-selection text-center py-5">
      <div class="container">
        <h2 class="mb-5 fw-bold"><i class="fas fa-box-open"></i> Item Selection</h2>
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
                <a href="item-detail.php?id=<?= $item['id'] ?>&from=index" class="btn btn-view">Rent</a>
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
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
      </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
