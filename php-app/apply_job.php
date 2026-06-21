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

$user_id = intval($_SESSION['user_id']);

if ($job_id == 0) {
    $_SESSION['error_message'] = "Invalid job selected.";
    header("Location: index.php");
    exit;
}

/* =====================================================
   CHECK IF JOB EXISTS
   ===================================================== */

$stmt = $conn->prepare("
    SELECT 
        job_id, 
        title, 
        company,
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

/* =====================================================
   PAYMENT REQUIRED FOR SCRAPED / EXTERNAL JOBS
   ===================================================== */

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

/* =====================================================
   CHECK DUPLICATE APPLICATION
   ===================================================== */

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

/* =====================================================
   STORE SELECTED JOB FOR RESUME UPLOAD / APPLICATION FLOW
   ===================================================== */

$_SESSION['selected_job_id'] = $job_id;
$_SESSION['chat_message'] = "Please upload your resume to apply for " . $job['title'] . " at " . $job['company'] . ".";

/*
|--------------------------------------------------------------------------
| Redirect to index.php because your system uses index page/chat interface
| to continue the resume upload and application process.
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;
?>