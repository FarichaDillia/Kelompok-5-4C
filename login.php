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

    } else {
        echo "<script>alert('Login gagal! Username atau password salah.'); window.location='login.php" . (!empty($intendedRole) ? "?role_login=" . $intendedRole : "") . "';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

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