<?php
session_start();
include "../config.php";

$insert_error = false;

if (isset($_POST['submit'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $daily_rate = mysqli_real_escape_string($conn, $_POST['daily_rate']);

    $query = "INSERT INTO kategori (nama_kategori, daily_rate) VALUES ('$nama_kategori', '$daily_rate')";

    if (mysqli_query($conn, $query)) {
        header("Location: addcategory.php?add_success=1");
        exit;
    } else {
        $insert_error = true; // set flag error
    }
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Owner - Rentify</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link rel="stylesheet" href="style.css">
</head>
<style>
        body {
            background-color: #77acc7;
        }
        .form-container {
            background-color: #fff;
            border-radius: 15px;
            padding: 40px;
            max-width: 600px;
            margin: auto;
            margin-top: 80px;
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

<div class="container">
        <div class="form-container">
            <h2 class="text-center">Form Tambah Kategori</h2>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Daily Rate</label>
                    <input type="number" name="daily_rate" class="form-control" required min="0">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
    
<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle like button color
        document.querySelectorAll('.like-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.style.color === 'red') {
                    icon.style.color = '#a8e0e9';
                } else {
                    icon.style.color = 'red';
                }
            });
        });
    </script>
    <?php if (isset($_GET['add_success']) && $_GET['add_success'] == 1): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Kategori berhasil ditambahkan.',
      timer: 2000,
      showConfirmButton: false
    });
  });
</script>
<?php endif; ?>
<?php if ($insert_error): ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan!',
      text: 'Terjadi kesalahan saat menambahkan kategori.',
      confirmButtonText: 'OK'
    });
  });
</script>
<?php endif; ?>


</body>
</html>