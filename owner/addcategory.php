<?php
session_start();
include "../config.php";

// Cek login dan role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $daily_rate = mysqli_real_escape_string($conn, $_POST["daily_rate"]);

    if (empty($name) || empty($daily_rate)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $query = "INSERT INTO categories (name, daily_rate) VALUES ('$name', '$daily_rate')";
        if (mysqli_query($conn, $query)) {
            $success = "Kategori berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan: " . mysqli_error($conn);
        }
    }
}

$active_page = "home";
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - Rentify</title>
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
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #1a1f36;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 20px;
            outline: none;
            font-size: 16px;
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
            <a href="dashboard.php" class="<?php echo $active_page == 'home' ? 'active' : ''; ?>">Home</a>
            <div class="dropdown">
                <a href="#" class="nav-link <?php echo $active_page == 'user' ? 'active' : ''; ?>">User</a>
                <div class="dropdown-content">
                    <a href="owner.php">Owner</a>
                    <a href="renter.php">Renter</a>
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
            <h1>Add Categori</h1>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name Categori</label>
                    <input type="text" id="name" name="name" placeholder="Name Categori" required>
                </div>
                <div class="form-group">
                    <label for="daily_rate">Daily Rate</label>
                    <input type="number" id="daily_rate" name="daily_rate" placeholder="Daily Rate" min="0" required>
                </div>
                <button type="submit" class="submit-btn" name="submit">Submit</button>
            </form>
        </div>
        <a href="dashboard.php" class="back-btn">←</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>