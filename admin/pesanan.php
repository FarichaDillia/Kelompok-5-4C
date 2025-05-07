<?php
session_start();
include "../config.php";
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Query data pesanan
$result = mysqli_query($conn, "
    SELECT pesanan.id, users.username, items.nama, pesanan.jumlah, pesanan.status 
    FROM pesanan 
    JOIN users ON pesanan.user_id = users.id 
    JOIN items ON pesanan.item_id = items.id
");

// Jika ada parameter verifikasi
if (isset($_GET['verifikasi'])) {
    $id = $_GET['verifikasi'];
    mysqli_query($conn, "UPDATE pesanan SET status='verified' WHERE id = $id");
    header("Location: pesanan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pesanan - Rentify Admin</title>
    <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../lib/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
        <li class="nav-item active"><a class="nav-link" href="#"><i class="fas fa-check-circle"></i> <span>Verifikasi Pesanan</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <span class="navbar-brand">Verifikasi Pesanan</span>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Pesanan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Username</th>
                                        <th>Item</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($p = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['username']) ?></td>
                                        <td><?= htmlspecialchars($p['nama']) ?></td>
                                        <td><?= $p['jumlah'] ?></td>
                                        <td>
                                            <?php if ($p['status'] == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($p['status'] == 'pending'): ?>
                                                <a href="?verifikasi=<?= $p['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Verifikasi pesanan ini?')"><i class="fas fa-check"></i> Verifikasi</a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-check-double"></i> Verified</button>
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
