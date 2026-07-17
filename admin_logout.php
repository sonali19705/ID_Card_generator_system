<?php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email']);
session_destroy();
header("Location: admin-login.php");
exit;
