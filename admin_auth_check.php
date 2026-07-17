<?php
// Include this at the top of every admin-only page.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}
