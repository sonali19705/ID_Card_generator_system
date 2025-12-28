<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - ID Card Requests</title>
  <link rel="stylesheet" href="admin.css"> 
</head>
<body>

<div class="navbar">
  <div class="logo">ID Card System</div>
  <ul>
    <li><a href="admin-dashboard.php" class="active">Dashboard</a></li>
    <li><a href="admin_requests.php">Requests</a></li>
    <li><a href="admin_approved.php">Approved IDs</a></li>
  </ul>
  <form method="POST" action="admin_logout.php" style="display:inline;">
      <button type="submit" class="logout-btn">Logout</button>
  </form>
</div>

<div class="container">
  <h1>Admin Dashboard</h1>
  <h2>Pending ID Card Requests</h2>

  <table>
    <thead>
      <tr>
        <th>Student Name</th>
        <th>Roll No</th>
        <th>Course</th>
        <th>Year</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT * FROM id_requests WHERE status='Pending'";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>".htmlspecialchars($row['student_name'])."</td>";
              echo "<td>".htmlspecialchars($row['roll_no'])."</td>";
              echo "<td>".htmlspecialchars($row['course'])."</td>";
              echo "<td>".htmlspecialchars($row['year'])."</td>";
              echo "<td class='status-pending'>".htmlspecialchars($row['status'])."</td>";
              echo "<td>
                      <form method='POST' action='update_status.php' style='display:inline;'>
                          <input type='hidden' name='id' value='".$row['id']."'>
                          <input type='hidden' name='status' value='Approved'>
                          <button type='submit' class='approve-btn'>Approve</button>
                      </form>
                      <form method='POST' action='update_status.php' style='display:inline;'>
                          <input type='hidden' name='id' value='".$row['id']."'>
                          <input type='hidden' name='status' value='Rejected'>
                          <button type='submit' class='reject-btn'>Reject</button>
                      </form>
                    </td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='6'>No pending requests</td></tr>";
      }
      $conn->close();
      ?>
    </tbody>
  </table>
</div>

</body>
</html>
