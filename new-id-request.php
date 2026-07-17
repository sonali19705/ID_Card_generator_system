<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = "";

// Fetch user details (profile must be filled in first)
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, enrollment, department, designation, dob, blood_group, email, mobile, photo FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$profileIncomplete = empty($user['enrollment']) || empty($user['department']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($profileIncomplete) {
        $message = "Please complete your profile (enrollment number and department) before requesting an ID card.";
    } else {
        $reason = trim($_POST['reason'] ?? '');

        if ($reason === '') {
            $message = "Please provide a reason for your request.";
        } else {
            // Handle optional supporting document upload
            $documentPath = null;
            if (!empty($_FILES['document']['name']) && $_FILES['document']['error'] == 0) {
                $targetDir = "uploads/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                $fileName = basename($_FILES['document']['name']);
                $targetFile = $targetDir . time() . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName);

                if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
                    $documentPath = $targetFile;
                } else {
                    $message = "File upload failed!";
                }
            }

            if (empty($message)) {
                $student_name = trim($user['first_name'] . " " . ($user['middle_name'] ? $user['middle_name'] . " " : "") . $user['last_name']);
                $roll_no = $user['enrollment'];
                $course = $user['department'];
                $year = $user['designation'];
                $dob = $user['dob'];
                $bloodGroup = $user['blood_group'];
                $email = $user['email'];
                $mobile = $user['mobile'];
                $photo = $user['photo'];

                $stmt = $conn->prepare("INSERT INTO id_requests
                    (user_id, request_type, student_name, roll_no, course, year, dob, blood_group, email, mobile, photo, reason, document, status)
                    VALUES (?, 'New', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt->bind_param(
                    "isssssssssss",
                    $user_id, $student_name, $roll_no, $course, $year, $dob, $bloodGroup, $email, $mobile, $photo, $reason, $documentPath
                );
                if ($stmt->execute()) {
                    $message = "ID request submitted successfully!";
                } else {
                    $message = "Something went wrong. Please try again.";
                }
                $stmt->close();
            }
        }
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
    <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

<section class="request-section">
    <h1 class="section-title">Request a New ID Card</h1>

    <?php if (!empty($message)): ?>
        <p style="color:green;"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($profileIncomplete): ?>
        <p style="color:red;">Your profile is incomplete. Please <a href="profile.php">update your profile</a> (enrollment number and department) before submitting a request.</p>
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
