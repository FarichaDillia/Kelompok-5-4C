<?php
session_start();
include "../config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$hargaKategori = '';
$id_kategori = $_POST['id_kategori'] ?? '';

// Ambil data kategori untuk dropdown
$queryCategories = "SELECT * FROM kategori";
$resultCategories = mysqli_query($conn, $queryCategories);

// Ambil harga kategori jika tersedia
if (!empty($id_kategori)) {
    $queryHarga = "SELECT daily_rate FROM kategori WHERE id_kategori = $id_kategori";
    $resultHarga = mysqli_query($conn, $queryHarga);
    if ($rowHarga = mysqli_fetch_assoc($resultHarga)) {
        $hargaKategori = $rowHarga['daily_rate'];
    }
}

// Jika tombol submit ditekan
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kode_voucher = $_POST['kode_voucher'];
    $stok = $_POST['stok'];
    $gambar = $_FILES['gambar']['name'];
    $status = $_POST['status'] ?? 'Available';
    $id_kategori = $_POST['id_kategori'];

    // Upload gambar
    $target_dir = "../img/";
    $target_file = $target_dir . basename($gambar);
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);

    // Insert ke database
    $query = "INSERT INTO items (nama, deskripsi, harga, kode_voucher, stok, gambar, status, id_kategori, owner_id)
              VALUES ('$nama', '$deskripsi', '$harga', '$kode_voucher', '$stok', '$gambar', '$status', '$id_kategori', '$owner_id')";

$insertSuccess = false;

    if (mysqli_query($conn, $query)) {
    $insertSuccess = true;
} else {
    echo "Error: " . mysqli_error($conn);
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
    <style>
    body {
      background-color: #77acc7;
    }
    .form-container {
      background-color: #fff;
      border-radius: 15px;
      padding: 40px;
      max-width: 700px;
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
    .form-control, .form-select {
      border-radius: 10px;
      padding: 12px;
      font-size: 15px;
    }
    .form-control:focus, .form-select:focus {
      box-shadow: 0 0 0 0.2rem rgba(43, 58, 103, 0.25);
      border-color: #2b3a67;
    }
    .btn-primary {
      background-color: #2b3a67;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #1f2a4d;
    }
  </style>
</head>
<body id="page-top">
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Navbar Start -->
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
    <!-- Navbar End -->
<div class="container py-5">
  <div class="form-container">
    <h2 class="text-center">Form Tambah Barang</h2>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="id_kategori" id="id_kategori" class="form-select" required>

          <option value="">-- Pilih Kategori --</option>
          <?php
          $selectedKategori = $_POST['id_kategori'] ?? '';
          while ($cat = mysqli_fetch_assoc($resultCategories)) {
              $selected = ($selectedKategori == $cat['id_kategori']) ? 'selected' : '';
              echo "<option value='{$cat['id_kategori']}' $selected>" . htmlspecialchars($cat['nama_kategori']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Harga</label>
        <input type="text" name="harga" id="harga" class="form-control bg-light" readonly required value="<?= htmlspecialchars($hargaKategori) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Kode Voucher</label>
        <input type="text" name="kode_voucher" class="form-control" required minlength="5" maxlength="10" pattern="\d+" title="Hanya angka yang diperbolehkan dan minimal 5 digit" placeholder="Masukkan kode voucher">
      </div>

      <div class="mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" required>
      </div>

      <div class="mb-4">
        <label class="form-label">Upload Foto</label>
        <input type="file" name="gambar" class="form-control" accept="image/*" required>
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>



<!-- Scripts -->
<script src="../lib/vendor/jquery/jquery.min.js"></script>
<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../lib/js/sb-admin-2.min.js"></script>

<script>
document.getElementById('id_kategori').addEventListener('change', function () {
    const kategoriId = this.value;

    if (kategoriId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "get_price.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("harga").value = xhr.responseText;
            }
        };

        xhr.send("id_kategori=" + encodeURIComponent(kategoriId));
    } else {
        document.getElementById("harga").value = '';
    }
});
</script>
<?php if ($insertSuccess): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Item berhasil ditambahkan.',
    showConfirmButton: false,
    timer: 2000
  }).then(() => {
    window.location.href = 'item.php';
  });
</script>
<?php endif; ?>



<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>


</body>
</html>

