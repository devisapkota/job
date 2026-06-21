<?php
session_start();
require_once "db.php";

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

$match_score = null;

if (isset($_GET['match_score']) && $_GET['match_score'] !== '') {
    $match_score = floatval($_GET['match_score']);
}

if ($job_id <= 0) {
    die("Invalid job selected.");
}

/* =====================================================
   HELPER FUNCTION TO NORMALIZE SKILLS
   ===================================================== */

function normalizeSkills($skillsText) {
    $skillsText = strtolower($skillsText ?? '');

    $skills = preg_split('/[,|\/\n\r]+/', $skillsText);

    $cleanSkills = [];

    foreach ($skills as $skill) {
        $skill = trim($skill);

        if ($skill !== '') {
            $cleanSkills[] = $skill;
        }
    }

    return array_unique($cleanSkills);
}

/* =====================================================
   FETCH JOB
   ===================================================== */

$stmt = $conn->prepare("
    SELECT *
    FROM jobs
    WHERE job_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Job not found.");
}

$job = $result->fetch_assoc();

/* =====================================================
   RECALCULATE MATCH SCORE IF URL SCORE IS MISSING
   ===================================================== */

if ($match_score === null && isset($_SESSION['user_id'])) {

    $user_id = intval($_SESSION['user_id']);

    $resumeStmt = $conn->prepare("
        SELECT extracted_skills
        FROM resumes
        WHERE user_id = ?
        ORDER BY resume_id DESC
        LIMIT 1
    ");

    $resumeStmt->bind_param("i", $user_id);
    $resumeStmt->execute();
    $resumeResult = $resumeStmt->get_result();

    if ($resumeResult->num_rows > 0) {

        $resume = $resumeResult->fetch_assoc();

        $resume_skills = normalizeSkills($resume['extracted_skills'] ?? '');
        $required_skills = normalizeSkills($job['required_skills'] ?? '');

        $matched_count = 0;

        foreach ($required_skills as $requiredSkill) {
            foreach ($resume_skills as $resumeSkill) {
                if ($requiredSkill === $resumeSkill) {
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

        /* Same title bonus as job_suggestions.php */
        foreach ($resume_skills as $skill) {
            if ($skill !== '' && strpos(strtolower($job['title']), $skill) !== false) {
                $match_score += 15;
            }
        }

        if ($match_score > 100) {
            $match_score = 100;
        }
    }
}

/* If still null, keep as not calculated */
$can_apply = true;
$apply_message = "";

if ($match_score !== null && $match_score < 25) {
    $can_apply = false;
    $apply_message = "You cannot apply for this job because your match score is below 25%. Please improve your resume skills first.";
}

$salary_text = "Negotiable / Not Disclosed";

if (!empty($job['salary']) && $job['salary'] != 0 && $job['salary'] != "0.00") {
    $salary_text = "Rs. " . htmlspecialchars($job['salary']);
}

$company = $job['company'] ?? "Not specified";
$location = $job['location'] ?? "Not specified";
$required_skills = $job['required_skills'] ?? "Not specified";
$description = $job['description'] ?? "No description available.";
$is_external = intval($job['is_external'] ?? 0);

$apply_score = $match_score !== null ? $match_score : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Job Details | CareerPilot AI</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f8fafc;
    color: #0f172a;
}

.page {
    max-width: 950px;
    margin: 45px auto;
    padding: 20px;
}

.card {
    background: #ffffff;
    padding: 35px;
    border-radius: 22px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    border: 1px solid #e5e7eb;
}

h1 {
    font-size: 34px;
    color: #1d4ed8;
    margin-bottom: 8px;
}

.meta {
    color: #64748b;
    font-size: 16px;
    margin-bottom: 25px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.info-box {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    padding: 16px;
    border-radius: 14px;
}

.info-box b {
    display: block;
    color: #475569;
    margin-bottom: 7px;
    font-size: 14px;
}

.info-box span {
    font-weight: 700;
    color: #111827;
}

.match-good {
    color: #16a34a !important;
}

.match-bad {
    color: #dc2626 !important;
}

.description {
    line-height: 1.7;
    color: #334155;
    background: #f8fafc;
    padding: 18px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    margin-top: 20px;
}

.warning {
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fed7aa;
    padding: 15px;
    border-radius: 12px;
    margin-top: 20px;
    font-weight: 600;
}

.btn-row {
    margin-top: 25px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 13px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 800;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-dark {
    background: #0f172a;
    color: white;
}

.btn-disabled {
    background: #94a3b8;
    color: white;
    cursor: not-allowed;
}
</style>
</head>

<body>

<div class="page">
    <div class="card">

        <h1><?php echo htmlspecialchars($job['title']); ?></h1>

        <div class="meta">
            <?php echo htmlspecialchars($company); ?> • 
            <?php echo htmlspecialchars($location); ?>
        </div>

        <div class="info-grid">

            <div class="info-box">
                <b>Salary</b>
                <span><?php echo $salary_text; ?></span>
            </div>

            <div class="info-box">
                <b>Required Skills</b>
                <span><?php echo htmlspecialchars($required_skills); ?></span>
            </div>

            <div class="info-box">
                <b>Job Type</b>
                <span>
                    <?php echo $is_external == 1 ? "External / Scraped Job" : "Internal Job"; ?>
                </span>
            </div>

            <div class="info-box">
                <b>Your Match Score</b>

                <?php if ($match_score !== null) { ?>
                    <span class="<?php echo $match_score >= 25 ? 'match-good' : 'match-bad'; ?>">
                        <?php echo htmlspecialchars(number_format($match_score, 2)); ?>%
                    </span>
                <?php } else { ?>
                    <span>Not calculated</span>
                <?php } ?>
            </div>

        </div>

        <div class="description">
            <b>Job Description:</b><br><br>
            <?php echo nl2br(htmlspecialchars($description)); ?>
        </div>

        <?php if (!$can_apply) { ?>
            <div class="warning">
                <?php echo htmlspecialchars($apply_message); ?>
            </div>
        <?php } ?>

        <div class="btn-row">
            <a href="job_suggestions.php" class="btn btn-dark">Back to Suggestions</a>

            <?php if ($can_apply) { ?>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <a href="apply_job.php?job_id=<?php echo $job_id; ?>&match_score=<?php echo urlencode($apply_score); ?>" class="btn btn-primary">
                        Apply Now
                    </a>
                <?php } else { ?>
                    <a href="login.php?redirect=<?php echo urlencode('apply_job.php?job_id=' . $job_id . '&match_score=' . $apply_score); ?>" class="btn btn-primary">
                        Login to Apply
                    </a>
                <?php } ?>
            <?php } else { ?>
                <span class="btn btn-disabled">Apply Not Allowed</span>
            <?php } ?>
        </div>

    </div>
</div>

</body>
</html>