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

if (!isset($_FILES['resume'])) {
    echo "No resume uploaded.";
    exit;
}

$fileType = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));

if ($fileType != "pdf") {
    echo "Only PDF resume is allowed.";
    exit;
}

$fileName = time() . "_" . basename($_FILES["resume"]["name"]);
$fileName = str_replace(" ", "_", $fileName);
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES["resume"]["tmp_name"], $filePath)) {
    echo "Resume upload failed.";
    exit;
}

$fullPath = realpath($filePath);

$data = [
    "file_path" => $fullPath
];

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

if (!$result) {
    unlink($filePath);
    echo "AI server returned invalid response.";
    exit;
}

if ($result['success'] == false) {
    unlink($filePath);
    echo "Resume rejected: " . htmlspecialchars($result['message']);
    exit;
}

$skills = implode(", ", $result['skills']);
$ats_score = $result['ats_score'] ?? 0;
$suggestions_text = implode(", ", $result['suggestions'] ?? []);

mysqli_query($conn, "
    INSERT INTO resumes(user_id, file_name, file_path, extracted_skills)
    VALUES('$user_id', '$fileName', '$filePath', '$skills')
");

$resume_id = mysqli_insert_id($conn);

mysqli_query($conn, "
    INSERT INTO resume_analysis(resume_id, ats_score, missing_skills, suggestions)
    VALUES('$resume_id', '$ats_score', '', '$suggestions_text')
");

echo "
    <h3>Resume Analysis Complete</h3>
    <b>Extracted Skills:</b> " . htmlspecialchars($skills) . "<br>
    <b>ATS Score:</b> " . htmlspecialchars($ats_score) . "/100<br>
    <b>ATS Suggestions:</b> " . htmlspecialchars($suggestions_text) . "<br><br>
";

if ($selected_job_id == 0) {
    echo "Resume uploaded successfully. Now select a job to apply.";
    exit;
}

$jobQuery = mysqli_query($conn, "SELECT * FROM jobs WHERE job_id='$selected_job_id'");
$job = mysqli_fetch_assoc($jobQuery);

if (!$job) {
    echo "Selected job not found.";
    exit;
}

$user_skills = array_map('trim', explode(",", strtolower($skills)));
$job_skills = array_map('trim', explode(",", strtolower($job['required_skills'])));

$matched = array_intersect($user_skills, $job_skills);
$missing = array_diff($job_skills, $user_skills);

$match_score = 0;

if (count($job_skills) > 0) {
    $match_score = round((count($matched) / count($job_skills)) * 100, 2);
}

echo "
    <hr>
    <h3>Applied Job Match Result</h3>
    <b>Job:</b> " . htmlspecialchars($job['title']) . "<br>
    <b>Company:</b> " . htmlspecialchars($job['company']) . "<br>
    <b>Match Score:</b> " . htmlspecialchars($match_score) . "%<br>
    <b>Missing Skills:</b> " . htmlspecialchars(implode(", ", $missing)) . "<br><br>
";

if ($match_score >= 60) {

    $check = mysqli_query($conn, "
        SELECT * FROM applications
        WHERE user_id='$user_id'
        AND job_id='$selected_job_id'
    ");

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "
            INSERT INTO applications(user_id, job_id, status)
            VALUES('$user_id', '$selected_job_id', 'Applied')
        ");
    }

    unset($_SESSION['selected_job_id']);

    echo "
        <div style='background:#dcfce7;color:#166534;padding:15px;border-radius:12px;margin-top:10px;'>
            ✅ Your skills match this job. Application submitted successfully.
        </div>
        <br>
        <a href='my_applications.php'>View My Applications</a>
    ";

} else {

    echo "
        <div style='background:#fff7ed;color:#9a3412;padding:15px;border-radius:12px;margin-top:10px;'>
            ⚠️ This job may not be suitable for your current skills. Suggested better jobs are shown below.
        </div>
        <br>
    ";

    $allJobs = mysqli_query($conn, "
        SELECT * FROM jobs
        WHERE job_id != '$selected_job_id'
    ");

    $suggestions = [];

    while ($otherJob = mysqli_fetch_assoc($allJobs)) {
        $other_skills = array_map('trim', explode(",", strtolower($otherJob['required_skills'])));
        $other_matched = array_intersect($user_skills, $other_skills);

        $other_score = 0;

        if (count($other_skills) > 0) {
            $other_score = round((count($other_matched) / count($other_skills)) * 100, 2);
        }

        if ($other_score > $match_score) {
            $otherJob['match_score'] = $other_score;
            $suggestions[] = $otherJob;
        }
    }

    usort($suggestions, function($a, $b) {
        return $b['match_score'] <=> $a['match_score'];
    });

    echo "<h3>Recommended Better Jobs</h3>";

    if (count($suggestions) > 0) {
        foreach ($suggestions as $sjob) {
            echo "
                <div style='background:white;border:1px solid #e2e8f0;padding:15px;border-radius:12px;margin:12px 0;'>
                    <b>" . htmlspecialchars($sjob['title']) . "</b><br>
                    Company: " . htmlspecialchars($sjob['company']) . "<br>
                    Location: " . htmlspecialchars($sjob['location']) . "<br>
                    Salary: Rs. " . htmlspecialchars($sjob['salary']) . "<br>
                    Match Score: " . htmlspecialchars($sjob['match_score']) . "%<br><br>
                    <a href='apply_job.php?job_id=" . $sjob['job_id'] . "'>Apply This Job</a>
                </div>
            ";
        }
    } else {
        echo "No better job suggestion found.";
    }
}
?>