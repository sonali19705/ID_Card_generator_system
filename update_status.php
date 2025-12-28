<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    if ($id > 0 && in_array($status, ['Approved', 'Rejected'])) {
        $stmt = $conn->prepare("UPDATE id_requests SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            // Redirect back to requests page with a success message
            header("Location: admin_requests.php?msg=success");
            exit;
        } else {
            echo "Error updating request.";
        }
        $stmt->close();
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid access.";
}

$conn->close();
