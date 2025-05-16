<?php
// Koneksi database
$conn = new mysqli("localhost", "root", "", "rentify");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rent = $_POST['id_rent'];
    $id_item = $_POST['id_item'];
    $id_user = $_POST['id_user'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO review (id_rent, id_item, id_user, rating, comment)
            VALUES ('$id_rent', '$id_item', '$id_user', '$rating', '$comment')";

    if ($conn->query($sql) === TRUE) {
    echo "<script>
        alert('Review berhasil dikirim!');
        window.location.href = '../index.php';
    </script>";
    exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Rent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d0d3f;
            color: white;
            padding-top: 50px;
        }
        .card {
            border-radius: 20px;
            padding: 30px;
        }
        .star-rating label {
            font-size: 2rem;
            color: lightgray;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: gold;
        }
        .star-rating {
            direction: rtl;
            display: inline-flex;
        }
        .star-rating input {
            display: none;
        }
        .car-img {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 10px;
        }
        .form-control, .btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>
       <!-- Navbar Start -->
        <nav style="background-color: #7fc7d9" class="navbar navbar-expand-lg mb-3">
    <div class="container">
         <!-- Logo -->
         <a href="../index.php"><img style="height: 60px" src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
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
<div class="container">
    <h2 class="text-center mb-4">Review Rent</h2>
    <form method="post" class="card bg-light text-dark mx-auto" style="max-width: 600px;">
        <!-- Hidden ID (set secara dinamis atau dari session) -->
        <input type="hidden" name="id_rent" value="1">
        <input type="hidden" name="id_item" value="1">
        <input type="hidden" name="id_user" value="1">

        <h5>Rating</h5>
        <div class="star-rating mb-3">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                <label for="star<?= $i ?>">★</label>
            <?php endfor; ?>
        </div>

        <div class="mb-3">
            <textarea class="form-control" name="comment" rows="3" placeholder="Write your review..."></textarea>
        </div>

        <img src="../img/car.jpg" class="car-img mb-3" alt="Car Image">

        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>
<!-- Footer -->
<footer class="footer text-center py-4">
  <div class="container-fluid">
    <p>&copy; 2024 Rentify - Team 5. All rights reserved.</p>
  </div>
</footer>
<!-- Footer End-->
<script src="rating.js"></script>
</body>
</html>
