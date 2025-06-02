<?php
session_start();
include "config.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validasi session checkout
if (empty($_SESSION['checkout_data']) || empty($_SESSION['checkout_items'])) {
    $alertType = 'missing';
} else {
    $alertType = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id       = $_SESSION['user_id'];
    $metode        = mysqli_real_escape_string($conn, $_POST['metode']);
    $rekening      = mysqli_real_escape_string($conn, $_POST['rekening']);
    $atas_nama     = mysqli_real_escape_string($conn, $_POST['atas_nama']);
    $tanggal_bayar = mysqli_real_escape_string($conn, $_POST['tanggal_bayar']);
    $kode_voucher  = mysqli_real_escape_string($conn, $_POST['kode_voucher']);

    if (strtotime($tanggal_bayar) > time()) {
        $alertType = 'tanggal_invalid';
    } else {
        $start_date = mysqli_real_escape_string($conn, $_SESSION['checkout_data']['start_date']);
        $end_date   = mysqli_real_escape_string($conn, $_SESSION['checkout_data']['end_date']);
        $durasi     = $_SESSION['checkout_data']['durasi'];
        $items      = $_SESSION['checkout_items'];
        $status     = 'pending';

        $diskon = ($kode_voucher === "131204") ? 0.2 : 0;

        foreach ($items as $item) {
            $item_id = (int) $item['id'];

            // Cek stok
            $cekStok = mysqli_query($conn, "SELECT stok FROM items WHERE id = $item_id");
            $stokData = mysqli_fetch_assoc($cekStok);
            if ($stokData['stok'] <= 0) {
                $alertType = 'stok_habis';
                break;
            }

            // Ambil daily_rate
            $getKategori = mysqli_query($conn, "SELECT k.daily_rate FROM items i JOIN kategori k ON i.id_kategori = k.id_kategori WHERE i.id = $item_id");
            $kategori = mysqli_fetch_assoc($getKategori);
            $daily_rate = $kategori['daily_rate'] ?? 0;

            $total_item = $daily_rate * $durasi;
            $total_diskon = $total_item - ($total_item * $diskon);
            $total = (float)$total_diskon;

            $queryPesanan = "INSERT INTO riwayat_pesanan 
                (user_id, item_id, total_harga, status, start_date, end_date) 
                VALUES ('$user_id', '$item_id', '$total', '$status', '$start_date', '$end_date')";
            $insertPesanan = mysqli_query($conn, $queryPesanan);
            if (!$insertPesanan) die("Gagal menyimpan pesanan: " . mysqli_error($conn));

            $pesanan_id = mysqli_insert_id($conn);

            $queryBayar = "INSERT INTO pembayaran 
                (pesanan_id, metode, rekening, atas_nama, tanggal_bayar, total_bayar, user_id) 
                VALUES ('$pesanan_id', '$metode', '$rekening', '$atas_nama', '$tanggal_bayar', '$total', '$user_id')";
            $insertBayar = mysqli_query($conn, $queryBayar);
            if (!$insertBayar) die("Gagal menyimpan pembayaran: " . mysqli_error($conn));

            $updateStok = mysqli_query($conn, "UPDATE items SET stok = stok - 1 WHERE id = $item_id");
            if (!$updateStok) die("Gagal update stok: " . mysqli_error($conn));
        }

        if (!$alertType) {
            unset($_SESSION['cart'], $_SESSION['checkout_data'], $_SESSION['checkout_items']);
            $alertType = 'success';
        }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <div class="mb-3">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode" class="form-control" required>
                    <option value="transfer_bank">Transfer Bank</option>
                    <option value="gopay">GoPay</option>
                    <option value="ovo">OVO</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nomor Rekening</label>
                <input type="text" name="rekening" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input type="text" name="atas_nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Pembayaran</label>
                <input type="date" name="tanggal_bayar" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Kode Voucher (Jika Ada)</label>
                <input type="text" name="kode_voucher" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success">Konfirmasi Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($alertType)): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    <?php if ($alertType === 'missing'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Data Tidak Ditemukan',
            text: 'Data checkout tidak tersedia!',
        }).then(() => {
            window.location = 'rent.php';
        });
    <?php elseif ($alertType === 'tanggal_invalid'): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Tanggal Tidak Valid',
            text: 'Tanggal pembayaran tidak boleh di masa depan.',
        }).then(() => {
            window.history.back();
        });
    <?php elseif ($alertType === 'stok_habis'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Stok Habis',
            text: 'Stok salah satu item habis.',
        }).then(() => {
            window.location = 'rent.php';
        });
    <?php elseif ($alertType === 'success'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Pembayaran Berhasil',
            text: 'Pesanan Anda sedang diproses!',
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            window.location = 'riwayat.php';
        });
    <?php endif; ?>
});
</script>
<?php endif; ?>
</body>
</html>
