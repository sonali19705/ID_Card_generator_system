<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Use prepared statement to fetch user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['first_name'] . " " . $row['last_name'];
            $_SESSION['user_email'] = $row['email'];

            header("Location: user-dashboard.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "No account found with this email!";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Generator - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="auth-body">

    <div class="auth-container">
        <div class="auth-card">
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Login to your ID Card System account</p>

            <?php if (!empty($message)): ?>
                <p style="color:red;"><?= $message; ?></p>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="index.php">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <p class="auth-links">
                <a href="#">Forgot Password?</a><br>
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </div>
    </div>
</body>

</html>