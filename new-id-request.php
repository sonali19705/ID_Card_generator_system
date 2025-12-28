<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = "";

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name, roll_no, course, year FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason = $_POST['reason'];

    // Handle file upload
    $documentPath = NULL;
    if (!empty($_FILES['document']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = basename($_FILES['document']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
            $documentPath = $targetFile;
        } else {
            $message = "File upload failed!";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO id_requests (user_id, student_name, roll_no, course, year, reason, document, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $student_name = $user['first_name'] . " " . $user['last_name'];
        $roll_no = $user['roll_no'];
        $course = $user['course'];
        $year = $user['year'];
        $stmt->bind_param("issssss", $user_id, $student_name, $roll_no, $course, $year, $reason, $documentPath);
        if ($stmt->execute()) {
            $message = "ID request submitted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New ID Request - ID Card Generator</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-logo">ID Card System</div>
    <ul class="navbar-links">
        <li><a href="user-dashboard.php">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="my-id-history.php">My IDs</a></li>
    </ul>
    <form method="POST" action="index.php" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

<section class="request-section">
    <h1 class="section-title">Request a New ID Card</h1>

    <?php if (!empty($message)): ?>
        <p style="color:green;"><?= $message; ?></p>
    <?php endif; ?>

    <div class="request-card">
        <form class="request-form" method="POST" action="new-id-request.php" enctype="multipart/form-data">
            <div class="form-group">
                <label>Reason for New ID</label>
                <textarea name="reason" placeholder="Lost card, damaged card, update in details..." rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Upload Supporting Document (if any)</label>
                <input type="file" name="document">
            </div>

            <button type="submit" class="primary-btn">Submit Request</button>
        </form>
    </div>
</section>

<script src="script.js"></script>
</body>
</html>
