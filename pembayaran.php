<?php
session_start();
include "config.php";

// 1. Gunakan prepared statement (lebih aman dari SQL injection)
$stmt = $conn->prepare("INSERT INTO pembayaran (pesanan_id, metode, rekening, atas_nama, tanggal_bayar, total_bayar) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssd", $pesanan_id, $metode, $rekening, $atas_nama, $tanggal_bayar, $total_bayar);
$stmt->execute();

// 2. Tambahkan update ke pesanan
mysqli_query($conn, "UPDATE pesanan SET status = 'pending' WHERE id = $pesanan_id");


// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek jika ada pesanan
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang kosong!'); window.location='navbar.php';</script>";
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
    // Ambil data terakhir yang baru saja dimasukkan ke riwayat_pesanan
$last_id = mysqli_insert_id($conn);
$get = mysqli_query($conn, "SELECT * FROM riwayat_pesanan WHERE id = $last_id");
$data = mysqli_fetch_assoc($get);

// Ambil data dari form
$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];
$jumlah = $_POST['jumlah'];
$total_price = $_POST['total_price'];

// Masukkan pesanan ke tabel 'pesanan'
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime($start_date. ' +1 day'));
$periode = 1;

// Setelah pembayaran berhasil
$insertRiwayat = mysqli_query($conn, "INSERT INTO riwayat_pesanan (user_id, item_id, jumlah, total_harga, status)
    SELECT p.user_id, p.item_id, p.jumlah, p.total_price, 'pending'
    FROM pesanan p
    WHERE p.id = $pesanan_id
");



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
    <style>
        .payment-container {
    background-color: #fff;
    border-radius: 15px;
    padding: 40px;
    max-width: 600px;
    margin: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }

  h2 {
    color: #333;
    font-weight: bold;
    margin-bottom: 30px;
  }

  .form-label {
    font-weight: 600;
    color: #333;
  }

  .form-control {
    border-radius: 10px;
    padding: 12px;
    font-size: 15px;
  }

  .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(43, 58, 103, 0.25);
    border-color: #2b3a67;
  }

  .btn-success {
    background-color: #2b3a67;
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
  }

  .btn-success:hover {
    background-color: #1f2a4d;
  }
    </style>
</head>
<body>
  <div class="container py-5">
    <div class="payment-container">
      <h2 class="text-center">Form Pembayaran</h2>
      
      <form method="POST">
          <!-- Metode Pembayaran -->
          <div class="mb-3">
              <label class="form-label">Metode Pembayaran</label>
              <select name="metode" class="form-control" required>
                  <option value="transfer_bank">Transfer Bank</option>
                  <option value="gopay">GoPay</option>
                  <option value="ovo">OVO</option>
              </select>
          </div>
          
          <!-- Nomor Rekening -->
          <div class="mb-3">
              <label class="form-label">Nomor Rekening</label>
              <input type="text" name="rekening" class="form-control" required>
          </div>

          <!-- Nama Pemilik Rekening -->
          <div class="mb-3">
              <label class="form-label">Nama Pemilik Rekening</label>
              <input type="text" name="atas_nama" class="form-control" required>
          </div>

          <!-- Tanggal Pembayaran -->
          <div class="mb-3">
              <label class="form-label">Tanggal Pembayaran</label>
              <input type="date" name="tanggal_bayar" class="form-control" required>
          </div>

          <!-- Kode Voucher -->
          <div class="mb-4">
              <label class="form-label">Kode Voucher (Jika Ada)</label>
              <input type="text" name="kode_voucher" class="form-control">
          </div>

          <!-- Tombol Submit -->
          <div class="text-center">
              <button type="submit" class="btn btn-success">Konfirmasi Pembayaran</button>
          </div>
      </form>
    </div>
  </div>
</body>

</html>
