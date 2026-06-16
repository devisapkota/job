<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'] ?? 0;
$match_score = $_GET['match_score'] ?? 0;

if ($job_id == 0) {
    header("Location: index.php");
    exit;
}

$resumeQuery = mysqli_query($conn, "
    SELECT resume_id
    FROM resumes
    WHERE user_id = '$user_id'
    ORDER BY resume_id DESC
    LIMIT 1
");

if (mysqli_num_rows($resumeQuery) == 0) {
    $_SESSION['chat_message'] = "Please upload your resume first before applying.";
    header("Location: index.php");
    exit;
}

$resume = mysqli_fetch_assoc($resumeQuery);
$resume_id = $resume['resume_id'];

$check = mysqli_query($conn, "
    SELECT * FROM applications
    WHERE user_id = '$user_id'
    AND job_id = '$job_id'
");

if (mysqli_num_rows($check) > 0) {
    $_SESSION['chat_message'] = "You already applied for this job.";
    header("Location: my_applications.php");
    exit;
}

mysqli_query($conn, "
    INSERT INTO applications(user_id, job_id, resume_id, match_score, status)
    VALUES('$user_id', '$job_id', '$resume_id', '$match_score', 'Applied')
");

$application_id = mysqli_insert_id($conn);
$user_name = $_SESSION['name'] ?? "User";
$job_res = mysqli_query($conn, "SELECT title FROM jobs WHERE job_id='$job_id'");
$job_data = mysqli_fetch_assoc($job_res);
$job_title = $job_data['title'] ?? "Job";

$notif_msg = "New application received for " . $job_title . " from " . $user_name;
mysqli_query($conn, "INSERT INTO admin_notifications (job_id, application_id, user_id, message) VALUES ('$job_id', '$application_id', '$user_id', '$notif_msg')");

$_SESSION['chat_message'] = "Application submitted successfully.";

header("Location: my_applications.php");
exit;
?>