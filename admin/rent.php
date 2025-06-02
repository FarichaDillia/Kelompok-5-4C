<?php
session_start();
include "../config.php";

// Cek apakah user sudah login dan sebagai owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit;
}

$owner_id = $_SESSION['user_id']; // Ambil ID owner
$search = '';
$query = "SELECT rp.id, rp.item_id, rp.user_id, rp.total_harga, rp.status, rp.start_date, rp.end_date,
                 i.nama AS item_name, u.username AS user_name
          FROM riwayat_pesanan rp
          JOIN items i ON rp.item_id = i.id
          JOIN users u ON rp.user_id = u.id
          WHERE i.owner_id = $owner_id";

// Filter pencarian nama item
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    if ($search !== '') {
        $searchEscaped = mysqli_real_escape_string($conn, $search);
        $query .= " AND i.nama LIKE '%$searchEscaped%'";
    }
}

$query .= " ORDER BY rp.start_date DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Verifikasi pesanan
if (isset($_GET['verifikasi'])) {
    $id = intval($_GET['verifikasi']);

    // Ambil start_date dan end_date dari database
    $resultTanggal = mysqli_query($conn, "SELECT start_date, end_date FROM riwayat_pesanan WHERE id = $id");
    $tanggal = mysqli_fetch_assoc($resultTanggal);

    if ($tanggal && $tanggal['start_date'] && $tanggal['end_date']) {
        // Jika tanggal sudah ada, cukup ubah status
        $update = "UPDATE riwayat_pesanan 
                   SET status='verified' 
                   WHERE id = $id";
    } else {
        echo "<script>alert('Tanggal sewa belum diisi oleh user.'); window.location='rent.php';</script>";
        exit;
    }

    if (mysqli_query($conn, $update)) {
        header("Location: rent.php?status=verified");
        exit;
    } else {
        echo "<script>alert('Gagal memverifikasi: " . mysqli_error($conn) . "');</script>";
    }
}


// Cancel pesanan
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $checkQuery = "SELECT status FROM riwayat_pesanan WHERE id = $id";
    $resultCheck = mysqli_query($conn, $checkQuery);

    if ($resultCheck && $order = mysqli_fetch_assoc($resultCheck)) {
        if ($order['status'] != 'cancelled') {
            mysqli_query($conn, "UPDATE riwayat_pesanan SET status='cancelled' WHERE id = $id");
            header("Location: rent.php?status=cancel");
            exit;
        } else {
            $_SESSION['alert'] = 'Status sudah dibatalkan.';
            header("Location: rent.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rent Management - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link rel="stylesheet" href="style.css">
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
    <h2 class="fw-bold text-dark">Riwayat Penyewaan</h2>
  </div>

  <div class="row justify-content-between align-items-center mb-4">
    <div class="col-md-8">
      <form method="GET" class="d-flex gap-2">
        <input type="search" class="form-control" name="search" placeholder="Cari nama item..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit" class="btn btn-outline-primary">Cari</button>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>No</th>
          <th>Username</th>
          <th>Item</th>
          <th>Total Harga</th>
          <th>Status</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Durasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $start = strtotime($row['start_date']);
            $end = strtotime($row['end_date']);
            $periode = ($start && $end) ? (ceil(($end - $start) / (60 * 60 * 24)) + 1) : '-';
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['user_name']) ?></td>
          <td><?= htmlspecialchars($row['item_name']) ?></td>
          <td>Rp. <?= number_format($row['total_harga']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= date('d M Y', strtotime($row['start_date'])) ?></td>
          <td><?= date('d M Y', strtotime($row['end_date'])) ?></td>
          <td><?= is_numeric($periode) ? $periode . ' hari' : '-' ?></td>
          <td>
            <button onclick="confirmVerification(<?= $row['id'] ?>)" class="btn btn-sm btn-primary me-1">Confirm</button>
            <button onclick="confirmCancel(<?= $row['id'] ?>)" class="btn btn-sm btn-danger">Cancel</button>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</main>

<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>
<script>
function confirmVerification(id) {
  Swal.fire({
    title: 'Verifikasi Pesanan?',
    text: "Apakah Anda yakin ingin memverifikasi pesanan ini?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, Verifikasi',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = '?verifikasi=' + id;
    }
  });
}

function confirmCancel(id) {
  Swal.fire({
    title: 'Batalkan Pesanan?',
    text: "Apakah Anda yakin ingin membatalkan pesanan ini?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = '?cancel=' + id;
    }
  });
}
</script>
<?php if (isset($_GET['status']) && $_GET['status'] == 'verified'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Pesanan berhasil diverifikasi!',
    timer: 2000,
    showConfirmButton: false
  });
});
</script>
<?php elseif (isset($_GET['status']) && ($_GET['status'] == 'cancelled' || $_GET['status'] == 'cancel')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  Swal.fire({
    icon: 'info',
    title: 'Dibatalkan',
    text: 'Pesanan berhasil dibatalkan.',
    timer: 2000,
    showConfirmButton: false
  });
});
</script>
<?php endif; ?>
<?php if (isset($_SESSION['alert'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  Swal.fire({
    icon: 'info',
    title: 'Perhatian',
    text: '<?= addslashes($_SESSION['alert']) ?>',
    confirmButtonText: 'OK'
  });
});
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>




</body>
</html>
