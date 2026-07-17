<?php
session_start();
include 'db_connect.php';

$email = trim($_POST['adminEmail'] ?? '');
$password = $_POST['adminPassword'] ?? '';

$errorRedirect = "admin-login.php?error=1";

if ($email === '' || $password === '') {
    header("Location: $errorRedirect");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];

        header("Location: admin-dashboard.php");
        exit;
    }
}

header("Location: $errorRedirect");
exit;
