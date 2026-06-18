<?php
session_start();
include "db.php";

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if (!isset($_SESSION['user_id'])) {
    $redirect_url = "apply_job.php?job_id=" . $job_id;
    header("Location: login.php?redirect=" . urlencode($redirect_url));
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

if ($job_id == 0) {
    $_SESSION['error_message'] = "Invalid job selected.";
    header("Location: index.php");
    exit;
}

// Check if job exists
$stmt = $conn->prepare("SELECT job_id, title, company FROM jobs WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_message'] = "The selected job was not found.";
    header("Location: user_dashboard.php");
    exit;
}

$job = $result->fetch_assoc();

// Store selected job in session
$_SESSION['selected_job_id'] = $job_id;
$_SESSION['chat_message'] = "Please upload your resume to apply for " . $job['title'] . " at " . $job['company'] . ".";

header("Location: index.php");
exit;
?>