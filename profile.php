<?php
include 'db_connect.php';
session_start();
$user_email = $_SESSION['user_email'] ?? '';

$message = "";

// Update user info
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $mobile = $_POST['mobile'];
    $dob = $_POST['dob'];
    $bloodGroup = $_POST['blood_group'];
    $designation = $_POST['designation'];
    $enrollment = $_POST['enrollment'];
    $department = $_POST['department'];
    $joinDate = $_POST['join_date'];
    
    $updateSql = "UPDATE users SET first_name='$firstName', middle_name='$middleName', last_name='$lastName',
                  mobile='$mobile', dob='$dob', blood_group='$bloodGroup', designation='$designation', 
                  enrollment='$enrollment', department='$department', join_date='$joinDate'
                  WHERE email='$user_email'";
    if($conn->query($updateSql)) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error: ".$conn->error;
    }
}

// Fetch updated user data
$sql = "SELECT * FROM users WHERE email='$user_email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc() ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ID Card Generator</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-logo">ID Card System</div>
    <ul class="navbar-links">
        <li><a href="user-dashboard.php">Home</a></li>
        <li><a href="profile.php" class="active">Profile</a></li>
        <li><a href="my-id-history.php">My IDs</a></li>
    </ul>
    <form method="POST" action="index.php" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

<section class="profile-section">
    <h1 class="section-title">My Profile</h1>

    <?php if($message): ?>
        <p style="color:green;"><?= $message ?></p>
    <?php endif; ?>

    <div class="profile-card">
        <form class="profile-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= $user['first_name'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" value="<?= $user['middle_name'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= $user['last_name'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" readonly>
            </div>
            <div class="form-group">
                <label>Mobile No</label>
                <input type="text" name="mobile" value="<?= $user['mobile'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <input type="text" name="blood_group" value="<?= $user['blood_group'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Designation</label>
                <select name="designation" id="designation" onchange="toggleEnrollmentField()">
                    <option value="Student" <?= ($user['designation'] ?? '')==='Student'?'selected':'' ?>>Student</option>
                    <option value="Teacher" <?= ($user['designation'] ?? '')==='Teacher'?'selected':'' ?>>Teacher</option>
                    <option value="Staff" <?= ($user['designation'] ?? '')==='Staff'?'selected':'' ?>>Staff</option>
                </select>
            </div>
            <div class="form-group" id="enrollmentField">
                <label>Enrollment Number</label>
                <input type="text" name="enrollment" value="<?= $user['enrollment'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" value="<?= $user['department'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Join Date</label>
                <input type="date" name="join_date" value="<?= $user['join_date'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="photo">
            </div>
            <button type="submit" class="primary-btn">Save Changes</button>
        </form>
    </div>
</section>

<script>
function toggleEnrollmentField() {
    const designation = document.getElementById("designation").value;
    document.getElementById("enrollmentField").style.display = (designation === "Student") ? "block" : "none";
}
toggleEnrollmentField();
</script>

</body>
</html>
