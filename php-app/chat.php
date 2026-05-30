<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo "Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$skills = $_POST['skills'] ?? '';
$location = $_POST['location'] ?? '';
$salary = $_POST['salary'] ?? '';

$jobs = [];

$query = "SELECT * FROM jobs WHERE 1";

if (!empty($location)) {
    $location = mysqli_real_escape_string($conn, $location);
    $query .= " AND location LIKE '%$location%'";
}

if (!empty($salary)) {
    $salary = floatval($salary);
    $query .= " AND salary >= $salary";
}

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $jobs[] = $row;
}

$data = [
    "user_id" => $user_id,
    "skills" => $skills,
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
    echo "
    <div style='
        background:#fee2e2;
        color:#991b1b;
        padding:18px;
        border-radius:16px;
        font-family:Segoe UI, sans-serif;
        border:1px solid #fecaca;
    '>
        <h3 style='margin-bottom:8px;'>AI Server Error</h3>
        <p>Please start your Flask AI server first.</p>
    </div>";
    exit;
}

$recommendations = json_decode($response, true);

if (!$recommendations || count($recommendations) == 0) {
    echo "
    <div style='
        background:white;
        padding:30px;
        border-radius:20px;
        text-align:center;
        font-family:Segoe UI, sans-serif;
        box-shadow:0 8px 25px rgba(0,0,0,0.06);
    '>
        <div style='font-size:45px;margin-bottom:12px;'>🔍</div>
        <h3 style='color:#1e3a8a;margin-bottom:8px;'>No Matching Jobs Found</h3>
        <p style='color:#64748b;'>Try adding more skills or changing your filters.</p>
    </div>";
    exit;
}

echo "
<style>
    .recommend-wrapper{
        font-family:'Segoe UI',sans-serif;
    }

    .recommend-header{
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        color:white;
        padding:22px;
        border-radius:20px;
        margin-bottom:22px;
        box-shadow:0 10px 25px rgba(37,99,235,0.25);
    }

    .recommend-header h3{
        margin:0 0 6px;
        font-size:24px;
    }

    .recommend-header p{
        margin:0;
        color:#dbeafe;
        font-size:14px;
    }

    .recommend-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
        gap:18px;
    }

    .recommend-card{
        background:white;
        border-radius:20px;
        padding:22px;
        border:1px solid #e2e8f0;
        box-shadow:0 8px 25px rgba(0,0,0,0.06);
        position:relative;
        overflow:hidden;
        transition:0.3s;
    }

    .recommend-card:hover{
        transform:translateY(-4px);
        box-shadow:0 15px 35px rgba(37,99,235,0.15);
    }

    .recommend-card::before{
        content:'';
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:5px;
        background:linear-gradient(90deg,#2563eb,#60a5fa);
    }

    .recommend-top{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:15px;
        margin-bottom:15px;
    }

    .recommend-title{
        font-size:22px;
        color:#1e3a8a;
        margin:0 0 5px;
    }

    .recommend-company{
        color:#64748b;
        font-size:14px;
        margin:0;
    }

    .score-badge{
        background:#dcfce7;
        color:#15803d;
        padding:9px 13px;
        border-radius:999px;
        font-weight:700;
        white-space:nowrap;
        font-size:14px;
    }

    .recommend-info{
        display:flex;
        flex-wrap:wrap;
        gap:9px;
        margin:15px 0;
    }

    .recommend-chip{
        background:#f8fafc;
        border:1px solid #e2e8f0;
        color:#334155;
        padding:8px 11px;
        border-radius:12px;
        font-size:13px;
    }

    .skill-gap{
        background:#fff7ed;
        border:1px solid #fed7aa;
        padding:13px;
        border-radius:14px;
        margin:15px 0;
        color:#9a3412;
        font-size:14px;
    }

    .skill-gap strong{
        display:block;
        margin-bottom:5px;
        color:#c2410c;
    }

    .apply-recommend-btn{
        display:block;
        text-align:center;
        background:linear-gradient(90deg,#2563eb,#3b82f6);
        color:white;
        text-decoration:none;
        padding:12px 20px;
        border-radius:12px;
        font-weight:600;
        margin-top:16px;
        transition:0.3s;
    }

    .apply-recommend-btn:hover{
        transform:translateY(-2px);
        box-shadow:0 10px 20px rgba(37,99,235,0.25);
    }
</style>

<div class='recommend-wrapper'>

    <div class='recommend-header'>
        <h3> Recommended Jobs</h3>
        <p>Based on your skills: " . htmlspecialchars($skills) . "</p>
    </div>

    <div class='recommend-grid'>
";

foreach ($recommendations as $job) {

    $job_id = $job['job_id'];
    $score = $job['match_score'];
    $missing = isset($job['missing_skills']) ? implode(", ", $job['missing_skills']) : "";

    mysqli_query($conn, "
        INSERT INTO recommendations(user_id, job_id, match_score, missing_skills)
        VALUES('$user_id', '$job_id', '$score', '$missing')
    ");

    echo "
        <div class='recommend-card'>

            <div class='recommend-top'>
                <div>
                    <h2 class='recommend-title'>" . htmlspecialchars($job['title']) . "</h2>
                    <p class='recommend-company'>" . htmlspecialchars($job['company']) . "</p>
                </div>

                <div class='score-badge'>
                    " . htmlspecialchars($score) . "% Match
                </div>
            </div>

            <div class='recommend-info'>
                <span class='recommend-chip'> " . htmlspecialchars($job['location']) . "</span>
                <span class='recommend-chip'> Rs. " . htmlspecialchars($job['salary']) . "</span>
            </div>
    ";

    if (!empty($missing)) {
        echo "
            <div class='skill-gap'>
                <strong>Skill Gap</strong>
                " . htmlspecialchars($missing) . "
            </div>
        ";
    } else {
        echo "
            <div class='skill-gap' style='background:#dcfce7;border-color:#bbf7d0;color:#166534;'>
                <strong style='color:#15803d;'>Skill Gap</strong>
                No major missing skills. You are a strong match.
            </div>
        ";
    }

    echo "
            <a href='apply_job.php?job_id=" . htmlspecialchars($job_id) . "' class='apply-recommend-btn'>
                Apply Now
            </a>

        </div>
    ";
}

echo "
    </div>
</div>
";
?>