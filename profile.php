<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $alamat   = $_POST['alamat'];
    $no_telp  = $_POST['no_telp'];
    $password = $_POST['password'];

    // Validasi dasar
    if (empty($username) || empty($email)) {
        $error = "Username dan email tidak boleh kosong!";
    } else {
        // Update dengan atau tanpa password
        if (!empty($password)) {
            $query = "UPDATE users SET username=?, email=?, alamat=?, no_telp=?, password=? WHERE id=?";
            $stmt  = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $username, $email, $alamat, $no_telp, $password, $user_id);
        } else {
            $query = "UPDATE users SET username=?, email=?, alamat=?, no_telp=? WHERE id=?";
            $stmt  = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $alamat, $no_telp, $user_id);
        }

        // Eksekusi dan redirect
        if (mysqli_stmt_execute($stmt)) {
            header("Location: profile.php?success=1");
            exit;
        } else {
            $error = "Gagal update profil: " . mysqli_error($conn);
        }
    }
}

// Ambil data user untuk ditampilkan
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user   = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(to right, #77acc7, #a1c4fd);
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .profile-card {
      background-color: #ffffff;
      border-radius: 20px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
      padding: 50px 35px;
      max-width: 500px;
      width: 100%;
      text-align: center;
      animation: fadeIn 0.6s ease;
    }

    .profile-icon {
      width: 100px;
      height: 100px;
      background-color: #2e3a59;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto 25px;
    }

    .profile-icon i {
      color: #ffffff;
      font-size: 45px;
    }

    .username-display {
      font-size: 24px;
      font-weight: bold;
      color: #2e3a59;
      margin-top: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .profile-info {
      text-align: left;
      margin-top: 20px;
      background-color: #f2f6fa;
      padding: 20px;
      border-radius: 12px;
    }

    .profile-info p {
      font-size: 15px;
      margin: 12px 0;
      color: #333;
    }

    .profile-info span {
      font-weight: bold;
      color: #2e3a59;
      display: inline-block;
      width: 100px;
    }

    .btn-group {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }

    .btn-custom {
      background-color: #3f5c8b;
      color: #fff;
      padding: 10px 25px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.3s;
    }

    .btn-custom:hover {
      background-color: #2b4066;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>

<div class="profile-card">
  <div class="profile-icon">
    <i class="fas fa-user"></i>
  </div>

  <h2 class="username-display"><?= strtoupper(htmlspecialchars($user['username'])) ?></h2>

  <div class="profile-info">
    <p><span>Email:</span> <?= htmlspecialchars($user['email']) ?></p>
    <p><span>Alamat:</span> <?= htmlspecialchars($user['alamat'] ?? '-') ?></p>
    <p><span>No. Telepon:</span> <?= htmlspecialchars($user['no_telp'] ?? '-') ?></p>
  </div>

  <div class="btn-group">
    <a href="javascript:history.back()" class="btn-custom">Kembali</a>
    <a href="edit.php" class="btn-custom">Edit Profile</a>
    <a href="logout.php" class="btn-custom">Log Out</a>
  </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
  icon: 'success',
  title: 'Profil berhasil diperbarui!',
  showConfirmButton: false,
  timer: 2000
});
</script>
<?php endif; ?>

</body>
</html>
