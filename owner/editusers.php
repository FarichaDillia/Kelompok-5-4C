<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit();
}

// Mengambil ID user yang akan diedit
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_data = [];

if ($user_id > 0) {
    // Gunakan prepared statement untuk keamanan
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
        } else {
            // Redirect jika user tidak ditemukan
            header("Location: management.php?error=User tidak ditemukan");
            exit();
        }
        
        mysqli_stmt_close($stmt);
    } else {
        // Error saat membuat prepared statement
        header("Location: management.php?error=" . urlencode("Error: " . mysqli_error($conn)));
        exit();
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: management.php?error=ID tidak valid");
    exit();
}

// Proses form jika ada submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan semua field yang dibutuhkan ada
    if (isset($_POST["nama"]) && isset($_POST["email"]) && isset($_POST["role"])) {
        $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $role = mysqli_real_escape_string($conn, $_POST["role"]);
        
        // Validasi input
        if (empty($nama) || empty($email) || empty($role)) {
            $error = "Semua kolom harus diisi!";
        } else {
            // Update data user ke database menggunakan prepared statement
            $update_query = "UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $role, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Redirect ke halaman manage users dengan pesan sukses
                header("Location: management.php?success=User berhasil diperbarui");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "Data yang dikirim tidak lengkap!";
    }
}

// Menentukan halaman aktif untuk navigasi
$active_page = "management";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: #1a1f36;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav-links a.active {
            background-color: #2c3454;
        }

        .nav-links a:hover {
            background-color: #2c3454;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }

        .user-profile span {
            font-size: 14px;
        }

        .container {
            flex: 1;
            background: linear-gradient(to bottom, #a8e0e9, #d0f0f7);
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .back-btn {
            align-self: flex-start;
            margin-top: 20px;
            text-decoration: none;
            color: #000;
            font-size: 24px;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        h1 {
            margin-bottom: 25px;
            color: #1a1f36;
            font-size: 28px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #1a1f36;
            font-size: 18px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 20px;
            outline: none;
            font-size: 16px;
            text-align: center;
        }

        .submit-btn {
            background-color: #1a1f36;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 12px 0;
            width: 60%;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }

        .submit-btn:hover {
            background-color: #2c3454;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            width: 100%;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="rentify.jpg" alt="Logo">
        </div>
        <div class="nav-links">
            <a href="addcategory.php" class="<?php echo $active_page == 'home' ? 'active' : ''; ?>">Home</a>
            <div class="dropdown">
    <a href="#" class="nav-link <?php echo $active_page == 'user' ? 'active' : ''; ?>">User</a>
    <div class="dropdown-content">
    <a href="owner.php">Owner</a>
    <a href="Renter">Renter</a>
    <a href="../logout.php">Logout</a>
</div>
</div>
            <a href="management.php" class="<?php echo $active_page == 'management' ? 'active' : ''; ?>">Management</a>
        </div>
        <div class="user-profile">
            <img src="owner.jpg" alt="Admin">
            <span>Admin</span>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1>Edit User</h1>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" placeholder="Nama" value="<?php echo isset($user_data['nama']) ? htmlspecialchars($user_data['nama']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" value="<?php echo isset($user_data['email']) ? htmlspecialchars($user_data['email']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="owner" <?php echo isset($user_data['role']) && $user_data['role'] == 'owner' ? 'selected' : ''; ?>>Owner</option>
                        <option value="user" <?php echo isset($user_data['role']) && $user_data['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
        <a href="management.php" class="back-btn">←</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>