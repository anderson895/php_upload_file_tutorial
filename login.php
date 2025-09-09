<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid username or password.";
    }
}
?>

<form method="POST">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" placeholder="Enter username" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" placeholder="Enter password" required><br><br>

    <button type="submit">Login</button>
</form>

<a href="register.php">Create Account</a>