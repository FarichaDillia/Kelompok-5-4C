<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit();
}

// Menentukan halaman aktif untuk navigasi
$active_page = "home";

// Periksa apakah parameter id tersedia
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

// Ambil data kategori dari database
$query = "SELECT * FROM categories WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

// Jika kategori tidak ditemukan
if ($result->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$category = $result->fetch_assoc();

// Proses form jika ada submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $daily_rate = $_POST['daily_rate'];
    
    // Update data kategori
    $update_query = "UPDATE categories SET name = ?, daily_rate = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sds", $name, $daily_rate, $id);
    
    if ($update_stmt->execute()) {
        // Redirect ke dashboard setelah berhasil update
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Error updating category: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - Rentify</title>
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
            background: linear-gradient(to bottom, #a8e0e9, #d0f0f7);
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
            align-items: center;
            gap: 30px;
        }

        .nav-links a, .nav-links .dropdown {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .nav-links a.active {
            background-color: #2c3454;
        }

        .nav-links a:hover, .nav-links .dropdown:hover {
            background-color: #2c3454;
        }

        .dropdown {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            top: 100%;
            left: 0;
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

        .content {
            flex-grow: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .edit-form {
            width: 100%;
            max-width: 600px;
            padding: 30px;
            box-sizing: border-box;
        }

        .edit-form h1 {
            margin-bottom: 40px;
            color: #1a1f36;
            text-align: center;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #1a1f36;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            border: none;
            background-color: white;
            font-size: 1em;
            text-align: center;
        }

        .submit-btn {
            background-color: #1a1f36;
            color: white;
            padding: 15px 0;
            border: none;
            border-radius: 25px;
            width: 100%;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .back-link {
            margin-top: 20px;
            font-size: 1.5em;
            color: #1a1f36;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .edit-form {
                padding: 20px;
            }
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

    <div class="content">
        <div class="edit-form">
            <h1>Edit Categori</h1>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Name Categori</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" placeholder="Nama Categori" required>
                </div>
                
                <div class="form-group">
                    <label for="daily_rate">Daily Rate</label>
                    <input type="number" id="daily_rate" name="daily_rate" value="<?php echo htmlspecialchars($category['daily_rate']); ?>" placeholder="Daily Rate" required>
                </div>
                
                <button type="submit" class="submit-btn">Submit</button>
            </form>
            
            <a href="dashboard.php" class="back-link">←</a>
        </div>
    </div>
</body>
</html>