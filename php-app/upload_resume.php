<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo "Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$selected_job_id = $_SESSION['selected_job_id'] ?? 0;

$uploadDir = "uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
    echo "No valid resume uploaded.";
    exit;
}

$fileType = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $_FILES["resume"]["tmp_name"]);
finfo_close($finfo);

if ($fileType != "pdf" || $mimeType != "application/pdf") {
    echo "Only valid PDF resume is allowed.";
    exit;
}

$fileName = time() . "_" . basename($_FILES["resume"]["name"]);
$fileName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES["resume"]["tmp_name"], $filePath)) {
    echo "Resume upload failed.";
    exit;
}

$fullPath = realpath($filePath);

$data = ["file_path" => $fullPath];

$ch = curl_init("http://127.0.0.1:5000/parse-resume");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    unlink($filePath);
    echo "AI server error. Please start Flask server.";
    exit;
}

$result = json_decode($response, true);

if (!$result || !isset($result['success'])) {
    unlink($filePath);
    echo "AI server returned invalid response.";
    exit;
}

if ($result['success'] == false) {
    unlink($filePath);
    echo "Resume rejected: " . htmlspecialchars($result['message']);
    exit;
}

$extracted_skills_raw = $result['skills'] ?? [];
$skills_str = implode(", ", $extracted_skills_raw);
$ats_score = $result['ats_score'] ?? 0;
$suggestions_text = implode(", ", $result['suggestions'] ?? []);

mysqli_query($conn, "
    INSERT INTO resumes(user_id, file_name, file_path, extracted_skills)
    VALUES('$user_id', '$fileName', '$filePath', '$skills_str')
");

$resume_id = mysqli_insert_id($conn);

echo "
    <div style='background:white; padding:20px; border-radius:12px; border:1px solid #e5e7eb; margin-bottom:20px;'>
        <h3 style='color: #1d4ed8; margin-top:0;'>Resume Analysis Result</h3>
        <div style='display:flex; gap:20px; margin: 15px 0;'>
            <div style='background:#eff6ff; padding:15px; border-radius:12px; flex:1; text-align:center;'>
                <div style='font-size:24px; font-weight:800; color:#1d4ed8;'>$ats_score%</div>
                <div style='font-size:12px; color:#6b7280;'>ATS Compatibility</div>
            </div>
            <div style='background:#f0fdf4; padding:15px; border-radius:12px; flex:2;'>
                <div style='font-size:12px; font-weight:700; color:#166534;'>EXTRACTED SKILLS</div>
                <div style='font-size:13px; color:#374151; margin-top:4px;'>$skills_str</div>
            </div>
        </div>
        <div style='font-size:13px; color:#4b5563; line-height:1.6;'>
            <b>AI Suggestions:</b> " . (!empty($suggestions_text) ? htmlspecialchars($suggestions_text) : "Your resume is well-formatted for AI parsers.") . "
        </div>
    </div>
";

$user_skills = array_map('trim', array_map('strtolower', $extracted_skills_raw));

// Job Recommendations Logic
$jobs_query = mysqli_query($conn, "SELECT * FROM jobs");
$recommendations = [];

while ($job = mysqli_fetch_assoc($jobs_query)) {
    $job_skills_str = $job['required_skills'] ?? '';
    $job_skills = array_map('trim', array_map('strtolower', explode(",", $job_skills_str)));
    
    // Skill matching
    $matched_skills = array_intersect($user_skills, $job_skills);
    $match_count = count($matched_skills);
    
    $score = 0;
    if (count($job_skills) > 0) {
        $score = round(($match_count / count($job_skills)) * 100, 2);
    }

    // Boost score if title matches user skills
    foreach ($user_skills as $us) {
        if (strpos(strtolower($job['title']), $us) !== false) {
            $score += 10;
        }
    }

    if ($score > 100) $score = 100;
    
    if ($score > 0) {
        $job['match_score'] = $score;
        $recommendations[] = $job;
    }
}

// Sort by score
usort($recommendations, function($a, $b) {
    return $b['match_score'] <=> $a['match_score'];
});

echo "<h3 style='margin-bottom:15px; font-size:16px;'>AI Personalized Recommendations</h3>";

if (count($recommendations) > 0) {
    foreach (array_slice($recommendations, 0, 6) as $r) {
        $is_ext = isset($r['is_external']) && $r['is_external'];
        $source_label = $is_ext ? "Portal: " . $r['company'] : "Company: " . $r['company'];
        $badge_style = $is_ext ? "background:#eff6ff; color:#1d4ed8;" : "background:#f0fdf4; color:#166534;";
        
        echo "
            <div style='background:white; border:1px solid #e5e7eb; padding:16px; border-radius:12px; margin-bottom:12px; transition:0.2s;'>
                <div style='display:flex; justify-content:space-between; align-items:flex-start;'>
                    <div>
                        <div style='font-weight:700; color:#111827; font-size:14px;'>" . htmlspecialchars($r['title']) . "</div>
                        <div style='font-size:12px; color:#6b7280; margin-top:2px;'>$source_label • 📍 " . htmlspecialchars($r['location']) . "</div>
                    </div>
                    <div style='font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px; $badge_style'>
                        " . $r['match_score'] . "% Match
                    </div>
                </div>
                <div style='margin-top:12px; display:flex; justify-content:flex-end;'>
                    <a href='job_details.php?job_id=" . $r['job_id'] . "' style='font-size:13px; font-weight:600; color:#1d4ed8; text-decoration:none;'>View & Apply →</a>
                </div>
            </div>
        ";
    }
} else {
    echo "<div style='padding:20px; text-align:center; color:#6b7280; border:1px dashed #e5e7eb; border-radius:12px;'>No direct matches found. Try updating your skills.</div>";
}
?>