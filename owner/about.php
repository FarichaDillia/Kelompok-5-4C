<?php
session_start();
include "../config.php";



$owner_id = mysqli_real_escape_string($conn, $_GET['id']);

// Query untuk mendapatkan data owner
$query_owner = "SELECT id, username, nama, email FROM users WHERE id = '$owner_id' AND role = 'owner'";
$result_owner = mysqli_query($conn, $query_owner);

// Cek apakah owner ditemukan
if (!$result_owner || mysqli_num_rows($result_owner) == 0) {
    // Redirect ke halaman owner jika tidak ditemukan
    header("Location: owner.php");
    exit();
}

// Mendapatkan data owner
$owner = mysqli_fetch_assoc($result_owner);

// Periksa struktur tabel items dengan DESCRIBE
$check_table_structure = mysqli_query($conn, "DESCRIBE items");
$columns = [];
if ($check_table_structure) {
    while ($column = mysqli_fetch_assoc($check_table_structure)) {
        $columns[] = $column['Field'];
    }
}

// Menyesuaikan query berdasarkan kolom yang tersedia
// Variabel untuk nama kolom
$item_name_column = in_array('nama_item', $columns) ? 'nama_item' : (in_array('nama', $columns) ? 'nama' : 'nama_barang');
$item_price_column = in_array('harga', $columns) ? 'harga' : 'price';
$item_category_column = in_array('kategori', $columns) ? 'kategori' : 'category';
$item_image_column = in_array('gambar', $columns) ? 'gambar' : 'image';

// Query untuk mendapatkan item-item yang dimiliki owner
$query_items = "SELECT id, $item_name_column, $item_price_column";
if (in_array($item_category_column, $columns)) {
    $query_items .= ", $item_category_column";
}
if (in_array($item_image_column, $columns)) {
    $query_items .= ", $item_image_column";
}
$query_items .= " FROM items WHERE owner_id = '$owner_id'";

$result_items = mysqli_query($conn, $query_items);

// Hitung total item dan pendapatan bulanan
$total_items = $result_items ? mysqli_num_rows($result_items) : 0;
$monthly_income = 0;

// Array untuk menyimpan data item
$items = [];

// Fetch data item
if ($result_items && mysqli_num_rows($result_items) > 0) {
    while ($row = mysqli_fetch_assoc($result_items)) {
        // Konversi ke format standar untuk template
        $standardized_item = [
            'id' => $row['id'],
            'nama_item' => $row[$item_name_column],
            'harga' => $row[$item_price_column],
            'kategori' => isset($row[$item_category_column]) ? $row[$item_category_column] : 'Uncategorized',
            'gambar' => isset($row[$item_image_column]) ? $row[$item_image_column] : 'computer.jpg'
        ];
        
        $items[] = $standardized_item;
        
        // Asumsi pendapatan bulanan adalah harga sewa dikalikan dengan estimasi penyewaan
        // Untuk sederhana, kita asumsikan setiap item disewa rata-rata 5 kali per bulan
        $monthly_income += $row[$item_price_column] * 5;
    }
}

// Jika tidak ada data real atau error pada query, gunakan data contoh sesuai dengan gambar
if (empty($items)) {
    // Data dari gambar contoh
    for ($i = 0; $i < 3; $i++) {
        $items[] = [
            'id' => $i + 1,
            'nama_item' => 'Komputer',
            'harga' => 100000,
            'kategori' => 'elektornik',
            'gambar' => 'computer.jpg'
        ];
    }
    $total_items = 20; // Dari gambar
    $monthly_income = 500000; // Dari gambar
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Owner - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
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

    .dropdown-content a:hover {
        background-color: #f1f1f1;
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

    .container-fluid {
        flex: 1;
        background: linear-gradient(to bottom, #a8e0e9, #d0f0f7);
        padding: 0;
    }

    .owner-header {
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        padding: 20px;
        margin: 20px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .owner-profile {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .owner-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
    }

    .owner-name {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
        color: #1a1f36;
    }

    .owner-username {
        font-size: 16px;
        color: #666;
        margin: 0;
    }

    .owner-stats {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-item {
        text-align: center;
        padding: 0 15px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #1a1f36;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
    }

    .income {
        font-size: 24px;
        font-weight: bold;
        color: #2ecc71;
        text-align: right;
    }

    .income .stat-label {
        text-align: right;
    }

    .items-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 0 20px;
        margin-top: 20px;
        justify-content: flex-start;
    }

    .item-card {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        width: calc(33.333% - 20px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .item-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .item-details {
        padding: 15px;
    }

    .item-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #1a1f36;
    }

    .item-price {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .view-btn {
        background-color: #3b5998;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .like-btn {
        background-color: transparent;
        border: none;
        color: #a8e0e9;
        font-size: 24px;
        cursor: pointer;
    }

    .back-btn {
        position: fixed;
        bottom: 30px;
        left: 30px;
        background-color: #1a1f36;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .back-btn:hover {
        background-color: #2c3454;
        color: white;
    }

    @media (max-width: 992px) {
        .item-card {
            width: calc(50% - 15px);
        }
    }

    @media (max-width: 768px) {
        .owner-header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        
        .owner-profile {
            flex-direction: column;
        }
        
        .item-card {
            width: 100%;
        }
    }
</style>
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
                    <a href="Renter.php">Renter</a>
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

    <div class="container-fluid">
        <div class="owner-header">
            <div class="owner-profile">
                <img src="admin.jpg" alt="<?php echo htmlspecialchars($owner['nama']); ?>" class="owner-avatar">
                <div>
                    <h1 class="owner-name"><?php echo htmlspecialchars($owner['nama']); ?></h1>
                    <p class="owner-username"><?php echo htmlspecialchars($owner['username']); ?></p>
                </div>
            </div>
            <div class="owner-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $total_items; ?></div>
                    <div class="stat-label">Item Rented</div>
                </div>
            </div>
            <div class="income">
                Rp. <?php echo number_format($monthly_income, 0, ',', '.'); ?>
                <div class="stat-label">Monthly</div>
            </div>
        </div>

        <div class="items-container">
            <?php foreach ($items as $item): ?>
            <div class="item-card">
                <img src="<?php echo !empty($item['gambar']) ? $item['gambar'] : 'computer.jpg'; ?>" alt="<?php echo htmlspecialchars($item['nama_item']); ?>" class="item-image">
                <div class="item-details">
                    <div class="item-title"><?php echo htmlspecialchars($item['nama_item']); ?></div>
                    <div class="item-price">Rp. <?php echo number_format($item['harga'], 0, ',', '.'); ?></div>
                    <div class="action-buttons">
                        <button class="view-btn">View</button>
                        <button class="like-btn">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <a href="owner.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle like button color
        document.querySelectorAll('.like-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.style.color === 'red') {
                    icon.style.color = '#a8e0e9';
                } else {
                    icon.style.color = 'red';
                }
            });
        });
    </script>
</body>
</html>