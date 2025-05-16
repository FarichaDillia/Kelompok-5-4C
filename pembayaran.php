<?php
session_start();
include "config.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek jika ada pesanan
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang kosong!'); window.location='index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form pembayaran
    $user_id = $_SESSION['user_id'];
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $rekening = mysqli_real_escape_string($conn, $_POST['rekening']);
    $atas_nama = mysqli_real_escape_string($conn, $_POST['atas_nama']);
    $tanggal_bayar = $_POST['tanggal_bayar'];
    
    // Inisialisasi diskon jika ada kode voucher
    $diskon = 0;
    $kode_voucher = mysqli_real_escape_string($conn, $_POST['kode_voucher']);
    if ($kode_voucher == "DISKON20") {
        $diskon = 0.2; // 20% diskon
    }

    // Hitung total harga pesanan
    $totalHarga = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalHarga += $item['harga'] * $item['qty'];
    }

    // Terapkan diskon
    $totalHargaSetelahDiskon = $totalHarga - ($totalHarga * $diskon);

    // Simpan pesanan ke database
    $query = "INSERT INTO riwayat_pesanan (user_id, item_id, jumlah, total_harga, status) VALUES ";
    $values = [];
    foreach ($_SESSION['cart'] as $item) {
        $values[] = "($user_id, {$item['id']}, {$item['qty']}, {$item['harga']} * {$item['qty']}, 'pending')";
    }
    $query .= implode(", ", $values);

    if (mysqli_query($conn, $query)) {
        // Simpan pembayaran setelah pesanan
        $pesanan_id = mysqli_insert_id($conn); // Mendapatkan ID pesanan terbaru
        $queryPembayaran = "INSERT INTO pembayaran (pesanan_id, metode, rekening, atas_nama, tanggal_bayar, total_bayar) 
                            VALUES ('$pesanan_id', '$metode', '$rekening', '$atas_nama', '$tanggal_bayar', '$totalHargaSetelahDiskon')";
        if (mysqli_query($conn, $queryPembayaran)) {
            unset($_SESSION['cart']); // Kosongkan keranjang setelah pembayaran berhasil
            echo "<script>alert('Pembayaran berhasil, pesanan sedang diproses!'); window.location='riwayat.php';</script>";
        } else {
            echo "<script>alert('Gagal memproses pembayaran. Coba lagi!'); window.location='pembayaran.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal memproses pesanan. Coba lagi!'); window.location='pembayaran.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Form Pembayaran</h2>
        
        <form method="POST" class="mx-auto" style="max-width: 600px;">
            <!-- Input Metode Pembayaran -->
            <div class="mb-3">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode" class="form-control" required>
                    <option value="transfer_bank">Transfer Bank</option>
                    <option value="gopay">GoPay</option>
                    <option value="ovo">OVO</option>
                </select>
            </div>
            <!-- Input Nomor Rekening -->
            <div class="mb-3">
                <label class="form-label">Nomor Rekening</label>
                <input type="text" name="rekening" class="form-control" required>
            </div>
            <!-- Input Nama Pemilik Rekening -->
            <div class="mb-3">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input type="text" name="atas_nama" class="form-control" required>
            </div>
            <!-- Input Tanggal Pembayaran -->
            <div class="mb-3">
                <label class="form-label">Tanggal Pembayaran</label>
                <input type="date" name="tanggal_bayar" class="form-control" required>
            </div>
            <!-- Input Kode Voucher -->
            <div class="mb-3">
                <label class="form-label">Kode Voucher (Jika Ada)</label>
                <input type="text" name="kode_voucher" class="form-control">
            </div>
            
            <!-- Tombol Submit -->
            <div class="text-center">
                <button type="submit" class="btn btn-success px-5 py-2">Konfirmasi Pembayaran</button>
            </div>
        </form>
    </div>
</body>
</html>
