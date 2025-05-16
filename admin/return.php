<?php
session_start();
include "../config.php";

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Query untuk mengambil data pengembalian
$query = "SELECT return_requests.id, users.username, items.nama AS item_name, 
                 return_requests.start_date, return_requests.reason, return_requests.status
          FROM return_requests
          JOIN users ON return_requests.user_id = users.id
          JOIN items ON return_requests.item_id = items.id";
$result = mysqli_query($conn, $query);

// Pastikan query berhasil
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

// Proses ketika ada permintaan konfirmasi
if (isset($_GET['confirm'])) {
    $id = $_GET['confirm'];
    mysqli_query($conn, "UPDATE return_requests SET status='Confirmed' WHERE id = $id");
    header("Location: return.php");
    exit;
}

// Proses ketika ada permintaan penolakan
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    mysqli_query($conn, "UPDATE return_requests SET status='Rejected' WHERE id = $id");
    header("Location: return.php");
    exit;
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
                    <a class="navbar-brand" href="index.php">
                        <img src="img/logo.jpg" alt="Logo" class="logo"> Rentify
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="item.php">Item</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="rent.php">Rent</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="return.php">Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="review.php">Review</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="transaction.php">Transaction</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">Owner</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid mt-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Return List</h6>
                    </div>
        
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>No. Order</th>
                                        <th>Name</th>
                                        <th>Name Item</th>
                                        <th>Start Date</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($order = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>OR00<?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['username']) ?></td>
                                        <td><?= htmlspecialchars($order['item_name']) ?></td>
                                        <td><?= date('d-m-Y', strtotime($order['start_date'])) ?></td>
                                        <td><?= htmlspecialchars($order['reason']) ?></td>
                                        <td>
                                            <?php if ($order['status'] == 'Pending'): ?>
                                                <a href="?confirm=<?= $order['id'] ?>" class="btn btn-sm btn-success">Confirm</a>
                                                <a href="?reject=<?= $order['id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                                            <?php else: ?>
                                                <span class="badge <?= $order['status'] == 'Confirmed' ? 'badge-success' : 'badge-danger' ?>">
                                                    <?= $order['status'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
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
