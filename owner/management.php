<?php
session_start();
include "../config.php";


$query = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$users = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
$active_page = "management";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
       
</head>
<body class="d-flex flex-column min-vh-100">
   
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
  </div>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
    <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    User
  </a>
  <ul class="dropdown-menu" aria-labelledby="navbarDropdownUser">
    <li><a class="dropdown-item" href="owner.php">Owner</a></li>
    <li><a class="dropdown-item" href="renter.php">Renter</a></li>
  </ul>
</li>

      <li class="nav-item"><a class="nav-link" href="management.php">Management</a></li>
    </ul>
  </div>
  <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
      <i class="fa fa-user-circle"></i> Profile
    </a>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="mb-4 fw-bold text-dark">User Management</h2>

  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <div class="table-responsive">
    <table id="userTable" class="table table-bordered">
      <thead>
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>No. Telepon</th>
          <th>Alamat</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($users)): ?>
          <?php foreach ($users as $user): ?>
            <tr data-id="<?= $user['id'] ?>">
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['no_telp']) ?></td>
              <td><?= htmlspecialchars($user['alamat']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center">Tidak ada data user.</td></tr>
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
    

<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>
</body>
</html>