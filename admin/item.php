<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

// Ambil data barang
$result = mysqli_query($conn, "SELECT * FROM items");
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
            
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <!-- Logo Rentify di kiri -->
         <a class="navbar-brand" href="index.php">
            <img src="img/logo.jpg" alt="Logo" class="logo"> Rentify
        </a>

        <!-- Navbar Toggle untuk mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>


        <!-- Navbar Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
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
                    <a class="nav-link <?php if($page == 'return') echo 'active'; ?>" href="return.php">Return</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'review') echo 'active'; ?>" href="review.php">Review</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'transaction') echo 'active'; ?>" href="transaction.php">Transaction</a>
                </li>
                <!-- Profile dengan ikon dan teks Owner -->
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                        <span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <div class="profile-text">Owner</div>

                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
            <!-- Main Content -->
            <div class="container-fluid mt-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Name Item</th>
                                        <th>Kategori</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Code Voucher</th>
                                        <th>Stock</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($item = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($item['nama']) ?></td>
                                        <td><?= htmlspecialchars($item['kategori']) ?></td>
                                        <td><?= htmlspecialchars($item['deskripsi']) ?></td>
                                        <td>Rp <?= number_format($item['harga']) ?></td>
                                        <td><?= $item['kode_voucher'] ?></td>
                                        <td><?= $item['stok'] ?></td>
                                        <td><img src="../img/<?= htmlspecialchars($item['gambar']) ?>" width="60"></td>

                                        <td><span class="badge <?= $item['status'] == 'Available' ? 'badge-success' : ($item['status'] == 'Rented' ? 'badge-warning' : 'badge-danger') ?>"><?= $item['status'] ?></span></td>
                                        <td>
                                            <a href="edititem.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="hapusitem.php?id=<?= $item['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="tambahitem.php" class="btn btn-primary mt-3"><i class="fas fa-plus"></i> Add Item</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto text-center">
                <span>&copy; 2024 Rentify - Team 5</span>
            </div>
        </footer>
    </div>
</div>

<!-- Scripts -->
<script src="../lib/vendor/jquery/jquery.min.js"></script>
<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../lib/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../lib/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../lib/js/sb-admin-2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

</body>
</html>