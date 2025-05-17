<?php
session_start();
include "config.php";

// Cek apakah sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION["user_id"];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
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

        .profile-container {
            background-color: #e0f2f7;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 350px;
            text-align: center;
            position: relative;
        }

        .profile-header {
            background-color: #4dd0e1;
            border-radius: 10px 10px 0 0;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #ffb74d;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 10px;
        }

        .avatar::before {
            content: '';
            display: block;
            width: 50%;
            height: 50%;
            background-color: #212121;
            border-radius: 50%;
        }

        .username {
            font-size: 1.5em;
            font-weight: bold;
            color: #263238;
            margin-bottom: 5px;
        }

        .role {
            color: #607d8b;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .info {
            text-align: left;
            margin-bottom: 15px;
        }

        .info p {
            margin: 8px 0;
            color: #37474f;
        }

        .info strong {
            font-weight: bold;
            color: #00838f;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            background-color: #fdd835;
            color: #212121;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9em;
        }

        .button:hover {
            background-color: #fbc02d;
        }

        .logout-button {
            background-color: #ffeb3b;
        }

        .logout-button:hover {
            background-color: #f9a825;
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
    </style>
</head>
<body>
    
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar"></div>
            <div class="username"><?php echo htmlspecialchars($user["username"]); ?></div>
            <div class="role"><?php echo htmlspecialchars($user["role"]); ?></div>
        </div>
        <div class="info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user["username"] ?? "-"); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
            
        </div>
        <div class="actions">
           <a href="logout.php" class="button logout-button">Log Out</a>

            <a href="edit.php" class="button">Edit Profile</a>
        </div>
    </div>
</body>
</html>
