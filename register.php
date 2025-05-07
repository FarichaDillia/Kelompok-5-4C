<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password, role) VALUES ('$username','$password', 'user')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Berhasil daftar!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Gagal daftar! Username sudah digunakan.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="keywords" content="Rentify, register, penyewaan barang">
  <meta name="description" content="Daftar akun di Rentify untuk mulai menyewa barang.">
  <title>Register - Rentify</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Register Section -->
<section class="register-section py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg">
          <div class="card-body">
            <h2 class="text-center mb-4"><i class="fa fa-user-plus"></i> Register</h2>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-view px-4 py-2">Register</button>
              </div>
            </form>
            <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login disini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Register Section End -->


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
