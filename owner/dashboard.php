<?php
session_start();
include "../config.php";

$active_page = "home";

// ==== PERBAIKAN: Hapus kategori dulu sebelum query SELECT dijalankan ====
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $query_delete = "DELETE FROM kategori WHERE id_kategori = $delete_id";
    if (mysqli_query($conn, $query_delete)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'dashboard.php';
                });
            });
        </script>";
        exit;
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menghapus kategori.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'dashboard.php';
                });
            });
        </script>";
        exit;
    }
}
// ==== END PERBAIKAN ====

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = mysqli_real_escape_string($conn, $search);

$query_kategori = "SELECT * FROM kategori WHERE 1";
if (!empty($search)) {
    $query_kategori .= " AND nama_kategori LIKE '%$search%'";
}
$query_kategori .= " ORDER BY id_kategori";
$result_kategori = mysqli_query($conn, $query_kategori);

$owner_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='owner'");
$owner = mysqli_fetch_assoc($owner_result)['total'] ?? 0;

$item_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM items");
$total_item = mysqli_fetch_assoc($item_result)['total'] ?? 0;

$kategori_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori");
$total_kategori = mysqli_fetch_assoc($kategori_result)['total'] ?? 0;
?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Owner - Rentify</title>
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
    <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    User
  </a>
  <ul class="dropdown-menu" aria-labelledby="navbarDropdownUser">
    <li><a class="dropdown-item" href="owner.php">Owner</a></li>
    <li><a class="dropdown-item" href="renter.php">Renter</a></li>
  </ul>
</li>

      <li class="nav-item"><a class="nav-link" href="management.php">Management</a></li>
    </ul>
  </div>
  <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
      <i class="fa fa-user-circle"></i> Profile
    </a>
  </div>
</nav>

<div class="container mt-5 mb-4">
  <p style="font-weight:700; color:#0f1a3c; margin-bottom: 0; font-size: 25px;">Welcome, Admin !</p>
  <h2 style="font-weight:800; color:#0f1a3c; margin-top: 0; letter-spacing: 1px;">DASHBOARD</h2>
  <p class="text-muted">Here is your overview summary</p>
</div>

<!-- Summary Cards -->
<div class="container mb-5">
  <div class="row g-4 justify-content-center">
    <div class="col-lg-4 col-md-6">
      <div class="bg-white rounded shadow-sm p-4 border-start border-4 border-primary">
        <div class="text-uppercase small fw-bold text-primary mb-2">Total Kategori</div>
        <div class="h5 fw-bold text-dark"><?= number_format($total_kategori, 0, ',', '.') ?></div>
        <i class="fas fa-tags fa-2x text-secondary position-absolute bottom-0 end-0 m-3"></i>
      </div>
    </div>
    <div class="col-lg-4 col-md-6">
      <div class="bg-white rounded shadow-sm p-4 border-start border-4 border-success">
        <div class="text-uppercase small fw-bold text-success mb-2">Total Owner</div>
        <div class="h5 fw-bold text-dark"><?= $owner ?></div>
        <i class="fas fa-users fa-2x text-secondary position-absolute bottom-0 end-0 m-3"></i>
      </div>
    </div>
    <div class="col-lg-4 col-md-6">
      <div class="bg-white rounded shadow-sm p-4 border-start border-4 border-info">
        <div class="text-uppercase small fw-bold text-info mb-2">Total Item</div>
        <div class="h5 fw-bold text-dark"><?= $total_item ?></div>
        <i class="fas fa-box fa-2x text-secondary position-absolute bottom-0 end-0 m-3"></i>
      </div>
    </div>
  </div>
</div>

<!-- Kategori Section -->
<div class="container my-5">
  <h2 class="fw-bold text-dark mb-4">Daftar Kategori</h2>
  <div class="row justify-content-between align-items-center mb-4">
    <div class="col-md-8">
      <form method="GET" class="d-flex gap-2">
  <input type="search" name="search" class="form-control" placeholder="Cari nama kategori..." value="<?= htmlspecialchars($search) ?>">
  <button type="submit" class="btn btn-outline-primary">Cari</button>
</form>

    </div>
    <div class="col-md-4 text-end">
      <a href="addcategory.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kategori
      </a>
    </div>
  </div>

  <div class="table-responsive">
   <table class="table table-bordered table-hover align-middle">
    <thead class="table-primary text-center">
        <tr>
          <th>Id Kategori</th>
          <th>Nama Kategori</th>
          <th>Daily Rate</th>
          <th>Action</th>
        </tr>
      </thead>
     <tbody>
<?php
$no = 1;
while ($kategori = mysqli_fetch_assoc($result_kategori)) {
    echo "<tr>
            <td>{$kategori['id_kategori']}</td>
            <td>" . htmlspecialchars($kategori['nama_kategori']) . "</td>
            <td>Rp. " . number_format($kategori['daily_rate'], 0, ',', '.') . "</td>
            <td>
                <a href='edit.php?id={$kategori['id_kategori']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Edit</a>
               <a href='#' onclick='confirmDelete({$kategori['id_kategori']})' class='btn btn-danger btn-sm'><i class='fas fa-trash-alt'></i> Delete</a>
            </td>
          </tr>";
    $no++;
}
?>
</tbody>

    </table>
  </div>
</div>


<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>



    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            const tableRows = document.querySelectorAll(".category-table tbody tr");

            // Fungsi filter baris
            function filterRows(term) {
                tableRows.forEach(row => {
                    const cells = row.querySelectorAll("td");
                    let match = false;

                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(term)) {
                            match = true;
                        }
                    });

                    row.style.display = match ? "" : "none";
                });
            }

            // Event saat mengetik
            searchInput.addEventListener("input", function () {
                const searchTerm = searchInput.value.toLowerCase();
                filterRows(searchTerm);
            });

            // Fungsi untuk tombol Search
            window.filterTable = function () {
                const searchTerm = searchInput.value.toLowerCase();
                filterRows(searchTerm);
            };
        });
    </script>
    <script>
function confirmDelete(id) {
  Swal.fire({
    title: 'Hapus Kategori?',
    text: "Data yang dihapus tidak bisa dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
     window.location.href = 'dashboard.php?delete_id=' + id;
    }
  });
}
</script>

    <!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>