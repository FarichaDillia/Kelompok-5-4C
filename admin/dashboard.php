<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$total_item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM items"))['total'];
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status='verified'"))['total'];

$rented_items_query = "SELECT pesanan.id, items.nama AS item_name, pesanan.jumlah, pesanan.periode, items.harga 
FROM pesanan 
JOIN items ON pesanan.item_id = items.id 
WHERE pesanan.status='verified'
";
$result_pesanan = mysqli_query($conn, $rented_items_query);

// Cek apakah query berhasil dan ada data
if (!$result_pesanan) {
    die("Error executing query: " . mysqli_error($conn));
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


            <!-- Dashboard Content -->
            <div class="container-fluid mt-5">
                <div class="row">
                    <!-- Total Salary Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Salary</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. 1.000.000</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-money-bill-alt fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Rented Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Rented</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">10</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Item Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Item</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">40</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid mt-5"></div>
                <h2>Item Rented</h2>
                <div class="container-fluid mt-3"></div>
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Name Item</th>
                            <th>Qty</th>
                            <th>Period</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($item = mysqli_fetch_assoc($result_pesanan)) {
                            echo "<tr>
                                    <td>".$no."</td>
                                    <td>".$item['item_name']."</td>
                                    <td>".$item['jumlah']."</td>
                                    <td>".$item['periode']."</td>
                                    <td>Rp ".number_format($item['harga'])."</td>
                                    <td>
                                        <a href='edithome.php?id=".$item['id']."' class='btn btn-warning'>Edit</a>
                                        <a href='hapushome.php?id=".$item['id']."' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger'>Delete</a>
                                    </td>
                                </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

        <!-- Footer -->
        <footer class="footer text-center py-4">
            <div class="container-fluid">
                <p>&copy; 2024 Rentify - Team 5. All rights reserved.</p>
            </div>
        </footer>
    </div>
</div>

<!-- JS updated path -->
<script src="../lib/vendor/jquery/jquery.min.js"></script>
<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../lib/js/sb-admin-2.min.js"></script>

</body>
</html>