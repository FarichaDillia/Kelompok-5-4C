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
    <title>Kelola Barang - Rentify Admin</title>
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

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <span class="navbar-brand">Kelola Barang</span>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Deskripsi</th>
                                        <th>Gambar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td><?= htmlspecialchars($item['nama']) ?></td>
                                        <td>Rp <?= number_format($item['harga']) ?></td>
                                        <td><?= $item['stok'] ?></td>
                                        <td><?= htmlspecialchars($item['deskripsi']) ?></td>
                                        <td><img src="../img/<?= $item['gambar'] ?>" width="60"></td>
                                        <td>
                                            <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="hapus.php?id=<?= $item['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="tambah.php" class="btn btn-primary mt-3"><i class="fas fa-plus"></i> Tambah Barang</a>
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
