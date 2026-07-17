<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch requests for this user
$stmt = $conn->prepare("SELECT * FROM id_requests WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My ID History - ID Card Generator</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-logo">ID Card System</div>
    <ul class="navbar-links">
        <li><a href="user-dashboard.php">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="my-id-history.php" class="active">My IDs</a></li>
    </ul>
    <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>

<section class="history-section">
    <h1 class="section-title">My ID Card Requests</h1>

    <div class="history-card">
        <table class="history-table">
            <thead>
                <tr>
                    <th>ID No</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Requested On</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $fullName = $row['student_name'];
                        echo "<tr>";
                        echo "<td>#".$row['id']."</td>";
                        echo "<td>".htmlspecialchars($fullName)."</td>";
                        echo "<td>".htmlspecialchars($row['year'])."</td>";
                        echo "<td class='status-".strtolower($row['status'])."'>".htmlspecialchars($row['status'])."</td>";
                        echo "<td>".htmlspecialchars($row['created_at'])."</td>";

                        if ($row['status'] === 'Approved' && !empty($row['pdf_path'])) {
                            echo "<td><a href='generate_pdf.php?id=".$row['id']."'><button class='primary-btn'>Download</button></a></td>";
                        } elseif ($row['status'] === 'Rejected') {
                            $reason = !empty($row['rejection_reason']) ? htmlspecialchars($row['rejection_reason']) : 'No reason provided';
                            echo "<td>Rejected: " . $reason . "</td>";
                        } else {
                            echo "<td>—</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No requests found</td></tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="script.js"></script>
</body>
</html>
