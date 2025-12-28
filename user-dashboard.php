<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - ID Card Generator</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-logo">ID Card System</div>
    <ul class="navbar-links">
        <li><a href="user-dashboard.php" class="active">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="my-id-history.php">My IDs</a></li>
    </ul>
    <form method="POST" action="index.php" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

<header class="hero">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Request, preview, and download your ID cards in just a few clicks.</p>
</header>

<section class="dashboard-cards">
    <div class="card" onclick="window.location.href='new-id-request.php'">
        <h3>Request New ID</h3>
        <p>Submit a request for a new ID card.</p>
    </div>
    <div class="card" onclick="window.location.href='fresher-id-request.php'">
        <h3>Fresher ID Request</h3>
        <p>Request an ID card specifically for new students (freshers).</p>
    </div>
    <div class="card" onclick="window.location.href='my-id-history.php'">
        <h3>My IDs</h3>
        <p>View and download your issued ID cards.</p>
    </div>
    <div class="card" onclick="window.location.href='profile.php'">
        <h3>Profile</h3>
        <p>Update your personal details and photo.</p>
    </div>
</section>

<script src="script.js"></script>
</body>
</html>
