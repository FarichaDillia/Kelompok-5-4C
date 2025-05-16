<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// Ambil ID item yang ingin diedit
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data item berdasarkan ID
    $query = "SELECT * FROM items WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $item = mysqli_fetch_assoc($result);

    // Jika item tidak ditemukan
    if (!$item) {
        echo "<script>alert('Item tidak ditemukan.'); window.location.href='item.php';</script>";
        exit;
    }
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kode_voucher = $_POST['kode_voucher']; // Ambil kode voucher dari form
    $stok = $_POST['stok'];
    
    // Cek apakah ada file gambar yang dikirim
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Cek apakah ada gambar yang diupload
        if ($_FILES['gambar']['name']) {
            $gambar = $_FILES['gambar']['name'];
            $gambar_tmp = $_FILES['gambar']['tmp_name'];
            $folder = "../img/";

            // Pindahkan file gambar ke folder tujuan
            if (move_uploaded_file($gambar_tmp, $folder . $gambar)) {
                // Berhasil upload gambar
            } else {
                echo "<script>alert('Gagal mengupload gambar.');</script>";
                exit;
            }
        }
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama atau default
        $gambar = isset($item['gambar']) ? $item['gambar'] : ''; 
    }


    // Update data item di database
    $query = "UPDATE items SET 
                nama = '$nama',
                kategori = '$kategori',
                deskripsi = '$deskripsi',
                harga = '$harga',
                kode_voucher = '$kode_voucher',
                stok = '$stok',
                gambar = '$gambar'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Item berhasil diupdate!'); window.location.href='item.php';</script>";
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

            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                        <span class="navbar-brand">Edit Barang</span>
                    </nav>

                    <div class="container-fluid">
                        <div class="card shadow mb-4 col-lg-8 mx-auto">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Form Edit Barang</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($item['nama']) ?>" required>
                                    </div>
                                     <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($item['kategori']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Harga</label>
                                        <input type="number" name="harga" class="form-control" value="<?= $item['harga'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="stok" class="form-control" value="<?= $item['stok'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($item['deskripsi']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kode Voucher</label>
                                        <input type="text" name="kode_voucher" class="form-control" value="<?= htmlspecialchars($item['kode_voucher']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nama File Gambar</label>
                                        <input type="text" name="gambar" class="form-control" value="<?= htmlspecialchars($item['gambar']) ?>" required>
                                    </div>
                                    <div class="text-end">
                                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                                        <a href="dashboard.php" class="btn btn-secondary">Update</a>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

        <script src="../lib/vendor/jquery/jquery.min.js"></script>
        <script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="../lib/js/sb-admin-2.min.js"></script>

    </div>
</div>

</body>
</html>
