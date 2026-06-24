<?php
session_start();
require_once "db.php";

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$url_match_score = isset($_GET['match_score']) && $_GET['match_score'] !== '' ? floatval($_GET['match_score']) : null;

if (!isset($_SESSION['user_id'])) {
    $redirect_url = "apply_job.php?job_id=" . $job_id;

    if ($url_match_score !== null) {
        $redirect_url .= "&match_score=" . urlencode($url_match_score);
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

/* =====================================================
   FETCH SELECTED JOB
   ===================================================== */

$stmt = $conn->prepare("
    SELECT *
    FROM jobs
    WHERE job_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $job_id);
$stmt->execute();
$jobResult = $stmt->get_result();

if ($jobResult->num_rows == 0) {
    $_SESSION['error_message'] = "The selected job was not found.";
    header("Location: index.php");
    exit;
}

$job = $jobResult->fetch_assoc();

/* =====================================================
   CHECK IF USER HAS UPLOADED RESUME
   ===================================================== */

$resumeStmt = $conn->prepare("
    SELECT resume_id, extracted_skills
    FROM resumes
    WHERE user_id = ?
    ORDER BY resume_id DESC
    LIMIT 1
");

$resumeStmt->bind_param("i", $user_id);
$resumeStmt->execute();
$resumeResult = $resumeStmt->get_result();

if ($resumeResult->num_rows == 0) {

    $_SESSION['selected_job_id'] = $job_id;

    $_SESSION['chat_message'] = "
        You selected " . $job['title'] . " at " . $job['company'] . ".
        Please upload your resume first. After uploading, CareerPilot AI will check your ATS score and match score.
        If your match score is below 25%, you cannot apply for this job.
    ";

    $_SESSION['error_message'] = "Please upload your resume first before applying for this job.";

    header("Location: index.php#chatBox");
    exit;
}

$resume = $resumeResult->fetch_assoc();
$resume_id = intval($resume['resume_id']);
$extracted_skills = strtolower($resume['extracted_skills'] ?? '');

/* =====================================================
   HELPER FUNCTION TO CLEAN SKILLS
   ===================================================== */

function cleanSkills($skillsText) {
    $skillsText = strtolower($skillsText ?? '');

    $skills = preg_split('/[,|\/\n\r]+/', $skillsText);
    $clean = [];

    foreach ($skills as $skill) {
        $skill = trim($skill);

        if ($skill !== '') {
            $clean[] = $skill;
        }
    }

    return array_unique($clean);
}

/* =====================================================
   CALCULATE MATCH SCORE
   Formula:
   Matching Skills / Required Skills * 100
   ===================================================== */

if ($url_match_score !== null) {
    $match_score = $url_match_score;
} else {
    $user_skills = cleanSkills($extracted_skills);
    $required_skills = cleanSkills($job['required_skills'] ?? '');

    $matched_count = 0;

    foreach ($required_skills as $requiredSkill) {
        foreach ($user_skills as $userSkill) {
            if ($requiredSkill === $userSkill) {
                $matched_count++;
                break;
            }
        }
    }

    if (count($required_skills) > 0) {
        $match_score = round(($matched_count / count($required_skills)) * 100, 2);
    } else {
        $match_score = 0;
    }

    /* Small title bonus, same as suggestions page */
    foreach ($user_skills as $skill) {
        if ($skill !== '' && strpos(strtolower($job['title']), $skill) !== false) {
            $match_score += 15;
        }
    }

    if ($match_score > 100) {
        $match_score = 100;
    }
}

/* =====================================================
   BLOCK APPLY IF MATCH SCORE BELOW 25%
   ===================================================== */

if ($match_score < 25) {
    $_SESSION['error_message'] = "You cannot apply for this job because your match score is below 25%. Please improve your resume skills first.";

    header("Location: job_details.php?job_id=" . $job_id . "&match_score=" . urlencode($match_score));
    exit;
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
   PAYMENT CHECK FOR EXTERNAL / SCRAPED JOBS
   ===================================================== */

if (intval($job['is_external'] ?? 0) == 1) {

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
   STORE SELECTED JOB FOR FINAL APPLICATION FLOW
   ===================================================== */

$_SESSION['selected_job_id'] = $job_id;
$_SESSION['selected_resume_id'] = $resume_id;
$_SESSION['selected_match_score'] = $match_score;

$_SESSION['chat_message'] = "
    Your resume match score for " . $job['title'] . " at " . $job['company'] . " is " . $match_score . "%.
    You are eligible to apply because your score is 25% or above.
";

/*
If your actual application is inserted from upload_resume.php or index.php chatbot flow,
keep redirecting to index.php.
*/

header("Location: index.php#chatBox");
exit;
?>