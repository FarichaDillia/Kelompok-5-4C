<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Ambil ID pesanan yang ingin diedit
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data pesanan berdasarkan ID
    $query = "SELECT pesanan.id, pesanan.jumlah, pesanan.periode, pesanan.item_id, items.nama AS item_name, items.harga
              FROM pesanan 
              JOIN items ON pesanan.item_id = items.id 
              WHERE pesanan.id = $id";
    $result = mysqli_query($conn, $query);
    $pesanan = mysqli_fetch_assoc($result);

    // Jika pesanan tidak ditemukan
    if (!$pesanan) {
        echo "<script>alert('Pesanan tidak ditemukan.'); window.location.href='rent.php';</script>";
        exit;
    }
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $jumlah = $_POST['jumlah'];
    $periode = $_POST['periode'];

    // Update data pesanan di database
    $query = "UPDATE pesanan SET 
                jumlah = '$jumlah',
                periode = '$periode'
              WHERE id = $id";

     if (mysqli_query($conn, $query)) {
        // Jika update berhasil, arahkan ke halaman home (dashboard.php)
        echo "<script>alert('Item berhasil diupdate!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate item.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body id="page-top">
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
        </div>
         <!-- Logo End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                 <li class="nav-item">
                    <a class="nav-link <?php if($page == 'home') echo 'active'; ?>" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'item') echo 'active'; ?>" href="item.php">Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'rent') echo 'active'; ?>" href="rent.php">Rent</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'review') echo 'active'; ?>" href="review.php">Review</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'transaction') echo 'active'; ?>" href="transaction.php">Transaction</a>
                </li>
            </ul>
        </div>
        <!-- Profil -->
        <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
        <i class="fa fa-user-circle"></i> Profile
    </a>
</div>
    </nav>
    <!-- Navbar End -->
     
 <!-- Main Content -->
            <div class="container-fluid">
                <div class="card shadow mb-4 col-lg-8 mx-auto">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Barang</h6>
                    </div>
                    <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($pesanan['jumlah']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="periode" class="form-label">Periode</label>
                        <input type="text" class="form-control" id="periode" name="periode" value="<?= htmlspecialchars($pesanan['periode']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Pesanan</button>
                    <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>

<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>