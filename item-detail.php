<?php
// include 'config.php';
// $result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Item</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
            background: linear-gradient(to bottom right, #a2c9e7, #e2f0fb);
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #0b1a3e;
        }

        .navbar-nav .nav-link {
            color: white !important;
            margin: 0 15px;
        }

        .nav-link.active {
            color: #00d1ff !important;
        }

        .card {
            max-width: 900px;
            margin: 60px auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .btn-rent {
            background-color: #2d3b66;
            color: white;
            padding: 10px 30px;
            font-weight: bold;
            border-radius: 10px;
        }

        .btn-rent:hover {
            background-color: #1d2a52;
        }

        .footer {
            background-color: #0b1a3e;
            color: white;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .owner-img {
            width: 60px;
            border-radius: 50%;
        }

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- Navbar -->
       <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="img/logo.jpg" alt="Logo" style="height: 60px" class="logo-img"></a>
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

<!-- Back Arrow -->
<a href="index.php" class="back-arrow">&#8592;</a>

<!-- Card Content -->
<div class="card p-4">
  <div class="row g-4 align-items-center">
    <div class="col-md-6">
      <h4>Car Sport</h4>
      <h5 class="text-primary">Rp. 100.000 / Day</h5>
      <p class="text-success fw-bold">Available 2</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
      <div class="mt-4">
        <h6>Information Owner</h6>
        <div class="d-flex align-items-center">
          <img src="https://ui-avatars.com/api/?name=Faricha+Dillia&background=random" alt="Owner" class="owner-img me-2">
          <div>
            <div><strong>Faricha Dillia</strong></div>
            <div>📍 Bogor, Indonesia</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 text-center">
      <img src="img/car.jpg" class="img-fluid rounded" alt="Car">
      <br><br>
      <a href="checkout.php" class="btn btn-rent">Rent</a> <!-- Kamu bisa ganti link ini -->
    </div>
  </div>
</div>

<!-- Footer -->
<div class="footer">
    Created by KELOMPOK 5 <br>
    © 2025 All Rights Reserved.
</div>

</body>
</html>
