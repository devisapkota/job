<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo "Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$message = trim($_POST['skills'] ?? '');
$message_lower = strtolower($message);

/* ==========================
   QUICK BUTTON RESPONSES
   ========================== */

if ($message_lower == "check ats score") {

    $query = "
        SELECT 
            resumes.extracted_skills,
            resume_analysis.ats_score,
            resume_analysis.suggestions
        FROM resumes
        LEFT JOIN resume_analysis 
        ON resumes.resume_id = resume_analysis.resume_id
        WHERE resumes.user_id = '$user_id'
        ORDER BY resumes.resume_id DESC
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        echo "
        <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
            Please upload your resume first to check ATS score.
        </div>";
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    echo "
    <div style='background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;'>
        <h3>ATS Resume Analysis</h3>
        <b>ATS Score:</b> " . htmlspecialchars($row['ats_score']) . "/100<br>
        <b>Extracted Skills:</b> " . htmlspecialchars($row['extracted_skills']) . "<br>
        <b>Suggestions:</b> " . htmlspecialchars($row['suggestions']) . "
    </div>";
    exit;
}


if ($message_lower == "resume tips" || $message_lower == "improve my resume") {

    echo "
    <div style='background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;'>
        <h3>Resume Improvement Tips</h3>
        <ul>
            <li>Add a clear Skills section.</li>
            <li>Mention your education and experience clearly.</li>
            <li>Add projects related to your job field.</li>
            <li>Include email and phone number.</li>
            <li>Use job-related keywords like PHP, MySQL, Python, Excel, etc.</li>
            <li>Use a text-based PDF, not scanned image PDF.</li>
        </ul>
    </div>";
    exit;
}


if ($message_lower == "suggest jobs" || $message_lower == "suggest jobs for me") {

    $resumeQuery = mysqli_query($conn, "
        SELECT extracted_skills 
        FROM resumes 
        WHERE user_id = '$user_id'
        ORDER BY resume_id DESC
        LIMIT 1
    ");

    if (mysqli_num_rows($resumeQuery) == 0) {
        echo "
        <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
            Please upload your resume first to get job suggestions.
        </div>";
        exit;
    }

    $resume = mysqli_fetch_assoc($resumeQuery);
    $message = $resume['extracted_skills'];
}

/* ==========================
   VALID SKILL CHECK
   ========================== */

$known_skills = [
    "python", "java", "php", "html", "css", "javascript",
    "mysql", "sql", "flask", "django", "laravel",
    "react", "node.js", "bootstrap", "ajax",
    "machine learning", "data analysis", "excel",
    "git", "github", "c++", "c#"
];

$skills_lower = strtolower($message);
$valid_skill_found = false;

foreach ($known_skills as $skill) {
    if (strpos($skills_lower, $skill) !== false) {
        $valid_skill_found = true;
        break;
    }
}

if ($message == "" || !$valid_skill_found) {
    echo "
    <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;border:1px solid #fed7aa;'>
        <strong>⚠ Invalid Input</strong><br><br>
        Please enter your technical skills to get job recommendations.<br><br>
        <b>Example:</b><br>
        Python, HTML, CSS, MySQL
    </div>";
    exit;
}

/* ==========================
   JOB RECOMMENDATION
   ========================== */

$jobs = [];

$query = "SELECT * FROM jobs WHERE 1";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $jobs[] = $row;
}

if (count($jobs) == 0) {
    echo "No jobs available.";
    exit;
}

$data = [
    "user_id" => $user_id,
    "skills" => $message,
    "jobs" => $jobs
];

$ch = curl_init("http://127.0.0.1:5000/recommend");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo "AI server error. Please start Flask server.";
    exit;
}

$recommendations = json_decode($response, true);

if (!$recommendations || count($recommendations) == 0) {
    echo "Sorry, no matching jobs found.";
    exit;
}

echo "<h3>Recommended Jobs</h3>";

foreach ($recommendations as $job) {

    if ($job['match_score'] <= 0) {
        continue;
    }

    $missing = isset($job['missing_skills']) ? implode(", ", $job['missing_skills']) : "";

    echo "
    <div style='background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;'>
        <b>" . htmlspecialchars($job['title']) . "</b><br>
        Company: " . htmlspecialchars($job['company']) . "<br>
        Location: " . htmlspecialchars($job['location']) . "<br>
        Match Score: " . htmlspecialchars($job['match_score']) . "%<br>
        Missing Skills: " . htmlspecialchars($missing ?: "No major missing skills") . "<br><br>
        <a href='apply_suggested_job.php?job_id=" . htmlspecialchars($job['job_id']) . "&match_score=" . htmlspecialchars($job['match_score']) . "'>Apply Now</a>
    </div>";
}
?>