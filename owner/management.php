<?php
session_start();
include "../config.php";

// Cek apakah pengguna sudah login dan memiliki role owner
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "owner") {
    header("Location: ../login.php");
    exit();
}

// Proses jika ada request hapus user
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    
    // Pastikan ID valid
    if ($delete_id > 0) {
        // Gunakan prepared statement untuk keamanan
        $delete_query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "User berhasil dihapus!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error = "ID user tidak valid!";
    }
}

// Mengambil semua data user dari database
$query = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    $error = "Error saat mengambil data user: " . mysqli_error($conn);
} else {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Menentukan halaman aktif untuk navigasi
$active_page = "management";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Rentify</title>
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

        h1 {
            color: #1a1f36;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .search-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .search-box {
            position: relative;
            width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .table-container {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background-color: #0f1730;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: normal;
        }

        table td {
            padding: 12px 15px;
            text-align: left;
            background-color: #ffffff;
            border-bottom: 1px solid #eee;
        }

        table tr:nth-child(even) td {
            background-color: #edf6f9;
        }

        .action-btns {
            display: flex;
            gap: 5px;
        }

        .btn-action {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            min-width: 60px;
        }

        .btn-edit {
            background-color: #3b5998;
            color: white;
        }

        .btn-delete {
            background-color: #e63946;
            color: white;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
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
        <h1>USER MANAGEMENT</h1>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Search" onkeyup="searchTable()">
            </div>
        </div>

        <div class="table-container">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach($users as $user): ?>
                        <tr data-id="<?php echo $user['id']; ?>">
                            <td><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-edit">Edit</button>
                                <button class="btn-action btn-delete">Delete</button>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Tidak ada data user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("userTable");
            tr = table.getElementsByTagName("tr");
            
            for (i = 1; i < tr.length; i++) { // Start from 1 to skip header row
                let found = false;
                let tdArray = tr[i].getElementsByTagName("td");
                
                // Check each cell in the row
                for (let j = 0; j < tdArray.length - 1; j++) { // Skip the action column
                    let td = tdArray[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                // Show/hide the row based on search results
                if (found) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
        
        // Add event listeners for edit and delete buttons
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.btn-edit');
            const deleteButtons = document.querySelectorAll('.btn-delete');
            
            // Tambahkan event listener untuk tombol edit
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const userId = row.getAttribute('data-id');
                    
                    if (userId) {
                        // Redirect ke editusers.php dengan parameter id
                        window.location.href = `editusers.php?id=${userId}`;
                    } else {
                        alert('ID user tidak ditemukan!');
                    }
                });
            });
            
            deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        const name = row.cells[0].innerText;
        const userId = row.getAttribute('data-id');
        
        if (userId && confirm(`Apakah kamu yakin ingin menghapus ${name}?`)) {
            window.location.href = `management.php?delete=${userId}&t=${new Date().getTime()}`;
        }
    });
});

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>