<?php


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review Rent - Rentity</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style-review.css">
</head>
<body>
 <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="assets/logo.jpg" alt="Logo" style="height: 30px"class="logo-img"></a>
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

<!-- Main Content -->
<div class="container mt-5">
  <h4 class="mb-4">Review Rent</h4>
  
  <div class="card">
    <h5>Rent Received</h5>
    <hr>

<div class="rating-container">
  <div class="left-section">
    <h6 class="mt-4">Rating</h6>
    <div class="star-rating mb-3" id="starRating">
      <span class="star" data-value="1">&#9733;</span>
      <span class="star" data-value="2">&#9733;</span>
      <span class="star" data-value="3">&#9733;</span>
      <span class="star" data-value="4">&#9733;</span>
      <span class="star" data-value="5">&#9733;</span>
    </div>
  </div>

  <div class="right-section">
    <img src="assets/car.jpg" alt="Car" class="img-fluid" style="max-width: 300px;" />
  </div>
</div>

    <form action="submit_review.php" method="POST">
      <input type="hidden" name="rating" id="ratingInput" value="0">
      <div class="mb-3">
        <textarea class="form-control" name="review" rows="4" placeholder="Review" required></textarea>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-dark">Submit</button>
      </div>
    </form>

    </div>
  </div>
</div>

<!-- Footer -->
<footer>
  <div>Created by KELOMPOK 5<br>&copy; 2025 All Rights Reserved.</div>
</footer>

<script src="rating.js"></script>
</body>
</html>
