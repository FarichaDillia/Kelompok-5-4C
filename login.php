<?php
session_start();
include "config.php";

$loginTitle = "Sign In";

// Tangkap role dari URL jika ada
$intendedRole = isset($_GET['role_login']) ? $_GET['role_login'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // tidak di-hash

    // Ambil user dari database
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && $password === $user['password']) {
        // Simpan sesi login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $userRole = strtolower(trim($user['role']));

        // Validasi role akses sesuai panel login
        if ($intendedRole === 'admin' && $userRole !== 'admin') {
            echo "<script>alert('Anda tidak memiliki akses ke panel ini.'); window.location='login.php?role_login=admin';</script>";
            exit;
        }
        if ($intendedRole === 'owner' && !in_array($userRole, ['owner', 'admin'])) {
            echo "<script>alert('Anda tidak memiliki akses ke panel ini.'); window.location='login.php?role_login=owner';</script>";
            exit;
        }
        if ($intendedRole === 'user' && !in_array($userRole, ['user', 'admin'])) {
            echo "<script>alert('Anda tidak memiliki akses ke panel ini.'); window.location='login.php?role_login=user';</script>";
            exit;
        }

        // Redirect sesuai panel login
        if ($intendedRole === 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($intendedRole === 'owner') {
            header("Location: owner/dashboard.php");
        } elseif ($intendedRole === 'user') {
            header("Location: navbar.php");
        } else {
            // fallback umum
            header("Location: navbar.php");
        }
        exit;
    } else {
        echo "<script>alert('Username atau password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
    body {
        background-color: #77acc7;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    .login-box {
        background-color: #ffffff;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 450px;
        text-align: center;
    }

    .login-box h2 {
        color: #2f3e5c;
        font-size: 26px;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .login-box .form-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .login-box label {
        color: #0f1035;
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
    }

    .login-box input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 16px;
        background-color: #f4f6f9;
        color: #333;
        box-sizing: border-box;
    }

    .login-box input:focus {
        border-color: #2b3a67;
        outline: none;
    }

    .login-box .btn-login {
        width: 100%;
        background-color: #3f5c8b;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }

    .login-box .btn-login:hover {
        background-color: #2d4263;
    }

    .login-box .signup-link {
        margin-top: 20px;
        font-size: 14px;
        color: #0f1035;
    }

    .login-box .signup-link a {
        color: #2b3a67;
        font-weight: 600;
        text-decoration: underline;
    }

    .login-box .signup-link a:hover {
        color: #0f1035;
    }
</style>

<div class="login-box">
    <h2>Sign In</h2>
    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required />
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required />
        </div>
        <button type="submit" class="btn-login">LOGIN</button>
    </form>
    <p class="signup-link">Don't have an account? <a href="register.php">Signup Here</a></p>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
