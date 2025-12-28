<?php
include 'db_connect.php';

// Make sure an ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$request_id = intval($_GET['id']);

// Fetch user info
$stmt = $conn->prepare("SELECT student_name, roll_no FROM id_requests WHERE id=? AND status='Approved'");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("ID card not found or not approved yet.");
}

$row = $result->fetch_assoc();
$student_name = $row['student_name'];
$roll_no = $row['roll_no'];

// Path to generated ID
$filePath = __DIR__ . "/generated_ids/ID_" . $roll_no . ".pdf"; // Example PDF
$fileName = "ID_$student_name.pdf";

if (!file_exists($filePath)) {
    die("Generated ID card not found.");
}

// Force download
header("Content-Description: File Transfer");
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit;
