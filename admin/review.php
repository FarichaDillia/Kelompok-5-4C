<?php
session_start();
include "../config.php";

// Validasi akses hanya untuk owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit;
}

$owner_id = $_SESSION["user_id"]; // Ambil ID owner dari sesi
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Query hanya untuk item milik owner yang sedang login
$query = "SELECT r.id_review, r.id_rent, r.rating, r.comment, u.username, i.nama AS item_name, rp.total_harga, rp.start_date, rp.end_date
          FROM review r
          JOIN users u ON r.id_user = u.id
          JOIN items i ON r.id_item = i.id
          JOIN riwayat_pesanan rp ON r.id_rent = rp.id
          WHERE i.owner_id = $owner_id";

if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (i.nama LIKE '%$searchEscaped%' OR u.username LIKE '%$searchEscaped%' OR r.comment LIKE '%$searchEscaped%')";
}

$query .= " ORDER BY r.id_review DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Hapus review (pastikan validasi tambahan jika dibutuhkan)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Validasi: review tersebut harus milik item owner
    $check = mysqli_query($conn, "
        SELECT r.id_review
        FROM review r
        JOIN items i ON r.id_item = i.id
        WHERE r.id_review = $id AND i.owner_id = $owner_id
    ");
    
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM review WHERE id_review = $id");
        header("Location: review.php");
        exit;
    } else {
        header("Location: review.php?status=denied");
exit;
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
    <h2 class="fw-bold text-dark">Review Penyewaan</h2>
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
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-primary">
        <tr>
          <th>No</th>
          <th>No. Order</th>
          <th>Username</th>
          <th>Item</th>
          <th>Total Harga</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $start = strtotime($row['start_date']);
            $end = strtotime($row['end_date']);
            $stars = str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']);
        ?>
        <tr>
          <td><?= $no++ ?></td>
         <td>OR<?= str_pad($row['id_rent'], 3, '0', STR_PAD_LEFT) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['item_name']) ?></td>
          <td>Rp. <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
          <td class="text-warning fw-bold"><?= $stars ?></td>
          <td><?= htmlspecialchars($row['comment']) ?></td>
          <td><a href="?delete=<?= $row['id_review'] ?>" class="btn btn-sm btn-danger">Delete</a></td>

        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</main>

<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>

<?php if (isset($_GET['status']) && $_GET['status'] === 'denied'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Akses Ditolak',
    text: 'Review bukan milik Anda!',
    confirmButtonText: 'OK'
  });
</script>
<?php endif; ?>


</body>
</html>