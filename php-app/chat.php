<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    echo "
    <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;border:1px solid #fed7aa;'>
        Please login first to use CareerPilot AI.
    </div>";
    exit;
}

$user_id = intval($_SESSION['user_id']);
$message = trim($_POST['skills'] ?? '');
$message_lower = strtolower($message);

/* =====================================================
   CHATBOT PAYMENT CHECK AFTER 5 FREE USER MESSAGES
   ===================================================== */

$countQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total_messages
    FROM chat_messages
    WHERE user_id = '$user_id'
    AND sender = 'user'
");

$countData = mysqli_fetch_assoc($countQuery);
$totalUserMessages = intval($countData['total_messages'] ?? 0);

$paidQuery = mysqli_query($conn, "
    SELECT *
    FROM chatbot_access
    WHERE user_id = '$user_id'
    AND is_paid = 1
    LIMIT 1
");

$hasPaidAccess = ($paidQuery && mysqli_num_rows($paidQuery) > 0);

if ($totalUserMessages >= 5 && !$hasPaidAccess) {
    echo "
    <div style='background:#fff7ed;color:#9a3412;padding:18px;border-radius:14px;border:1px solid #fed7aa;'>
        <h3 style='margin-top:0;'>Chatbot Limit Reached</h3>
        <p>You have used your 5 free CareerPilot AI messages.</p>
        <p>Please unlock Chatbot Pro Access from Pro Features to continue chatting.</p>

        <a href='pro_features.php' 
           style='display:inline-block;background:#2563eb;color:white;padding:12px 18px;border-radius:10px;text-decoration:none;font-weight:700;margin-top:10px;'>
            Go to Pro Features
        </a>
    </div>";
    exit;
}

/* =====================================================
   SAVE USER MESSAGE
   ===================================================== */

if ($message != "") {
    $safe_message = mysqli_real_escape_string($conn, $message);

    mysqli_query($conn, "
        INSERT INTO chat_messages
        (user_id, sender, message, created_at)
        VALUES
        ('$user_id', 'user', '$safe_message', NOW())
    ");
}

/* =====================================================
   QUICK BUTTON RESPONSES
   ===================================================== */

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

    if (!$result || mysqli_num_rows($result) == 0) {
        $reply = "
        <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
            Please upload your resume first to check ATS score.
        </div>";

        saveBotMessage($conn, $user_id, $reply);
        echo $reply;
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    $reply = "
    <div style='background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;'>
        <h3>ATS Resume Analysis</h3>
        <b>ATS Score:</b> " . htmlspecialchars($row['ats_score'] ?? 'Not available') . "/100<br>
        <b>Extracted Skills:</b> " . htmlspecialchars($row['extracted_skills'] ?? 'Not available') . "<br>
        <b>Suggestions:</b> " . htmlspecialchars($row['suggestions'] ?? 'No suggestions available') . "
    </div>";

    saveBotMessage($conn, $user_id, $reply);
    echo $reply;
    exit;
}

if ($message_lower == "resume tips" || $message_lower == "improve my resume") {

    $reply = "
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

    saveBotMessage($conn, $user_id, $reply);
    echo $reply;
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

    if (!$resumeQuery || mysqli_num_rows($resumeQuery) == 0) {
        $reply = "
        <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
            Please upload your resume first to get job suggestions.
        </div>";

        saveBotMessage($conn, $user_id, $reply);
        echo $reply;
        exit;
    }

    $resume = mysqli_fetch_assoc($resumeQuery);
    $message = $resume['extracted_skills'];
}

/* =====================================================
   VALID SKILL CHECK
   ===================================================== */

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
    $reply = "
    <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;border:1px solid #fed7aa;'>
        <strong>⚠ Invalid Input</strong><br><br>
        Please enter your technical skills to get job recommendations.<br><br>
        <b>Example:</b><br>
        Python, HTML, CSS, MySQL
    </div>";

    saveBotMessage($conn, $user_id, $reply);
    echo $reply;
    exit;
}

/* =====================================================
   JOB RECOMMENDATION
   ===================================================== */

$jobs = [];

$query = "SELECT * FROM jobs";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $jobs[] = $row;
    }
}

if (count($jobs) == 0) {
    $reply = "
    <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
        No jobs available.
    </div>";

    saveBotMessage($conn, $user_id, $reply);
    echo $reply;
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
    $reply = "
    <div style='background:#fef2f2;color:#991b1b;padding:15px;border-radius:12px;border:1px solid #fecaca;'>
        AI server error. Please start Flask server.
    </div>";

    saveBotMessage($conn, $user_id, $reply);
    echo $reply;
    exit;
}

$recommendations = json_decode($response, true);

if (!$recommendations || count($recommendations) == 0) {
    $reply = "
    <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
        Sorry, no matching jobs found.
    </div>";

    saveBotMessage($conn, $user_id, $reply);
    echo $reply;
    exit;
}

/* =====================================================
   SHOW ONLY RELEVANT JOBS
   Minimum match score required = 25%
   ===================================================== */

$reply = "<h3>Recommended Jobs</h3>";
$hasRecommendation = false;

foreach ($recommendations as $job) {

    $raw_match_score = isset($job['match_score']) ? floatval($job['match_score']) : 0;

    if ($raw_match_score < 25) {
        continue;
    }

    $hasRecommendation = true;

    $missing = isset($job['missing_skills']) && is_array($job['missing_skills']) 
        ? implode(", ", $job['missing_skills']) 
        : "";

    $job_id = intval($job['job_id']);
    $match_score = number_format($raw_match_score, 2);

    $reply .= "
    <div style='background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;'>
        <b>" . htmlspecialchars($job['title']) . "</b><br>
        Company: " . htmlspecialchars($job['company']) . "<br>
        Location: " . htmlspecialchars($job['location']) . "<br>
        Match Score: " . htmlspecialchars($match_score) . "%<br>
        Missing Skills: " . htmlspecialchars($missing ?: "No major missing skills") . "<br><br>

        <a href='job_details.php?job_id=" . $job_id . "&match_score=" . urlencode($match_score) . "' 
           style='background:#0f172a;color:white;padding:9px 13px;border-radius:8px;text-decoration:none;font-weight:600;margin-right:8px;'>
            View Details
        </a>

        <a href='apply_job.php?job_id=" . $job_id . "&match_score=" . urlencode($match_score) . "' 
           style='background:#2563eb;color:white;padding:9px 13px;border-radius:8px;text-decoration:none;font-weight:600;'>
            Apply Now
        </a>
    </div>";
}

if (!$hasRecommendation) {
    $reply = "
    <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;'>
        Sorry, no suitable jobs found with at least 25% match score.<br>
        Please improve your resume skills or try different technical skills.
    </div>";
}

saveBotMessage($conn, $user_id, $reply);
echo $reply;
exit;


/* =====================================================
   FUNCTION TO SAVE BOT MESSAGE
   ===================================================== */

function saveBotMessage($conn, $user_id, $reply) {
    $safe_reply = mysqli_real_escape_string($conn, strip_tags($reply));

    mysqli_query($conn, "
        INSERT INTO chat_messages
        (user_id, sender, message, created_at)
        VALUES
        ('$user_id', 'bot', '$safe_reply', NOW())
    ");
}
?>