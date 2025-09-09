<?php
include 'db.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $password        = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Server-side Confirm Password Validation
    if ($password !== $confirmPassword) {
        $message = "<div class='alert alert-danger'> Passwords do not match.</div>";
    } else {
        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //  Handle profile upload
        $profileName = "default.png";
        if (!empty($_FILES['profile']['name'])) {
            $profileName = time() . "_" . basename($_FILES['profile']['name']);
            move_uploaded_file($_FILES['profile']['tmp_name'], "uploads/" . $profileName);
        }

        //  Check if username or email already exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        $exists = $check->fetchColumn();

        if ($exists > 0) {
            $message = "<div class='alert alert-danger'> Username or Email already taken. Try another.</div>";
        } else {
            //  Insert new user
            $stmt = $pdo->prepare("INSERT INTO user (username,email,password,profile) VALUES (?,?,?,?)");
            if ($stmt->execute([$username, $email, $hashedPassword, $profileName])) {
                $message = "<div class='alert alert-success'> Registration successful! <a href='login.php'>Login here</a></div>";
            } else {
                $message = "<div class='alert alert-danger'> Error during registration. Please try again.</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm p-4" style="max-width: 450px; width: 100%;">
        <h3 class="text-center mb-4">Create Account</h3>

        <!-- Show messages -->
        <?= $message ?>

        <!-- Registration Form -->
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" 
                       class="form-control" placeholder="Enter username" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" 
                       class="form-control" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" 
                       class="form-control" placeholder="Enter password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="form-control" placeholder="Confirm password" required>
            </div>

            <div class="mb-3">
                <label for="profile" class="form-label">Profile Picture</label>
                <input type="file" id="profile" name="profile" class="form-control">
            </div>

            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</div>

<script>
function validateForm() {
    let password = document.getElementById("password").value;
    let confirm  = document.getElementById("confirm_password").value;

    if (password !== confirm) {
        alert("Passwords do not match!");
        return false;
    }
    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
    }
    return true;
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
