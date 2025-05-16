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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Rentify</title>
    <!-- Link updated to lib/ -->
    <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
   <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
            <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-tools"></i></div>
            <div class="sidebar-brand-text mx-3">Rentify Admin</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-fw fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li class="nav-item"><a class="nav-link" href="barang.php"><i class="fas fa-box"></i> <span>Kelola Barang</span></a></li>
        <li class="nav-item"><a class="nav-link" href="pesanan.php"><i class="fas fa-check-circle"></i> <span>Verifikasi Pesanan</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <span class="navbar-brand">Dashboard Admin</span>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <!-- User Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_user ?></div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Item Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Items</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_item ?></div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pesanan Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Verified Orders</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pesanan ?></div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="text-center my-auto"><span>&copy; 2024 Rentify - Team 5</span></div>
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
