<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$whereClause = "";
if ($search !== "") {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $whereClause = " AND items.nama LIKE '%$searchEscaped%'";
}

$rented_items_query = "SELECT pesanan.id, items.nama AS item_name, pesanan.jumlah, pesanan.periode, items.harga 
FROM pesanan 
JOIN items ON pesanan.item_id = items.id 
WHERE pesanan.status='verified' $whereClause
";
$result_pesanan = mysqli_query($conn, $rented_items_query);

if (!$result_pesanan) {
    die("Error executing query: " . mysqli_error($conn));
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

    <!-- Tombol Tambah -->
    <div class="col-md-4 text-end mt-3 mt-md-0">
      <a href="tambahitem.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Barang
      </a>
    </div>
  </div>

 <!-- Tabel Data Item -->
<div class="table-responsive">
  <table class="table table-bordered table-hover align-middle">
    <thead class="table-primary text-center">
      <tr>
        <th>No</th>
        <th>Nama Item</th>
        <th>Deskripsi</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Gambar</th>
        <th>Kode Voucher</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php
      $no = 1;
      $query_items = "SELECT * FROM items";
      $result_items = mysqli_query($conn, $query_items);
      while ($item = mysqli_fetch_assoc($result_items)) {
        echo "<tr>
                <td>{$no}</td>
                <td>" . htmlspecialchars($item['nama']) . "</td>
                <td>" . htmlspecialchars($item['deskripsi']) . "</td>
                <td>Rp " . number_format($item['harga']) . "</td>
                <td>{$item['stok']}</td>
                <td><img src='../img/" . htmlspecialchars($item['gambar']) . "' width='60' alt='item-img'></td>
                <td>{$item['kode_voucher']}</td>
                <td>
                  <span class='badge " . 
                    ($item['status'] == 'Available' ? 'bg-success' : 
                    ($item['status'] == 'Rented' ? 'bg-warning' : 'bg-secondary')) . "'>
                    {$item['status']}
                  </span>
                </td>
                <td>
                  <a href='edititem.php?id={$item['id']}' class='btn btn-sm btn-warning me-1'>
                    <i class='fas fa-edit'></i> Edit
                  </a>
                  <a href='hapusitem.php?id={$item['id']}' onclick='return confirm(\"Yakin hapus?\")' class='btn btn-sm btn-danger'>
                    <i class='fas fa-trash-alt'></i> Delete
                  </a>
                </td>
              </tr>";
        $no++;
      }
      ?>
    </tbody>
  </table>
</div>
</div>
</div>


       <!-- Footer -->
    <footer class="footer text-center py-4">
      <div class="container-fluid">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
      </div>
    </footer>

<!-- Scripts -->
<script src="../lib/vendor/jquery/jquery.min.js"></script>
<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../lib/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../lib/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../lib/js/sb-admin-2.min.js"></script>
<script>
  // Inisialisasi DataTable jika digunakan
  $(document).ready(function () {
    $('#dataTable').DataTable();
  });

  // Script AJAX untuk pencarian tanpa reload
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');

    if (form && input && tableBody) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const keyword = input.value.trim();

        fetch(`?search=${encodeURIComponent(keyword)}`)
          .then(response => response.text())
          .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.getElementById('tableBody');

            if (newTableBody) {
              tableBody.innerHTML = newTableBody.innerHTML;
            }
          });
      });
    }
  });
</script>

</body>
</html>