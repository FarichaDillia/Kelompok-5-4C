<?php
session_start();
include "../config.php";
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
    $harga = (int)$_POST["harga"];
    $stok = (int)$_POST["stok"];
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);

    // upload file
    $target_dir = "../img/"; // folder penyimpanan
    $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
    $gambar_name = basename($_FILES["gambar"]["name"]);

    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
        // upload sukses, simpan ke database
        mysqli_query($conn, "INSERT INTO items (nama, harga, stok, deskripsi, gambar) VALUES ('$nama', $harga, $stok, '$deskripsi', '$gambar_name')");
        header("Location: barang.php");
        exit;
    } else {
        echo "<script>alert('Upload gambar gagal!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Rentify Admin</title>
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
        <li class="nav-item active"><a class="nav-link" href="barang.php"><i class="fas fa-box"></i> <span>Kelola Barang</span></a></li>
        <li class="nav-item"><a class="nav-link" href="pesanan.php"><i class="fas fa-check-circle"></i> <span>Verifikasi Pesanan</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <span class="navbar-brand">Tambah Barang</span>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="card shadow mb-4 col-lg-8 mx-auto">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Barang</h6>
                    </div>
                    <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="harga" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Foto</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" required>
                        </div>
                        <div class="text-end">
                            <a href="barang.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        </div>
                    </form>
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
<script src="../lib/js/sb-admin-2.min.js"></script>

</body>
</html>
