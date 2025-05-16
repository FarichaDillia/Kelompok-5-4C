<?php
session_start();
include "config.php";

// Cek login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($username) || empty($email)) {
        echo "<script>alert('Username dan Email tidak boleh kosong!'); window.location='edit.php';</script>";
        exit();
    }

    // Proses update
    if (!empty($password)) {
    $sql = "UPDATE users SET username=?, email=?, password=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $password, $user_id);
} else {
    $sql = "UPDATE users SET username=?, email=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $user_id);
}


    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profile.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui profil!'); window.location='edit.php';</script>";
        exit();
    }
}

// Ambil data user saat ini untuk ditampilkan di form
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .edit-container {
            background-color: #e0f2f7;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 350px;
            text-align: left;
        }

        h2 {
            color: #263238;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            color: #607d8b;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 12px);
            padding: 8px;
            border: 1px solid #b0bec5;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }

        button[type="submit"] {
            background-color: #1a237e;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #283593;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.5em;
            color: #607d8b;
            text-decoration: none;
        }

        .back-button:hover {
            color: #263238;
        }

        small {
            color: #607d8b;
        }
    </style>
</head>
<body>
    <a href="profile.php" class="back-button">&larr;</a>
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
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="********">
                <small>Biarkan kosong jika tidak ingin mengubah password.</small>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
