<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')) {
    header("Location: login.php");
    exit;
}

$job_id = $_GET['job_id'] ?? 0;

if ($job_id == 0) {
    header("Location: admin_dashboard.php");
    exit;
}

mysqli_query($conn, "DELETE FROM jobs WHERE job_id='$job_id'");

header("Location: admin_dashboard.php");
exit;
?>