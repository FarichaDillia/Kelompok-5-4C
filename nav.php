<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose Your Panel - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .panel-choice-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .panel-title {
            color: #343a40;
            margin-bottom: 30px;
        }
        .panel-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
            width: 100%;
            max-width: 300px;
        }
        .panel-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="panel-choice-container">
        <h2 class="panel-title">Sign-in To Your Panel</h2>
        <a href="login.php?role_login=admin">
    <button class="panel-button">Admin Panel</button>
</a>
<a href="login.php?role_login=owner">
    <button class="panel-button">Owner Panel</button>
</a>
<a href="login.php?role_login=user">
    <button class="panel-button">Renter Panel</button>
</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>