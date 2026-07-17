<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Update user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName   = trim($_POST['first_name'] ?? '');
    $middleName  = trim($_POST['middle_name'] ?? '');
    $lastName    = trim($_POST['last_name'] ?? '');
    $mobile      = trim($_POST['mobile'] ?? '');
    $dob         = $_POST['dob'] ?? '';
    $bloodGroup  = trim($_POST['blood_group'] ?? '');
    $designation = $_POST['designation'] ?? 'Student';
    $enrollment  = trim($_POST['enrollment'] ?? '');
    $department  = trim($_POST['department'] ?? '');
    $joinDate    = $_POST['join_date'] ?? '';

    $dob = $dob !== '' ? $dob : null;
    $joinDate = $joinDate !== '' ? $joinDate : null;

    // Handle optional profile photo upload
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['photo']['tmp_name']);
        if (in_array($fileType, $allowed)) {
            $targetDir = "uploads/photos/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = "user_" . $user_id . "_" . time() . "." . $ext;
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photoPath = $targetFile;
            }
        } else {
            $message = "Photo must be a JPG, PNG or GIF image.";
        }
    }

    if (empty($message)) {
        if ($photoPath !== null) {
            $stmt = $conn->prepare("UPDATE users SET first_name=?, middle_name=?, last_name=?, mobile=?, dob=?, blood_group=?, designation=?, enrollment=?, department=?, join_date=?, photo=? WHERE id=?");
            $stmt->bind_param("sssssssssssi", $firstName, $middleName, $lastName, $mobile, $dob, $bloodGroup, $designation, $enrollment, $department, $joinDate, $photoPath, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET first_name=?, middle_name=?, last_name=?, mobile=?, dob=?, blood_group=?, designation=?, enrollment=?, department=?, join_date=? WHERE id=?");
            $stmt->bind_param("sssssssssi", $firstName, $middleName, $lastName, $mobile, $dob, $bloodGroup, $designation, $enrollment, $department, $joinDate, $user_id);
        }

        if ($stmt->execute()) {
            // Keep session name in sync with any updated first/last name
            $_SESSION['user_name'] = $firstName . " " . $lastName;
            $message = "Profile updated successfully!";
        } else {
            $message = "Something went wrong while updating your profile.";
        }
        $stmt->close();
    }
}

// Fetch updated user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc() ?? [];
$stmt->close();
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
    <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

<section class="profile-section">
    <h1 class="section-title">My Profile</h1>

    <?php if($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="profile-card">
        <form class="profile-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" value="<?= htmlspecialchars($user['middle_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
            </div>
            <div class="form-group">
                <label>Mobile No</label>
                <input type="text" name="mobile" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <input type="text" name="blood_group" value="<?= htmlspecialchars($user['blood_group'] ?? '') ?>">
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
                <input type="text" name="enrollment" value="<?= htmlspecialchars($user['enrollment'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" value="<?= htmlspecialchars($user['department'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Join Date</label>
                <input type="date" name="join_date" value="<?= htmlspecialchars($user['join_date'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="photo">
                <?php if (!empty($user['photo'])): ?>
                    <p style="margin-top:6px;"><img src="<?= htmlspecialchars($user['photo']) ?>" alt="Profile Photo" style="width:70px;height:70px;object-fit:cover;border-radius:50%;"></p>
                <?php endif; ?>
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
