<?php
include 'admin_auth_check.php';
include 'db_connect.php';

// Fetch approved requests
$sql = "SELECT * FROM id_requests WHERE status='Approved' ORDER BY id DESC";
$result = $conn->query($sql);

// Check if query succeeded
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Approved IDs</title>
  <link rel="stylesheet" href="admin.css">
</head>

<body>
  <div class="navbar">
    <div class="logo">ID Card System</div>
    <ul>
      <li><a href="admin-dashboard.php">Dashboard</a></li>
      <li><a href="admin_requests.php">Requests</a></li>
      <li><a href="admin_approved.php" class="active">Approved IDs</a></li>
    </ul>
    <form method="POST" action="admin_logout.php" style="display:inline;">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>

  <div class="content">
    <h1>Approved ID Cards</h1>
    <table>
      <tr>
        <th>Student Name</th>
        <th>Enrollment No</th>
        <th>Course</th>
        <th>Status</th>
      </tr>

      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . htmlspecialchars($row["student_name"]) . "</td>
                      <td>" . htmlspecialchars($row["roll_no"]) . "</td>
                      <td>" . htmlspecialchars($row["course"]) . "</td>
                      <td><span class='status approved'>" . htmlspecialchars($row["status"]) . "</span></td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No approved requests found</td></tr>";
      }

      $conn->close();
      ?>
    </table>
  </div>
</body>
</html>
