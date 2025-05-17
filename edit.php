<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email    = $_POST["email"];
    $alamat   = $_POST["alamat"];
    $no_telp  = $_POST["no_telp"];
    $password = $_POST["password"];

    if (empty($username) || empty($email)) {
        echo "<script>alert('Username dan Email tidak boleh kosong!'); window.location='edit.php';</script>";
        exit();
    }

    if (!empty($password)) {
        $sql = "UPDATE users SET username=?, email=?, alamat=?, no_telp=?, password=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $email, $alamat, $no_telp, $password, $user_id);
    } else {
        $sql = "UPDATE users SET username=?, email=?, alamat=?, no_telp=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $alamat, $no_telp, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profile.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui profil!'); window.location='edit.php';</script>";
        exit();
    }
}

$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #77acc7, #a1c4fd);
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .edit-container {
      background-color: #ffffff;
      border-radius: 20px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
      padding: 40px 30px;
      max-width: 500px;
      width: 100%;
      animation: fadeIn 0.5s ease;
    }

    h2 {
      text-align: center;
      font-weight: bold;
      color: #2e3a59;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      color: #3f5c8b;
      margin-bottom: 6px;
      display: block;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea {
      width: 100%;
      padding: 10px 12px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }

    small {
      color: #607d8b;
      font-size: 0.85em;
    }

    button[type="submit"] {
      background-color: #3f5c8b;
      color: #fff;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 8px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #2b4066;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>

  <div class="edit-container">
    <h2>Edit Profile</h2>
    <form action="edit.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
      </div>

      <div class="form-group">
        <label for="alamat">Alamat</label>
        <textarea id="alamat" name="alamat" rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="no_telp">No. Telp</label>
        <input type="text" id="no_telp" name="no_telp" value="<?= htmlspecialchars($user['no_telp'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="password">Password Baru</label>
        <input type="password" id="password" name="password" placeholder="********">
        <small>Kosongkan jika tidak ingin mengganti password.</small>
      </div>

      <button type="submit">Simpan Perubahan</button>
    </form>
  </div>

</body>
</html>
