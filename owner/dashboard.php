<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role owner
// Sementara dinonaktifkan untuk debugging
/*if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit();
}*/

// Menentukan halaman aktif untuk navigasi
$active_page = "home";

// Menampilkan data dari tabel categories secara dinamis
// Ubah query ini sesuai dengan struktur tabel 'categories' Anda
$query = "SELECT * FROM categories ORDER BY id";
$result = $conn->query($query);

if ($result) {
    $categories = array();
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    echo "Error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rentify</title>
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
            align-items: center; /* Tambahkan align-items: center */
            gap: 30px;
        }

        .nav-links a, .nav-links .dropdown {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            height: 100%; /* Tambahkan height: 100% */
            display: flex; /* Tambahkan display: flex */
            align-items: center; /* Tambahkan align-items: center */
        }

        .nav-links a.active {
            background-color: #2c3454;
        }

        .nav-links a:hover, .nav-links .dropdown:hover {
            background-color: #2c3454;
        }

        .dropdown {
            position: relative;
            display: inline-flex; /* Ubah menjadi inline-flex */
            align-items: center; /* Tambahkan align-items: center */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            top: 100%; /* Pastikan dropdown berada di bawah parent */
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

        .nav-link {
            color: white;
            text-decoration: none;
            cursor: pointer;
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
        }

        .dashboard-header h1 {
            margin: 0;
            color: #1a1f36;
        }

        .dashboard-header h2 {
            font-size: 2em;
            font-weight: bold;
            color: #1a1f36;
            margin-top: 5px;
        }

        .summary-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .summary-box {
            background: linear-gradient(to right, #5fb5e2, #85d1f4);
            color: white;
            border-radius: 12px;
            padding: 25px;
            font-weight: bold;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-box .value {
            font-size: 2.2em;
            color: white;
        }

        .categories-section {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .categories-header h2 {
            font-size: 1.6em;
            color: #333;
        }

        .search-add-container {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }

        .search-add-container input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-button {
            background-color: #1a1f36;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: bold;
        }

        .add-category-button {
            background-color: #1a1f36;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: bold;
            text-decoration: none;
        }

        .category-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .category-table th {
            background-color: #1a1f36;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }

        .category-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .category-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .category-table tbody tr:nth-child(even) {
            background-color: #d6f0fa;
        }

        .action-buttons a {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            color: white;
            font-size: 0.9em;
        }

        .edit-button {
            background-color: #0047ab;
        }

        .delete-button {
            background-color: red;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .search-add-container {
                flex-direction: column;
            }

            .add-category-button {
                width: 100%;
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
        <div class="dashboard-header">
            <h1>Welcome, Admin!</h1>
            <h2>DASHBOARD</h2>
        </div>

            <div class="summary-boxes">
                <div class="summary-box">
                    <span>Total Categories</span>
                    <div class="value"><?php echo isset($categories) ? count($categories) : 0; ?></div>
                </div>
                <div class="summary-box">
                    <span>Total Owner</span>
                    <div class="value">
                        <?php
                        // Query untuk menghitung jumlah owner
                        $owner_query = "SELECT COUNT(*) as total FROM users WHERE role = 'owner'";
                        $owner_result = $conn->query($owner_query);
                        if ($owner_result) {
                            $row = $owner_result->fetch_assoc();
                            echo $row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </div>
                </div>
                <div class="summary-box">
                    <span>Total Item</span>
                    <div class="value">
                        <?php
                        // Query untuk menghitung jumlah item
                        $item_query = "SELECT COUNT(*) as total FROM items";
                        $item_result = $conn->query($item_query);
                        if ($item_result) {
                            $row = $item_result->fetch_assoc();
                            echo $row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </div>
                </div>
            </div>

        <div class="categories-section">
            <div class="categories-header">
                <h2>Categories Item</h2>
            </div>
            <div class="search-add-container">
                <input type="text" id="searchInput" placeholder="Search" />
                <button type="button" onclick="filterTable()" class="search-button">Search</button>
                <a href="addcategory.php" class="add-category-button">+ Add Category</a>
            </div>

            <table class="category-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Categori</th>
                        <th>Name Categori</th>
                        <th>Daily Rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($categories)) {
                        $no = 1;
                        foreach ($categories as $category) {
                            echo "<tr>";
                            echo "<td>" . $no . "</td>";
                            echo "<td>" . htmlspecialchars($category['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($category['name']) . "</td>";
                            echo "<td>Rp. " . number_format($category['daily_rate'], 0, ',', '.') . "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<a href='edit.php?id=" . $category['id'] . "' class='edit-button' onclick=\"window.location.href='edit.php?id=" . $category['id'] . "'\">Edit</a> ";
                            echo "<a href='delete.php?id=" . $category['id'] . "' class='delete-button' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?')\">Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>Tidak ada data kategori</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            const tableRows = document.querySelectorAll(".category-table tbody tr");

            // Fungsi filter baris
            function filterRows(term) {
                tableRows.forEach(row => {
                    const cells = row.querySelectorAll("td");
                    let match = false;

                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(term)) {
                            match = true;
                        }
                    });

                    row.style.display = match ? "" : "none";
                });
            }

            // Event saat mengetik
            searchInput.addEventListener("input", function () {
                const searchTerm = searchInput.value.toLowerCase();
                filterRows(searchTerm);
            });

            // Fungsi untuk tombol Search
            window.filterTable = function () {
                const searchTerm = searchInput.value.toLowerCase();
                filterRows(searchTerm);
            };
        });
    </script>
</body>
</html>