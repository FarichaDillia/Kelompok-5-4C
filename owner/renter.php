<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit();
}

// Menentukan halaman aktif untuk navigasi
$active_page = "user";
$users = [];

$q   = "SELECT id, username, nama, email FROM users WHERE role = 'user'";
$res = mysqli_query($conn, $q);

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        /*  ── Contoh data statis sesuai mock‑up ───────────────
            Silakan ganti dengan query real (JOIN ke tabel transaksi) kalau sudah ada.
        ---------------------------------------------------------------------- */
        switch ($row['username']) {
            case 'faricha': // M. Alpha A / Alphatahalah
                $item_count     = 1;
                $monthly_income = 20000;
                break;
            case 'resya':   // Dimas Hadi / Haddimaas
                $item_count     = 3;
                $monthly_income = 95000;
                break;
            case 'alpha':   // Kevin Noval / Novallian
                $item_count     = 5;
                $monthly_income = 240000;
                break;
            default:        // Data dummy untuk user lain
                $item_count     = rand(1, 10);
                $monthly_income = rand(15000, 300000);
        }

        $users[] = [
            'id'             => $row['id'],
            'username'       => $row['username'],
            'nama'           => $row['nama'],
            'email'          => $row['email'],
            'item_count'     => $item_count,
            'monthly_income' => $monthly_income
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renter Management - Rentify</title>
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
    }

    .page-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #1a1f36;
    }

    .search-container {
        text-align: right;
        margin-bottom: 20px;
    }

    .search-box {
        padding: 10px 15px;
        border: none;
        border-radius: 20px;
        width: 250px;
        font-size: 14px;
    }

    .owner-card {
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .owner-info {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 300px; /* Fixed width for consistent alignment */
    }

    .owner-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .owner-details {
        width: 200px; /* Fixed width for consistent alignment */
    }

    .owner-details h3 {
        margin: 0 0 5px 0;
        font-size: 18px;
        color: #1a1f36;
        white-space: nowrap; /* Prevent wrapping */
    }

    .owner-details p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    .owner-stats {
        display: flex;
        align-items: center;
        width: 150px; /* Fixed width for consistent alignment */
        justify-content: center;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 18px;
        font-weight: bold;
        color: #1a1f36;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
    }

    .income {
        font-size: 18px;
        font-weight: bold;
        color: #2ecc71;
        width: 200px; /* Fixed width for consistent alignment */
        text-align: right;
        padding-right: 20px;
    }

    .income .stat-label {
        text-align: right;
    }

    .view-btn {
        background-color: transparent;
        border: none;
        color: #1a1f36;
        font-size: 24px;
        cursor: pointer;
        width: 40px;
        text-align: center;
    }
    
    /* Tambahan CSS untuk membuat seluruh kartu menjadi clickable */
    .owner-link {
        text-decoration: none;
        color: inherit;
        display: contents;
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
        <h1 class="page-title">RENTER</h1>

        <div class="search-container">
            <input type="text" placeholder="Search" class="search-box" id="searchBox">
        </div>

        <div class="card-list">
        <?php foreach ($users as $renter): ?>
            <a href="aboutrenter.php?id=<?php echo $renter['id']; ?>" class="owner-link">
                <div class="owner-card">
                    <!-- Profil & nama -->
                    <div class="owner-info">
                        <img src="admin.jpg" class="owner-avatar" alt="<?= htmlspecialchars($renter['nama']); ?>">
                        <div class="owner-details">
                            <h3><?= htmlspecialchars($renter['nama']); ?></h3>
                            <p><?= htmlspecialchars($renter['username']); ?></p>
                        </div>
                    </div>

                    <!-- Jumlah item -->
                    <div class="owner-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?= $renter['item_count']; ?></div>
                            <div class="stat-label">Item Rented</div>
                        </div>
                    </div>

                    <!-- Biaya bulanan -->
                    <div class="income">
                        Rp. <?= number_format($renter['monthly_income'],0,',','.'); ?>
                        <div class="stat-label">Monthly</div>
                    </div>

                    
                </div>
            </a>
        <?php endforeach; ?>

        <?php if (empty($users)): ?>
            <div class="alert alert-info">No users found.</div>
        <?php endif; ?>
        </div><!-- /.card-list -->
    </div><!-- /.container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple search functionality
        document.getElementById('searchBox').addEventListener('keyup', function() {
            let searchText = this.value.toLowerCase();
            let ownerLinks = document.querySelectorAll('.owner-link');
            
            ownerLinks.forEach(function(link) {
                let ownerName = link.querySelector('.owner-details h3').textContent.toLowerCase();
                let ownerUsername = link.querySelector('.owner-details p').textContent.toLowerCase();
                
                if (ownerName.includes(searchText) || ownerUsername.includes(searchText)) {
                    link.style.display = 'contents';
                } else {
                    link.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>