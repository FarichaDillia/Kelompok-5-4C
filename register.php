<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"]; 
    $role = 'user'; 

    // Cek apakah username atau email sudah ada
    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Username atau email sudah terdaftar!'); window.location='register.php';</script>";
    } else {
        $insert_query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Pendaftaran berhasil! Silakan pilih panel untuk login.'); window.location='nav.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat mendaftar: " . mysqli_error($conn) . "'); window.location='register.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .btn-primary-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
        }
        .or-separator {
            text-align: center;
            margin: 15px 0;
            color: #6c757d;
        }
        .social-login button {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<section class="register-section">
    <div class="container">
        <div class="card shadow-lg register-card">
            <div class="card-body">
                <h2 class="text-center mb-4">Create Account</h2>
                <div class="social-login">
                    <button class="btn btn-outline-primary mb-2"><i class="fab fa-google me-2"></i> Sign Up With Google</button>
                    <button class="btn btn-outline-primary"><i class="fab fa-facebook-f me-2"></i> Sign Up With Facebook</button>
                </div>
                <div class="or-separator">- OR -</div>
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom">Create Account</button>
                    </div>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="nav.php">Login</a></p>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>