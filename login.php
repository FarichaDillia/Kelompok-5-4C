<?php
session_start();
include "config.php"; // Pastikan file config.php ada dan berisi koneksi database ($conn)

$loginTitle = "Sign In";
$intendedRole = isset($_GET['role_login']) ? $_GET['role_login'] : '';
$sweetAlertScript = ""; // Variabel untuk menyimpan script SweetAlert jika diperlukan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Password masih disimpan tanpa hash, ini TIDAK AMAN untuk produksi.
                                    // Sangat disarankan untuk menggunakan password_hash() dan password_verify().

    // Ambil user dari database
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && $password === $user['password']) { // Perbandingan password tanpa hash, sangat rentan.
        // Login berhasil, simpan sesi
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $userRole = strtolower(trim($user['role']));

        // Validasi role akses sesuai panel login yang dimaksud
        $redirectUrl = "";
        $accessGranted = true;
        $alertTitle = "";
        $alertText = "";

        if ($intendedRole === 'owner') {
            if (!in_array($userRole, ['admin'])) { // Owner panel adalah admin dalam website
                $accessGranted = false;
                $alertTitle = 'Akses Ditolak!';
                $alertText = 'Anda tidak memiliki akses ke panel ini.';
                $redirectUrl = 'login.php?role_login=owner';
            } else {
                $redirectUrl = 'owner/dashboard.php'; // Owner akan redirect ke owner/dashboard.php
            }
        } elseif ($intendedRole === 'admin') {
            if ($userRole !== 'owner') { // Admin panel adalah owner dalam website
                $accessGranted = false;
                $alertTitle = 'Akses Ditolak!';
                $alertText = 'Anda tidak memiliki akses ke panel ini.';
                $redirectUrl = 'login.php?role_login=admin';
            } else {
                // BUG FIX: Jika intendedRole adalah 'admin', dan user.role adalah 'admin',
                // seharusnya redirect ke admin/dashboard.php.
                // Kode asli Anda akan redirect ke 'owner/dashboard.php' jika intendedRole='admin'.
                $redirectUrl = 'admin/dashboard.php';
            }
        } elseif ($intendedRole === 'user') {
            if (!in_array($userRole, ['user'])) { // User panel bisa diakses oleh user dan admin
                $accessGranted = false;
                $alertTitle = 'Akses Ditolak!';
                $alertText = 'Anda tidak memiliki akses ke panel ini.';
                $redirectUrl = 'login.php?role_login=user';
            } else {
                $redirectUrl = 'navbar.php';
            }
        } else {
            // Default redirect jika tidak ada role_login di URL atau role tidak dikenali
            $redirectUrl = 'navbar.php';
        }

        if ($accessGranted) {
            header("Location: " . $redirectUrl);
            exit;
        } else {
            // Jika akses ditolak, siapkan SweetAlert
            $sweetAlertScript = "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: '$alertTitle',
                        text: '$alertText',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location='$redirectUrl';
                    });
                </script>";
        }

    } else {
        // Username atau password salah, siapkan SweetAlert
        $sweetAlertScript = "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal!',
                    text: 'Username atau password salah!',
                    confirmButtonText: 'Coba Lagi'
                });
            </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($loginTitle); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
</head>
<body>

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
    <p class="signup-link">Don't have an account? <a href="register.php">Sign Up Here</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Cetak script SweetAlert hanya jika ada pesan yang perlu ditampilkan
echo $sweetAlertScript;
?>

</body>
</html>