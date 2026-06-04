<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'] ?? 1;
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
curl_close($ch);

$recommendations = json_decode($response, true);

if (!$recommendations || count($recommendations) == 0) {
    echo "Sorry, no matching jobs found.";
    exit;
}

echo "<h3>Recommended Jobs</h3>";

foreach ($recommendations as $job) {
    $job_id = $job['job_id'];
    $score = $job['match_score'];
    $missing = implode(", ", $job['missing_skills']);

    mysqli_query($conn, "
        INSERT INTO recommendations(user_id, job_id, match_score, missing_skills)
        VALUES('$user_id', '$job_id', '$score', '$missing')
    ");

    echo "
    <div class='job-card'>
        <b>{$job['title']}</b><br>
        Company: {$job['company']}<br>
        Location: {$job['location']}<br>
        Salary: Rs. {$job['salary']}<br>
        Match Score: {$score}%<br>
        Missing Skills: {$missing}
    </div><hr>";
}
?>