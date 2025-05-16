<?php
session_start();
include "config.php";

$loginTitle = "Sign In";
$intendedRole = '';
if (isset($_GET['role_login'])) {
    $intendedRole = strtolower(trim($_GET['role_login']));
    if ($intendedRole == 'owner') {
        $loginTitle = "Owner Sign In";
    } elseif ($intendedRole == 'user') {
        $loginTitle = "Renter Sign In";
    } elseif ($intendedRole == 'admin') {
        $loginTitle = "Admin Sign In";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);

    if ($user && $password == $user["password"]) {
        $userRole = strtolower(trim($user["role"]));

        // Validasi akses ke panel login
        if ($intendedRole === 'admin' && $userRole !== 'admin') {
            echo "<script>alert('Anda tidak memiliki akses ke panel ini.'); window.location='login.php?role_login=admin';</script>";
            exit();
        }

        if ($intendedRole === 'owner' && !in_array($userRole, ['owner', 'admin'])) {
            echo "<script>alert('Anda tidak memiliki akses ke panel ini.'); window.location='login.php?role_login=owner';</script>";
            exit();
        }

        if ($intendedRole === 'user' && !in_array($userRole, ['user', 'admin'])) {
            echo "<script>alert('Anda tidak memiliki akses ke panel ini.'); window.location='login.php?role_login=user';</script>";
            exit();
        }

        // Login valid
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];

        // Redirect berdasarkan PANEL LOGIN (bukan role user)
        if ($intendedRole === 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($intendedRole === 'owner') {
            header("Location: owner/dashboard.php");
        } elseif ($intendedRole === 'user') {
            header("Location: index.php");
        } else {
            // fallback aman
            header("Location: index.php");
        }
        exit();
    } else {
        echo "<script>alert('Login gagal! Username atau password salah.'); window.location='login.php" . (!empty($intendedRole) ? "?role_login=" . $intendedRole : "") . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $loginTitle; ?> - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #dcf2f1; /* Warna background */
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: #7fc7d9; /* Warna container login */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            color: #f8f9fa; /* Warna teks utama */
        }
        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: #0f1035; /* Warna judul */
        }
        .form-label {
            color: #0f1035; /* Warna label form */
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .form-control {
            background-color: #dcf2f1; /* Warna input */
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            color: #365886; /* Warna teks input */
            width: 100%;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #0f1035;
            box-shadow: 0 0 0 0.2rem rgba(15, 16, 53, 0.25);
        }
        .btn-login {
            background-color: #0f1035; /* Warna tombol login */
            color: #dcf2f1; /* Warna teks tombol login */
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #365886;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #0f1035; /* Warna link signup */
        }
        .signup-link a {
            color: #365886; /* Warna link signup aktif */
            text-decoration: underline;
        }
        .signup-link a:hover {
            color: #0f1035;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title"><?php echo $loginTitle; ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-login">LOGIN</button>
        </form>
        <p class="signup-link">Don't have an account? <a href="register.php">Signup Here</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>