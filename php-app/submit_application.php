<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    $redirect_url = "submit_application.php?" . $_SERVER['QUERY_STRING'];
    header("Location: login.php?redirect=" . urlencode($redirect_url));
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$resume_id = isset($_GET['resume_id']) ? intval($_GET['resume_id']) : 0;
$match_score = isset($_GET['match_score']) ? floatval($_GET['match_score']) : 0;

if ($job_id == 0 || $resume_id == 0) {
    $_SESSION['error_message'] = "Invalid application request. Please upload your resume and try again.";
    header("Location: my_applications.php");
    exit;
}

// Check if job exists
$jobCheck = $conn->prepare("SELECT job_id, title, company FROM jobs WHERE job_id = ?");
$jobCheck->bind_param("i", $job_id);
$jobCheck->execute();
$jobResult = $jobCheck->get_result();

if ($jobResult->num_rows == 0) {
    $_SESSION['error_message'] = "Selected job was not found.";
    header("Location: my_applications.php");
    exit;
}

$job = $jobResult->fetch_assoc();

// Check if resume belongs to logged-in user
$resumeCheck = $conn->prepare("
    SELECT resume_id 
    FROM resumes 
    WHERE resume_id = ? AND user_id = ?
");
$resumeCheck->bind_param("ii", $resume_id, $user_id);
$resumeCheck->execute();
$resumeResult = $resumeCheck->get_result();

if ($resumeResult->num_rows == 0) {
    $_SESSION['error_message'] = "Invalid resume selected.";
    header("Location: my_applications.php");
    exit;
}

// Check duplicate application
$duplicateCheck = $conn->prepare("
    SELECT application_id 
    FROM applications 
    WHERE user_id = ? AND job_id = ?
");
$duplicateCheck->bind_param("ii", $user_id, $job_id);
$duplicateCheck->execute();
$duplicateResult = $duplicateCheck->get_result();

if ($duplicateResult->num_rows > 0) {
    $_SESSION['warning_message'] = "You have already applied for " . $job['title'] . " at " . $job['company'] . ". You can track your application status below.";
    header("Location: my_applications.php");
    exit;
}

// Insert application
$status = "Applied";

$insert = $conn->prepare("
    INSERT INTO applications 
    (user_id, job_id, resume_id, match_score, status, applied_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");

$insert->bind_param("iiids", $user_id, $job_id, $resume_id, $match_score, $status);

if ($insert->execute()) {
    $application_id = $insert->insert_id;
    
    // Insert admin notification
    $admin_msg = "New application received for " . $job['title'] . " from " . $_SESSION['name'];
    $notif_query = $conn->prepare("INSERT INTO admin_notifications (job_id, application_id, user_id, message) VALUES (?, ?, ?, ?)");
    $notif_query->bind_param("iiis", $job_id, $application_id, $user_id, $admin_msg);
    $notif_query->execute();

    unset($_SESSION['selected_job_id']);

    $_SESSION['success_message'] = "Your application for " . $job['title'] . " at " . $job['company'] . " has been submitted successfully. You can track your application status below.";

    header("Location: my_applications.php");
    exit;
} else {
    $_SESSION['error_message'] = "Application could not be submitted. Please try again.";
    header("Location: my_applications.php");
    exit;
}
?>