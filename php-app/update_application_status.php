<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$application_id = $_POST['application_id'] ?? 0;
$status = $_POST['status'] ?? 'Applied';

$status = mysqli_real_escape_string($conn, $status);

mysqli_query($conn, "
    UPDATE applications
    SET status='$status'
    WHERE application_id='$application_id'
");

header("Location: admin_applications.php");
exit;
?>