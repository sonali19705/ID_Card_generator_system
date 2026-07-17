<?php
session_start();
include 'db_connect.php';

// Make sure an ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$request_id = intval($_GET['id']);

// Only a logged-in user (owner of the request) or a logged-in admin may download
$isAdmin = isset($_SESSION['admin_id']);
$isUser = isset($_SESSION['user_id']);

if (!$isAdmin && !$isUser) {
    header("Location: index.php");
    exit;
}

if ($isUser && !$isAdmin) {
    $stmt = $conn->prepare("SELECT * FROM id_requests WHERE id=? AND user_id=? AND status='Approved' LIMIT 1");
    $stmt->bind_param("ii", $request_id, $_SESSION['user_id']);
} else {
    $stmt = $conn->prepare("SELECT * FROM id_requests WHERE id=? AND status='Approved' LIMIT 1");
    $stmt->bind_param("i", $request_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("ID card not found or not approved yet.");
}

$row = $result->fetch_assoc();
$stmt->close();

if (empty($row['pdf_path'])) {
    die("Generated ID card not found.");
}

$filePath = __DIR__ . "/" . $row['pdf_path'];
$fileName = "ID_" . preg_replace('/[^A-Za-z0-9_]/', '_', $row['student_name']) . ".pdf";

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
