<?php
include 'admin_auth_check.php';
include 'db_connect.php';
include 'pdf_generator.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_requests.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$rejectionReason = trim($_POST['rejection_reason'] ?? '');

// Where to send the admin back to after processing
$redirectTo = "admin_requests.php";
if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin-dashboard.php') !== false) {
    $redirectTo = "admin-dashboard.php";
}

if ($id <= 0 || !in_array($status, ['Approved', 'Rejected'])) {
    header("Location: $redirectTo?msg=invalid");
    exit;
}

// Fetch the request first
$stmt = $conn->prepare("SELECT * FROM id_requests WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    header("Location: $redirectTo?msg=notfound");
    exit;
}

if ($status === 'Approved') {
    // Build and store the ID card PDF
    $targetDir = __DIR__ . "/generated_ids/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $photoData = null;
    if (!empty($request['photo'])) {
        $photoData = preparePhotoForPdf(__DIR__ . "/" . $request['photo']);
    }

    $pdfRelativePath = "generated_ids/ID_" . $id . ".pdf";
    $pdfFullPath = __DIR__ . "/" . $pdfRelativePath;

    generateIdCardPdf([
        'request_id'  => $id,
        'name'        => $request['student_name'],
        'designation' => $request['year'],
        'roll_no'     => $request['roll_no'],
        'department'  => $request['course'],
        'dob'         => $request['dob'],
        'blood_group' => $request['blood_group'],
        'email'       => $request['email'],
        'mobile'      => $request['mobile'],
        'photo_data'  => $photoData,
    ], $pdfFullPath);

    $stmt = $conn->prepare("UPDATE id_requests SET status=?, pdf_path=?, rejection_reason='' WHERE id=?");
    $stmt->bind_param("ssi", $status, $pdfRelativePath, $id);
    $stmt->execute();
    $stmt->close();
} else {
    // Rejected
    if ($rejectionReason === '') {
        $rejectionReason = "No reason provided.";
    }
    $stmt = $conn->prepare("UPDATE id_requests SET status=?, rejection_reason=?, pdf_path='' WHERE id=?");
    $stmt->bind_param("ssi", $status, $rejectionReason, $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: $redirectTo?msg=success");
exit;
