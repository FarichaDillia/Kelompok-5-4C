<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);

    if ($user && $password == $user["password"]) {  // langsung cocokkan string
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        
        // Periksa role pengguna dan redirect sesuai role
        if ($user["role"] == "admin") {
            header("Location: admin/dashboard.php");
        } elseif ($user["role"] == "owner") {
            header("Location: owner/dashboard.php");
        } else {
            header("Location: index.php");
        }
    } else {
        echo "<script>alert('Login gagal!'); window.location='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Rentify</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Login Section -->
<section class="login-section py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg">
          <div class="card-body">
            <h2 class="text-center mb-4"><i class="fa fa-user-circle"></i> Login</h2>
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
                <button type="submit" class="btn btn-view px-4 py-2">Login</button>
              </div>
            </form>
            <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar disini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Login Section End -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>