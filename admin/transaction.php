<?php
session_start();
include "../config.php";

// Validasi hanya untuk owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit;
}

$owner_id = $_SESSION['user_id']; // Ambil ID owner
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Query pembayaran hanya untuk item milik owner
$query = "SELECT p.*, u.username
          FROM pembayaran p
          JOIN riwayat_pesanan rp ON p.pesanan_id = rp.id
          JOIN items i ON rp.item_id = i.id
          JOIN users u ON p.user_id = u.id
          WHERE i.owner_id = $owner_id";

if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (u.username LIKE '%$searchEscaped%' 
                     OR p.atas_nama LIKE '%$searchEscaped%' 
                     OR p.metode LIKE '%$searchEscaped%')";
}

$query .= " ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Hapus transaksi
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Verifikasi bahwa transaksi milik owner
    $check = mysqli_query($conn, "
        SELECT p.id 
        FROM pembayaran p
        JOIN riwayat_pesanan rp ON p.pesanan_id = rp.id
        JOIN items i ON rp.item_id = i.id
        WHERE p.id = $id AND i.owner_id = $owner_id
    ");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM pembayaran WHERE id = $id");
        header("Location: transaction.php?deleted=1");
        exit;
    } else {
        echo "<script>alert('Akses ditolak. Transaksi bukan milik Anda.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaksi - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
  </div>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
      <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
      <li class="nav-item"><a class="nav-link" href="review.php">Review</a></li>
      <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
    </ul>
  </div>
  <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
      <i class="fa fa-user-circle"></i> Profile
    </a>
  </div>
</nav>

<main class="container my-5 flex-grow-1">
  <div class="mb-4">
    <h2 class="fw-bold text-dark">Transaksi Pembayaran</h2>
  </div>

  <div class="row justify-content-between align-items-center mb-4">
    <div class="col-md-8">
      <form method="GET" class="d-flex gap-2">
        <input type="search" class="form-control" name="search" placeholder="Cari user, metode, atau nama..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit" class="btn btn-outline-primary">Cari</button>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-primary">
        <tr>
          <th>No</th>
          <th>No. Order</th>
          <th>Metode</th>
          <th>Rekening</th>
          <th>Atas Nama</th>
          <th>Tanggal Bayar</th>
          <th>Total Bayar</th>
          <th>Username</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td>OR<?= str_pad($row['pesanan_id'], 3, '0', STR_PAD_LEFT) ?></td>
          <td><?= ucwords(str_replace('_', ' ', $row['metode'])) ?></td>
          <td><?= htmlspecialchars($row['rekening']) ?></td>
          <td><?= htmlspecialchars($row['atas_nama']) ?></td>
          <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
          <td>Rp. <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-sm btn-danger">Hapus</button></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>
<script>
function confirmDelete(id) {
  Swal.fire({
    title: 'Hapus Transaksi?',
    text: "Tindakan ini tidak dapat dibatalkan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = '?delete=' + id;
    }
  });
}
</script>
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Transaksi berhasil dihapus.',
    timer: 2000,
    showConfirmButton: false
  });
});
</script>
<?php endif; ?>


</body>
</html>
