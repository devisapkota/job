<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$applied_job_id = $_GET['job_id'] ?? 0;

if ($applied_job_id == 0) {
    header("Location: user_dashboard.php");
    exit;
}

/* Get latest uploaded resume skills */
$resumeQuery = mysqli_query($conn, "
    SELECT extracted_skills 
    FROM resumes 
    WHERE user_id = '$user_id'
    ORDER BY resume_id DESC 
    LIMIT 1
");

if (mysqli_num_rows($resumeQuery) == 0) {
    echo "Please upload your resume first to get job suggestions.";
    exit;
}

$resume = mysqli_fetch_assoc($resumeQuery);
$user_skills = array_map('trim', explode(",", strtolower($resume['extracted_skills'])));

/* Get applied job */
$appliedJobQuery = mysqli_query($conn, "
    SELECT * FROM jobs 
    WHERE job_id = '$applied_job_id'
");

$appliedJob = mysqli_fetch_assoc($appliedJobQuery);

if (!$appliedJob) {
    echo "Applied job not found.";
    exit;
}

$required_skills = array_map('trim', explode(",", strtolower($appliedJob['required_skills'])));

$matched = array_intersect($user_skills, $required_skills);
$missing = array_diff($required_skills, $user_skills);

$match_score = 0;

if (count($required_skills) > 0) {
    $match_score = round((count($matched) / count($required_skills)) * 100, 2);
}

/* If score is good */
if ($match_score >= 60) {
    header("Location: my_applications.php?msg=suitable");
    exit;
}

/* Get all other jobs */
$jobsQuery = mysqli_query($conn, "
    SELECT * FROM jobs 
    WHERE job_id != '$applied_job_id'
");

$suggestions = [];

while ($job = mysqli_fetch_assoc($jobsQuery)) {

    $jobSkills = array_map('trim', explode(",", strtolower($job['required_skills'])));

    $jobMatched = array_intersect($user_skills, $jobSkills);

    $score = 0;

    if (count($jobSkills) > 0) {
        $score = round((count($jobMatched) / count($jobSkills)) * 100, 2);
    }

    if ($score > $match_score) {
        $job['match_score'] = $score;
        $suggestions[] = $job;
    }
}

usort($suggestions, function($a, $b) {
    return $b['match_score'] <=> $a['match_score'];
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suggested Jobs</title>
    <style>
        body{
            font-family:'Segoe UI',sans-serif;
            background:#f4f8ff;
            margin:0;
            color:#1e293b;
        }

        .container{
            width:90%;
            max-width:1100px;
            margin:40px auto;
        }

        .warning-box{
            background:white;
            border-radius:24px;
            padding:35px;
            margin-bottom:30px;
            box-shadow:0 8px 25px rgba(0,0,0,0.06);
            border-left:7px solid #f97316;
        }

        .warning-box h1{
            color:#c2410c;
            margin-bottom:12px;
        }

        .warning-box p{
            color:#475569;
            line-height:1.7;
        }

        .score-badge{
            display:inline-block;
            background:#ffedd5;
            color:#c2410c;
            padding:10px 16px;
            border-radius:999px;
            font-weight:700;
            margin-top:15px;
        }

        .missing-box{
            margin-top:18px;
            background:#fff7ed;
            padding:16px;
            border-radius:16px;
            color:#9a3412;
        }

        .job-list{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
            gap:25px;
        }

        .job-card{
            background:white;
            padding:25px;
            border-radius:22px;
            box-shadow:0 8px 25px rgba(0,0,0,0.06);
            border:1px solid #e2e8f0;
            transition:.3s;
        }

        .job-card:hover{
            transform:translateY(-5px);
            box-shadow:0 15px 35px rgba(37,99,235,0.15);
        }

        .job-card h2{
            color:#1e3a8a;
            margin-bottom:8px;
        }

        .company{
            color:#64748b;
            margin-bottom:15px;
        }

        .match{
            display:inline-block;
            background:#dcfce7;
            color:#15803d;
            padding:8px 13px;
            border-radius:999px;
            font-weight:700;
            margin-bottom:15px;
        }

        .btn{
            display:inline-block;
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
            padding:12px 20px;
            border-radius:12px;
            text-decoration:none;
            font-weight:700;
            margin-top:15px;
        }

        .btn-red{
            background:#ef4444;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="warning-box">
        <h1>This job may not be the best match</h1>

        <p>
            You applied for <b><?php echo htmlspecialchars($appliedJob['title']); ?></b>,
            but your current resume skills do not strongly match this job.
        </p>

        <div class="score-badge">
            Current Match Score: <?php echo $match_score; ?>%
        </div>

        <div class="missing-box">
            <b>Missing Skills:</b>
            <?php echo htmlspecialchars(implode(", ", $missing)); ?>
        </div>

        <br>

        <a href="my_applications.php" class="btn-red btn">
            Continue Anyway
        </a>

        <a href="user_dashboard.php" class="btn">
            Browse All Jobs
        </a>
    </div>

    <h2 style="margin-bottom:20px;color:#1e3a8a;">
        Suggested Better Jobs For You
    </h2>

    <div class="job-list">

        <?php if (count($suggestions) > 0) { ?>

            <?php foreach ($suggestions as $job) { ?>

                <div class="job-card">
                    <span class="match">
                        <?php echo $job['match_score']; ?>% Match
                    </span>

                    <h2><?php echo htmlspecialchars($job['title']); ?></h2>

                    <p class="company">
                        <?php echo htmlspecialchars($job['company']); ?>
                    </p>

                    <p>
                        <b>Location:</b>
                        <?php echo htmlspecialchars($job['location']); ?>
                    </p>

                    <p>
                        <b>Salary:</b>
                        Rs. <?php echo htmlspecialchars($job['salary']); ?>
                    </p>

                    <p>
                        <b>Required Skills:</b>
                        <?php echo htmlspecialchars($job['required_skills']); ?>
                    </p>

                    <a href="apply_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn">
                        Apply This Job
                    </a>
                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="job-card">
                <h2>No better suggestion found</h2>
                <p>You can continue with your selected job or browse all jobs.</p>
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>