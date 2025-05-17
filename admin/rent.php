<?php
session_start();
include "../config.php";
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_POST['submit'])) {
    $user_id     = $_POST['user_id'];
    $item_id     = $_POST['item_id'];
    $jumlah      = $_POST['jumlah'];
    $start_date  = $_POST['start_date'];
    $end_date    = $_POST['end_date'];
    $status      = 'pending';

    // Hitung periode (dalam hari)
    $start = new DateTime($start_date);
    $end   = new DateTime($end_date);
    $interval = $start->diff($end);
    $periode = $interval->days;

    // Ambil harga barang dari tabel items
    $queryItem = mysqli_query($conn, "SELECT harga FROM items WHERE id = $item_id");
    $itemData = mysqli_fetch_assoc($queryItem);
    $harga = $itemData['harga'];

    // Hitung total harga
    $total_price = $harga * $jumlah * $periode;

    // Insert ke tabel pesanan
    $query = "INSERT INTO pesanan (user_id, item_id, jumlah, status, start_date, end_date, periode, total_price)
              VALUES ('$user_id', '$item_id', '$jumlah', '$status', '$start_date', '$end_date', '$periode', '$total_price')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pesanan berhasil ditambahkan'); window.location.href='riwayat.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

if (isset($_GET['verify_id'])) {
    $verify_id = $_GET['verify_id'];
    mysqli_query($conn, "UPDATE riwayat_pesanan SET status='Verified' WHERE id=$verify_id");
}

// Jika ada parameter verifikasi
if (isset($_GET['verifikasi'])) {
    $id = $_GET['verifikasi'];
    mysqli_query($conn, "UPDATE pembayaran SET status='verified' WHERE id = $id");
    header("Location: rent.php?status=verified");

    exit;
}
$query = mysqli_query($conn, "SELECT rp.*, u.username, i.nama 
    FROM riwayat_pesanan rp
    JOIN users u ON rp.user_id = u.id
    JOIN items i ON rp.item_id = i.id
    WHERE rp.status = 'pending'");


/// Jika ada parameter cancel
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];

    // Cek apakah status masih Pending, jika iya, update jadi Cancelled
    $checkQuery = "SELECT status FROM pesanan WHERE id = $id";
    $resultCheck = mysqli_query($conn, $checkQuery);

    if ($resultCheck && $order = mysqli_fetch_assoc($resultCheck)) {
        if ($order['status'] != 'Cancel') {
            mysqli_query($conn, "UPDATE pesanan SET status='Cancel' WHERE id = $id");
            header("Location: rent.php?status=cancel");

            exit;
        } else {
            echo "<script>alert('Status sudah dibatalkan.');</script>";
        }
    } else {
        echo "<script>alert('Pesanan tidak ditemukan.');</script>";
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
<body class="d-flex flex-column min-vh-100">
   <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
        </div>
         <!-- Logo End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
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
                    <a class="nav-link <?php if($page == 'review') echo 'active'; ?>" href="review.php">Review</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'transaction') echo 'active'; ?>" href="transaction.php">Transaction</a>
                </li>
            </ul>
        </div>
        <!-- Profil -->
        <div class="profile">
    <a href="profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
        <i class="fa fa-user-circle"></i> Profile
    </a>
</div>
    </nav>
    <!-- Navbar End -->


    <!-- Section: Item Rented -->
<div class="container my-5">
  <!-- Judul Section -->
  <div class="mb-4">
    <h2 class="fw-bold text-dark">Daftar Barang</h2>
  </div>

  <!-- Search Bar -->
  <div class="row justify-content-between align-items-center mb-4">
    <div class="col-md-8">
      <form method="GET" class="d-flex gap-2">
        <input type="search" class="form-control" name="search" placeholder="Cari nama item..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit" class="btn btn-outline-primary">Cari</button>
      </form>
    </div>

  <!-- Tabel Data Item -->
<div class="container mt-4">
<div class="table-responsive">
  <table class="table table-bordered table-hover align-middle">
    <thead class="table-primary text-center">
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
    <tbody class="text-center">
      <?php
      $no = 1;
      $query_items = "SELECT * FROM items";
      $result_items = mysqli_query($conn, $query_items);
      $query = " SELECT 
            pesanan.*,
            users.username,
            items.nama AS item_name,
            kategori.nama_kategori AS item_category
        FROM pesanan
        JOIN users ON pesanan.user_id = users.id
        JOIN items ON pesanan.item_id = items.id
        JOIN kategori ON items.id_kategori = kategori.id_kategori
         WHERE items.nama LIKE '%$search%'
        ORDER BY pesanan.id DESC
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "<tr><td colspan='10'>Gagal mengambil data: " . mysqli_error($conn) . "</td></tr>";
    } else {
      while ($order = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($order['username']) . "</td>";
        echo "<td>" . htmlspecialchars($order['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($order['item_category']) . "</td>";
        echo "<td>" . $order['periode'] . " Days</td>";
        echo "<td>Rp " . number_format($order['total_price']) . "</td>";
        echo "<td>" . date('d-m-Y', strtotime($order['start_date'])) . "</td>";
        echo "<td>" . date('d-m-Y', strtotime($order['end_date'])) . "</td>";

        $badgeClass = ($order['status'] == 'verified') ? 'badge-success' : (($order['status'] == 'Cancel') ? 'badge-danger' : 'badge-warning');
        // Status badge dengan style berbeda
        $statusLabel = ucfirst($order['status']); // Kapitalisasi pertama
        switch ($order['status']) {
            case 'verified':
                $badgeClass = 'bg-success text-white'; // Hijau
                break;
            case 'pending':
                $badgeClass = 'bg-warning text-dark'; // Kuning
                break;
            case 'cancelled':
            case 'unavailable':
                $badgeClass = 'bg-danger text-white'; // Merah
                break;
            default:
                $badgeClass = 'bg-secondary text-white'; // Abu
                break;
        }
        echo "<td><span class='badge $badgeClass px-3 py-2'>$statusLabel</span></td>";

        // Tombol aksi
        echo "<td>";
        if ($order['status'] == 'pending') {
            echo "<button onclick='confirmVerification(" . $order['id'] . ")' class='btn btn-sm btn-primary me-1'>Confirm</button>";
            echo "<button onclick='confirmCancel(" . $order['id'] . ")' class='btn btn-sm btn-danger'>Cancel</button>";
        } else {
            echo "<span class='text-muted'>-</span>";
        }
        echo "</td>";
        }
        $no++;
      }
      ?>


<script>
  // Konfirmasi sebelum verifikasi
  function confirmVerification(id) {
    if (confirm("Apakah Anda yakin ingin memverifikasi pesanan ini?")) {
      window.location.href = '?verifikasi=' + id;
    }
  }

  // Konfirmasi sebelum pembatalan
  function confirmCancel(id) {
    if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
      window.location.href = '?cancel=' + id;
    }
  }

  // Notifikasi sukses
  <?php if (isset($_GET['status']) && $_GET['status'] == 'verified'): ?>
    alert("✅ Pesanan berhasil diverifikasi!");
  <?php elseif (isset($_GET['status']) && $_GET['status'] == 'cancelled'): ?>
    alert("❌ Pesanan berhasil dibatalkan!");
  <?php endif; ?>
</script>


    </tbody>
  <table>
</div>
  </div>
</div>