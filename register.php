<?php
include 'db_connect.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statement
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, contact, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $contact, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Registration successful! <a href='index.php'>Login here</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Generator - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="auth-body">

    <div class="auth-container">
        <div class="auth-card">
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Register for your ID Card System account</p>

            <?php if (!empty($message)): ?>
                <p style="color:red;"><?= $message; ?></p>
            <?php endif; ?>

            <!-- Register Form -->
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="Enter your first name" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="Enter your last name" required>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" name="contact" placeholder="Enter your contact number" required>
                </div>

                <div class="form-group">
                    <label>College Email ID</label>
                    <input type="email" name="email" placeholder="Enter your college email ID" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn-primary">Register</button>
            </form>

            <p class="auth-links">
                Already have an account? <a href="index.php">Login</a>
            </p>
        </div>
    </div>
</body>

</html>