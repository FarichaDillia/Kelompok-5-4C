<?php
include "config.php";

$from = isset($_GET['from']) ? $_GET['from'] : 'item';
$redirectPage = ($from === 'navbar') ? 'navbar.php' : 'item.php';

if (!isset($_GET['id'])) {
    header("Location: $redirectPage?alert=notfound");
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT items.*, users.username, users.alamat 
    FROM items 
    JOIN users ON items.owner_id = users.id 
    WHERE items.id = $id");

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: $redirectPage?alert=notfound");
    exit;
}

$item = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentify - Team 5</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <style>
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
        .image-wrapper {
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
        }
        .item-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
    </div>
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
    <div class="profile">
        <a href="profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
            <i class="fa fa-user-circle"></i> Profile
        </a>
    </div>
</nav>

<!-- Back -->
<a href="navbar.php" class="back-arrow">&#8592;</a>

<!-- Card -->
<div class="card p-4">
    <div class="row g-4 align-items-center">
        <div class="col-md-6">
            <h4><?= htmlspecialchars($item['nama']) ?></h4>
            <h5 class="text-primary">Rp. <?= number_format($item['harga']) ?> / Day</h5>
            <p class="text-success fw-bold">Available <?= $item['stok'] ?> pcs</p>
            <p><?= htmlspecialchars($item['deskripsi']) ?></p>
            <div class="mt-4">
                <h6>Information Owner</h6>
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($item['username']) ?>&background=random" alt="Owner" class="owner-img me-2">
                    <div>
                        <div><strong><?= htmlspecialchars($item['username']) ?></strong></div>
                        <div>📍 <?= htmlspecialchars($item['alamat']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="img/<?= $item['gambar'] ?>" class="item-image" alt="<?= htmlspecialchars($item['nama']) ?>">
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="add_to_cart.php?id=<?= $item['id'] ?>&from=detail" class="btn btn-rent">Rent</a>
    </div>
</div>

<!-- Footer -->
<footer class="footer text-center py-4 mt-auto">
    <div class="container-fluid">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
    </div>
</footer>

<!-- SweetAlert Notification -->
<?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Item berhasil ditambahkan ke keranjang!',
        showConfirmButton: false,
        timer: 2000
    });
});
</script>
<?php endif; ?>

<?php if (isset($_GET['alert']) && $_GET['alert'] === 'notfound'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: 'Item tidak ditemukan!',
        confirmButtonText: 'OK'
    });
});
</script>
<?php endif; ?>

</body>
</html>
