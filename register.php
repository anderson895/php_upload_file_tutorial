<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle file upload
    $profileName = "default.png";
    if (!empty($_FILES['profile']['name'])) {
        $profileName = time() . "_" . basename($_FILES['profile']['name']);
        move_uploaded_file($_FILES['profile']['tmp_name'], "uploads/" . $profileName);
    }

    $stmt = $pdo->prepare("INSERT INTO user (username,email,password,profile) VALUES (?,?,?,?)");
    if ($stmt->execute([$username, $email, $password, $profileName])) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error during registration.";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" placeholder="Enter username" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" placeholder="Enter email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" placeholder="Enter password" required><br><br>

    <label for="profile">Profile Picture:</label><br>
    <input type="file" id="profile" name="profile"><br><br>

    <button type="submit">Register</button>
</form>


<a href="login.php">Login Account</a>