<?php
session_start();
include "../config.php";
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Query data pesanan
$query = "SELECT pesanan.id, users.username, items.nama AS item_name, 
          items.kategori AS item_category, pesanan.jumlah, pesanan.total_price, 
          pesanan.start_date, pesanan.end_date, 
          DATEDIFF(pesanan.end_date, pesanan.start_date) AS periode, pesanan.status 
          FROM pesanan 
          JOIN users ON pesanan.user_id = users.id 
          JOIN items ON pesanan.item_id = items.id";
$result = mysqli_query($conn, $query);

// Pastikan query berhasil
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

// Jika ada parameter verifikasi
if (isset($_GET['verifikasi'])) {
    $id = $_GET['verifikasi'];
    mysqli_query($conn, "UPDATE pesanan SET status='verified' WHERE id = $id");
    header("Location: rent.php");
    exit;
}

// Jika ada parameter cancel
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    
    // Cek apakah status masih Pending, jika iya, update jadi Cancelled
    $checkQuery = "SELECT status FROM pesanan WHERE id = $id";
    $resultCheck = mysqli_query($conn, $checkQuery);
    $order = mysqli_fetch_assoc($resultCheck);
    
    if ($order && $order['status'] != 'Cancelled') {
        mysqli_query($conn, "UPDATE pesanan SET status='Cancelled' WHERE id = $id");
        header("Location: rent.php");
        exit;
    } else {
        echo "<script>alert('Status sudah dibatalkan atau tidak ditemukan.');</script>";
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

             <!-- Rent Table -->
            <div class="container-fluid mt-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Rent List</h6>
                    </div>
        
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Name Item</th>
                                        <th>Kategori</th>
                                        <th>Periode</th>
                                        <th>Total Price</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($order = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($order['username']) ?></td>
                                        <td><?= htmlspecialchars($order['item_name']) ?></td>
                                        <td><?= htmlspecialchars($order['item_category']) ?></td> <!-- Display Category -->
                                        <td><?= $order['periode'] ?> Days</td>
                                        <td>Rp <?= number_format($order['total_price']) ?></td>
                                        <td><?= date('d-m-Y', strtotime($order['start_date'])) ?></td>
                                        <td><?= date('d-m-Y', strtotime($order['end_date'])) ?></td>
                                        <td>
                                            <span class="badge <?= $order['status'] == 'verified' ? 'badge-success' : ($order['status'] == 'Cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                                <?= $order['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($order['status'] == 'Pending'): ?>
                                                <a href="?verifikasi=<?= $order['id'] ?>" class="btn btn-sm btn-success">Confirm</a>
                                            <?php endif; ?>
                                            <?php if ($order['status'] != 'Cancelled'): ?>
                                                <a href="?cancel=<?= $order['id'] ?>" class="btn btn-sm btn-danger">Cancel</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
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
