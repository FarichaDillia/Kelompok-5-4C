<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $alamat   = mysqli_real_escape_string($conn, $_POST["alamat"]);
    $no_telp  = mysqli_real_escape_string($conn, $_POST["no_telp"]);
    $role     = mysqli_real_escape_string($conn, $_POST["role"]);

    // Validasi role
    if (!in_array($role, ['user', 'owner'])) {
        echo "<script>alert('Role tidak valid.'); window.location='register.php';</script>";
        exit;
    }

    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Username atau email sudah terdaftar!'); window.location='register.php';</script>";
    } else {
        $insert_query = "INSERT INTO users (username, email, password, alamat, no_telp, role)
                         VALUES ('$username', '$email', '$password', '$alamat', '$no_telp', '$role')";
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Pendaftaran berhasil sebagai $role!'); window.location='nav.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat mendaftar.'); window.location='register.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #77acc7;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }

    .register-card {
      background-color: #ffffff;
      border-radius: 20px;
      padding: 40px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    h2 {
      text-align: center;
      color: #2f3e5c;
      margin-bottom: 30px;
    }

    label {
      font-weight: 600;
      margin-bottom: 5px;
      color: #2e3a59;
    }

    input, textarea, select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ced4da;
      border-radius: 10px;
      margin-bottom: 18px;
      font-size: 15px;
      background-color: #f9fbfd;
    }

    input:focus, textarea:focus, select:focus {
      border-color: #3f5c8b;
      outline: none;
    }

    .btn-submit {
      background-color: #3f5c8b;
      color: #fff;
      border: none;
      width: 100%;
      padding: 12px;
      font-weight: 600;
      border-radius: 10px;
      transition: 0.3s;
    }

    .btn-submit:hover {
      background-color: #2b4066;
    }

    .text-center a {
      color: #2b3a67;
      text-decoration: underline;
    }

    .text-center a:hover {
      color: #0f1035;
    }
  </style>
</head>
<body>

<div class="register-card">
  <h2>Create Account</h2>
  <form method="POST">
    <label for="username">Username</label>
    <input type="text" name="username" required>

    <label for="email">Email</label>
    <input type="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" name="password" required>

    <label for="alamat">Alamat</label>
    <textarea name="alamat" rows="3"></textarea>

    <label for="no_telp">No. Telp</label>
    <input type="text" name="no_telp" placeholder="08xxxxxxxxxx">

    <label for="role">Pilih Role</label>
    <select name="role" required>
      <option value="user">Renter</option>
      <option value="owner">Owner</option>
    </select>

    <button type="submit" class="btn-submit">Create Account</button>
  </form>
  <p class="text-center mt-3">Already have an account? <a href="nav.php">Login</a></p>
</div>

</body>
</html>
