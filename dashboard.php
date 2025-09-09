<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { 
            display: flex;
            margin: 0;
            font-family: Arial; 
        }
        .sidebar {
            width: 250px;
            background: #333;
            color: #fff;
            height: 100vh;
            padding: 20px;
        }
        .sidebar img { 
            width: 100px;
            border-radius: 50%;
        }
        .content { 
            flex: 1;
            padding: 20px; 
        }
        a { 
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="uploads/<?= htmlspecialchars($user['profile']) ?>" alt="Profile"><br>
        <h3><?= htmlspecialchars($user['username']) ?></h3>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
        <p>This is your dashboard.</p>
    </div>
</body>
</html>
