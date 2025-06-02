<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pesanan yang status-nya verified
if (!isset($_GET['pesanan_id'])) {
    echo "<script>alert('Pesanan tidak ditemukan!'); window.location='riwayat.php';</script>";
    exit;
}

$pesanan_id = intval($_GET['pesanan_id']);
$query = "SELECT * FROM riwayat_pesanan WHERE id = $pesanan_id AND user_id = $user_id AND status = 'verified'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Pesanan tidak valid untuk diberi ulasan.'); window.location='riwayat.php';</script>";
    exit;
}

// Cek apakah review sudah pernah dibuat
$check_review = mysqli_query($conn, "SELECT * FROM review WHERE id_rent = $pesanan_id AND id_user = $user_id");
if (mysqli_num_rows($check_review) > 0) {
    header("Location: riwayat.php?alert=review_exists");
    exit;
}


// Proses review
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rent  = $_POST['id_rent'];
    $id_item  = $_POST['id_item'];
    $id_user  = $_POST['id_user'];
    $rating   = $_POST['rating'];
    $comment  = $_POST['comment'];

    $sql = "INSERT INTO review (id_rent, id_item, id_user, rating, comment)
            VALUES ('$id_rent', '$id_item', '$id_user', '$rating', '$comment')";

  if ($conn->query($sql) === TRUE) {
    header("Location: riwayat.php?alert=reviewed");
    exit();
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item</title>

    <!-- SEO -->
    <meta name="keywords" content="Rentify, sewa barang, marketplace penyewaan, rental online">
    <meta name="description" content="Rentify adalah platform penyewaan berbasis web yang memudahkan pengguna mencari dan menyewa berbagai barang dengan mudah dan hemat.">

    <!-- CSS & Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Custom Style -->
    <style>
        body {
            background-color: #77acc7;
        }

        .review-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 20px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 60px auto;
        }

        .review-box h2 {
            font-weight: 700;
            color: #2b3a67;
            text-align: center;
            margin-bottom: 25px;
        }

        .star-rating {
            direction: rtl;
            display: inline-flex;
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

        .star-rating input {
            display: none;
        }

        .search-btn {
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            background-color: #2b3a67;
            color: white !important;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background-color: #1f2a4d;
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="#"><img src="img/logo.jpg" style="height: 60px" alt="Logo" class="logo-img"></a>
        </div>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="navbar.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
                <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php">History</a></li>
            </ul>
        </div>
        <div class="profile">
            <a href="profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
                <i class="fa fa-user-circle"></i> Profile
            </a>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Review Form -->
    <div class="review-box">
        <h2>Review Pesanan Anda</h2>
        <form method="post">
            <input type="hidden" name="id_rent" value="<?= $data['id'] ?>">
            <input type="hidden" name="id_item" value="<?= $data['item_id'] ?>">
            <input type="hidden" name="id_user" value="<?= $data['user_id'] ?>">

            <div class="mb-3">
                <label class="form-label">Rating</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                        <label for="star<?= $i ?>">&#9733;</label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="mb-3">
                <textarea class="form-control" name="comment" rows="3" placeholder="Tulis ulasan Anda..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Kirim Ulasan</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer text-center py-4 mt-auto">
        <div class="container-fluid">
            <p>&copy; 2024 Rentify - Team 5. All rights reserved.</p>
        </div>
    </footer>

    <script src="rating.js"></script>
</body>
</html>
