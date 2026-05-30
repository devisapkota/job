<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$job_id = $_GET['job_id'] ?? 0;

if ($job_id == 0) {
    header("Location: user_dashboard.php");
    exit;
}

$_SESSION['selected_job_id'] = $job_id;
$_SESSION['chat_message'] = "Please upload your resume to check whether this job matches your skills.";

header("Location: index.php");
exit;
?>