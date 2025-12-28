<?php
include 'db_connect.php';  // Include your common database connection

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data
    $firstName   = $_POST['first_name'];
    $middleName  = $_POST['middle_name'];
    $lastName    = $_POST['last_name'];
    $enrollment  = $_POST['enrollment'];
    $department  = $_POST['department'];
    $designation = $_POST['designation'];
    $dob         = $_POST['dob'];
    $bloodGroup  = $_POST['blood_group'];
    $email       = $_POST['email'];
    $mobile      = $_POST['mobile'];

    // Handle file upload
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir = "uploads/photos/";
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        $photo = $targetDir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo);
    }

    // Combine full name
    $fullName = $firstName . " " . ($middleName ? $middleName . " " : "") . $lastName;

    // Insert into id_requests table
    $sql = "INSERT INTO id_requests (student_name, roll_no, course, year, STATUS, document)
            VALUES ('$fullName', '$enrollment', '$department', '$designation', 'Pending', '$photo')";

    if ($conn->query($sql) === TRUE) {
        $message = "Request submitted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Fresher ID Request</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Navbar -->
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

<!-- Page Header -->
<header class="hero">
    <h1>New ID Request</h1>
    <p>Submit your details to request a new or replacement ID card.</p>
</header>

<!-- Request Form -->
<section class="form-section">
    <div class="form-card">
        <?php if(!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
        <form id="newIdForm" method="POST" action="fresher-id-request.php" enctype="multipart/form-data">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" placeholder="Enter your middle name">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
                <label>Enrollment/Employee Number</label>
                <input type="text" name="enrollment" placeholder="Enter your enrollment/employee number" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" placeholder="Enter your department" required>
            </div>
            <div class="form-group">
                <label>Designation</label>
                <input type="text" name="designation" placeholder="Enter your designation (e.g., Student, Staff)" required>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" required>
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <input type="text" name="blood_group" placeholder="e.g., A+, O-" required>
            </div>
            <div class="form-group">
                <label>Email ID</label>
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="tel" name="mobile" placeholder="Enter your mobile number" required>
            </div>
            <div class="form-group">
                <label>Upload Photo</label>
                <input type="file" name="photo" accept="image/*" required>
            </div>
            <button type="submit" class="btn-primary">Submit Request</button>
        </form>
    </div>
</section>

<script src="script.js"></script>
</body>
</html>
