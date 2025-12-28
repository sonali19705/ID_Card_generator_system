<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Requests</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="navbar">
    <div class="logo">ID Card System</div>
    <ul>
      <li><a href="admin-dashboard.php">Dashboard</a></li>
      <li><a href="admin_requests.php" class="active">Requests</a></li>
      <li><a href="admin_approved.php">Approved IDs</a></li>
    </ul>
    <form method="POST" action="admin_logout.php" style="display:inline;">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>

  <div class="content">
    <h1>Pending Requests</h1>
    <table>
      <tr>
        <th>Student Name</th>
        <th>Enrollment No</th>
        <th>Course</th>
        <th>Action</th>
      </tr>
      <?php
      $sql = "SELECT * FROM id_requests WHERE status='Pending'";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()):
      ?>
          <tr>
            <td><?= htmlspecialchars($row['student_name']); ?></td>
            <td><?= htmlspecialchars($row['roll_no']); ?></td>
            <td><?= htmlspecialchars($row['course']); ?></td>
            <td>
              <form method="POST" action="update_status.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                <input type="hidden" name="status" value="Approved">
                <button type="submit" class="btn-approve">Approve</button>
              </form>
              <form method="POST" action="update_status.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                <input type="hidden" name="status" value="Rejected">
                <button type="submit" class="btn-reject">Reject</button>
              </form>
            </td>
          </tr>
      <?php
        endwhile;
      } else {
        echo "<tr><td colspan='4'>No pending requests.</td></tr>";
      }
      $conn->close();
      ?>
    </table>
  </div>
</body>
</html>
