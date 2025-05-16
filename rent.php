<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Tambah atau kurang jumlah item
if (isset($_GET['action'], $_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                if ($_GET['action'] == 'plus') {
                    $item['qty']++;
                } elseif ($_GET['action'] == 'minus') {
                    if ($item['qty'] > 1) {
                        $item['qty']--;
                    } else {
                        // Hapus item jika qty == 1 dan dikurangi
                        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($i) use ($id) {
                            return $i['id'] != $id;
                        });
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                    }
                }
                break;
            }
        }
        unset($item);
    }
    header("Location: rent.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (count($cart) == 0) {
    echo "<script>alert('Keranjang kosong!'); window.location='index.php';</script>";
    exit;
}
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
                 <li class="nav-item"><a class="nav-link" href="#">Rent</a></li>
                 <li class="nav-item"><a class="nav-link" href="riwayat.php">History</a></li>
            </ul>
        </div>
        <!-- Account -->
        <div class="account">
            <a href="login.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Account</a>
        </div>
        <!-- Account End -->
    </nav>
    <!-- Navbar End -->

<!-- Isi Keranjang -->
<div class="container rent-section py-5">
    
    <h2 class="section-title mb-5 fw-bold">
  <i class="fas fa-shopping-cart"></i> Keranjang Sewa
</h2>


    <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Hapus</th>
            </tr>
        </thead>
        <tbody>
            <?php $grandTotal = 0; ?>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><img src="img/<?= htmlspecialchars($item['gambar']) ?>" width="80"></td>
                    <td><?= htmlspecialchars($item['nama']) ?></td>
                    <td>Rp. <?= number_format($item['harga']) ?></td>
                    <td>
                        <a href="rent.php?action=minus&id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">-</a>
                        <span class="mx-2"><?= $item['qty'] ?></span>
                        <a href="rent.php?action=plus&id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">+</a>
                    </td>
                    <td>Rp. <?= number_format($item['harga'] * $item['qty']) ?></td>
                    <td>
                        <a href="remove_from_cart.php?id=<?= $item['id'] ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Yakin ingin menghapus barang ini dari keranjang?');">
   <i class="fas fa-trash"></i> Hapus
</a>

                    </td>
                </tr>
                <?php $grandTotal += $item['harga'] * $item['qty']; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" class="text-end"><strong>Total Keseluruhan:</strong></td>
                <td colspan="2"><strong>Rp. <?= number_format($grandTotal) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="checkout.php" class="btn btn-success px-5 py-2">
            <i class="fas fa-check-circle"></i> Checkout Semua
        </a>
    </div>
</div>

<!-- Footer -->
    <footer class="footer text-center py-4">
      <div class="container-fluid">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
      </div>
    </footer>

</body>
</html>
