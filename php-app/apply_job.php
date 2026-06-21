<?php
session_start();
require_once "db.php";

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$match_score = isset($_GET['match_score']) ? floatval($_GET['match_score']) : null;

if (!isset($_SESSION['user_id'])) {
    $redirect_url = "apply_job.php?job_id=" . $job_id;

    if ($match_score !== null) {
        $redirect_url .= "&match_score=" . urlencode($match_score);
    }

    header("Location: login.php?redirect=" . urlencode($redirect_url));
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

if ($job_id <= 0) {
    $_SESSION['error_message'] = "Invalid job selected.";
    header("Location: index.php");
    exit;
}

/* Match score below 25 cannot apply */
if ($match_score !== null && $match_score < 25) {
    $_SESSION['error_message'] = "You cannot apply for this job because your match score is below 25%. Please improve your resume skills first.";
    header("Location: job_details.php?job_id=" . $job_id . "&match_score=" . urlencode($match_score));
    exit;
}

/* Fetch selected job */
$stmt = $conn->prepare("
    SELECT 
        job_id,
        title,
        company,
        description,
        required_skills,
        location,
        salary,
        is_external
    FROM jobs
    WHERE job_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_message'] = "The selected job was not found.";
    header("Location: index.php");
    exit;
}

$job = $result->fetch_assoc();

/* Duplicate application check */
$checkApplication = $conn->prepare("
    SELECT application_id
    FROM applications
    WHERE user_id = ?
    AND job_id = ?
    LIMIT 1
");

$checkApplication->bind_param("ii", $user_id, $job_id);
$checkApplication->execute();
$applicationResult = $checkApplication->get_result();

if ($applicationResult->num_rows > 0) {
    $_SESSION['error_message'] = "You have already applied for this job.";
    header("Location: my_applications.php");
    exit;
}

/* Payment check for external scraped jobs */
if (intval($job['is_external']) == 1) {

    $paymentCheck = $conn->prepare("
        SELECT payment_id
        FROM payments
        WHERE user_id = ?
        AND job_id = ?
        AND purpose = 'job_apply'
        AND payment_status = 'Completed'
        LIMIT 1
    ");

    $paymentCheck->bind_param("ii", $user_id, $job_id);
    $paymentCheck->execute();
    $paymentResult = $paymentCheck->get_result();

    if ($paymentResult->num_rows == 0) {
        header("Location: payment_start.php?purpose=job_apply&job_id=" . $job_id);
        exit;
    }
}

/* Store selected job for resume upload/application process */
$_SESSION['selected_job_id'] = $job_id;
$_SESSION['selected_match_score'] = $match_score ?? 0;

$_SESSION['chat_message'] = "Please upload your resume to apply for " . $job['title'] . " at " . $job['company'] . ".";

header("Location: index.php");
exit;
?>