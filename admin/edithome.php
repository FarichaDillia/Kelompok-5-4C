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
    <title>Edit Pesanan - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Pesanan</h2>

    <form method="POST">
        <div class="mb-3">
            <label for="item_name" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="item_name" value="<?= htmlspecialchars($pesanan['item_name']) ?>" disabled>
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
