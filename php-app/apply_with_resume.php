<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'] ?? 0;

if ($job_id == 0) {
    header("Location: user_dashboard.php");
    exit;
}

$jobResult = mysqli_query($conn, "SELECT * FROM jobs WHERE job_id='$job_id'");
$job = mysqli_fetch_assoc($jobResult);

if (!$job) {
    die("Job not found.");
}

$message = "";
$suggestions = [];
$match_score = null;
$missing_skills = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_FILES['resume'])) {
        $message = "Please upload your resume.";
    } else {

        $uploadDir = "uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileType = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));

        if ($fileType != "pdf") {
            $message = "Only PDF resume is allowed.";
        } else {

            $fileName = time() . "_" . basename($_FILES["resume"]["name"]);
            $fileName = str_replace(" ", "_", $fileName);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["resume"]["tmp_name"], $filePath)) {

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
                    $message = "AI server error. Please start Flask server.";
                } else {

                    $result = json_decode($response, true);

                    if (!$result || $result['success'] == false) {
                        unlink($filePath);
                        $message = "Resume rejected: " . htmlspecialchars($result['message']);
                    } else {

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

                        $user_skills = array_map('trim', explode(",", strtolower($skills)));
                        $job_skills = array_map('trim', explode(",", strtolower($job['required_skills'])));

                        $matched = array_intersect($user_skills, $job_skills);
                        $missing_skills = array_diff($job_skills, $user_skills);

                        if (count($job_skills) > 0) {
                            $match_score = round((count($matched) / count($job_skills)) * 100, 2);
                        } else {
                            $match_score = 0;
                        }

                        if ($match_score >= 60) {

                            $check = mysqli_query($conn, "
                                SELECT * FROM applications
                                WHERE user_id='$user_id'
                                AND job_id='$job_id'
                            ");

                            if (mysqli_num_rows($check) == 0) {
                                mysqli_query($conn, "
                                    INSERT INTO applications(user_id, job_id, status)
                                    VALUES('$user_id', '$job_id', 'Applied')
                                ");
                                $application_id = mysqli_insert_id($conn);
                                $admin_name = $_SESSION['name'] ?? "User";
                                $notif_msg = "New application received for " . $job['title'] . " from " . $admin_name;
                                mysqli_query($conn, "INSERT INTO admin_notifications (job_id, application_id, user_id, message) VALUES ('$job_id', '$application_id', '$user_id', '$notif_msg')");
                            }

                            $_SESSION['chat_message'] = "Job applied successfully. Match Score: $match_score%";
                            header("Location: index.php");
                            exit;

                        } else {

                            $allJobs = mysqli_query($conn, "
                                SELECT * FROM jobs 
                                WHERE job_id != '$job_id'
                            ");

                            while ($otherJob = mysqli_fetch_assoc($allJobs)) {

                                $other_skills = array_map('trim', explode(",", strtolower($otherJob['required_skills'])));
                                $other_matched = array_intersect($user_skills, $other_skills);

                                if (count($other_skills) > 0) {
                                    $other_score = round((count($other_matched) / count($other_skills)) * 100, 2);
                                } else {
                                    $other_score = 0;
                                }

                                if ($other_score > $match_score) {
                                    $otherJob['match_score'] = $other_score;
                                    $suggestions[] = $otherJob;
                                }
                            }

                            usort($suggestions, function($a, $b) {
                                return $b['match_score'] <=> $a['match_score'];
                            });
                        }
                    }
                }
            } else {
                $message = "Resume upload failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply With Resume</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            background:#f4f8ff;
            color:#1e293b;
        }

        .container{
            width:90%;
            max-width:1100px;
            margin:40px auto;
        }

        .card{
            background:white;
            padding:32px;
            border-radius:24px;
            box-shadow:0 8px 25px rgba(0,0,0,0.06);
            margin-bottom:25px;
        }

        h1{
            color:#1e3a8a;
            margin-bottom:10px;
        }

        p{
            color:#475569;
            line-height:1.7;
            margin-bottom:12px;
        }

        .job-box{
            border-left:6px solid #2563eb;
        }

        .upload-box{
            border-left:6px solid #16a34a;
        }

        input[type="file"]{
            width:100%;
            padding:16px;
            border:2px dashed #93c5fd;
            border-radius:16px;
            background:#eff6ff;
            margin:18px 0;
        }

        button,
        .btn{
            display:inline-block;
            border:none;
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
            padding:13px 24px;
            border-radius:12px;
            font-weight:700;
            text-decoration:none;
            cursor:pointer;
        }

        .btn-red{
            background:#ef4444;
        }

        .message{
            background:#fee2e2;
            color:#991b1b;
            padding:16px;
            border-radius:14px;
            margin-bottom:20px;
        }

        .warning{
            background:#fff7ed;
            border-left:6px solid #f97316;
        }

        .score{
            display:inline-block;
            background:#ffedd5;
            color:#c2410c;
            padding:10px 16px;
            border-radius:999px;
            font-weight:700;
            margin:12px 0;
        }

        .job-list{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:22px;
        }

        .suggest-card{
            background:white;
            padding:24px;
            border-radius:20px;
            box-shadow:0 8px 25px rgba(0,0,0,0.06);
            border:1px solid #e2e8f0;
        }

        .match{
            display:inline-block;
            background:#dcfce7;
            color:#15803d;
            padding:8px 14px;
            border-radius:999px;
            font-weight:700;
            margin-bottom:12px;
        }

        .top-link{
            margin-bottom:20px;
            display:inline-block;
            color:#2563eb;
            text-decoration:none;
            font-weight:700;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="user_dashboard.php" class="top-link">← Back to Jobs</a>

    <?php if ($message != "") { ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <div class="card job-box">
        <h1>Apply for <?php echo htmlspecialchars($job['title']); ?></h1>

        <p><b>Company:</b> <?php echo htmlspecialchars($job['company']); ?></p>
        <p><b>Location:</b> <?php echo htmlspecialchars($job['location']); ?></p>
        <p><b>Salary:</b> Rs. <?php echo htmlspecialchars($job['salary']); ?></p>
        <p><b>Required Skills:</b> <?php echo htmlspecialchars($job['required_skills']); ?></p>
    </div>

    <?php if ($match_score === null) { ?>

        <div class="card upload-box">
            <h1>Upload Resume to Continue</h1>

            <p>
                Please upload your resume. The system will extract your skills and check whether this job is suitable for you.
            </p>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="resume" accept="application/pdf" required>
                <button type="submit">Upload Resume & Check Match</button>
            </form>
        </div>

    <?php } else { ?>

        <div class="card warning">
            <h1>This job may not be suitable</h1>

            <p>
                Your resume skills do not strongly match this job.
            </p>

            <div class="score">
                Match Score: <?php echo $match_score; ?>%
            </div>

            <p>
                <b>Missing Skills:</b>
                <?php echo htmlspecialchars(implode(", ", $missing_skills)); ?>
            </p>
        </div>

        <h1 style="margin-bottom:20px;">Suggested Better Jobs</h1>

        <div class="job-list">

            <?php if (count($suggestions) > 0) { ?>

                <?php foreach ($suggestions as $sjob) { ?>

                    <div class="suggest-card">
                        <div class="match">
                            <?php echo $sjob['match_score']; ?>% Match
                        </div>

                        <h2><?php echo htmlspecialchars($sjob['title']); ?></h2>

                        <p><b>Company:</b> <?php echo htmlspecialchars($sjob['company']); ?></p>
                        <p><b>Location:</b> <?php echo htmlspecialchars($sjob['location']); ?></p>
                        <p><b>Salary:</b> Rs. <?php echo htmlspecialchars($sjob['salary']); ?></p>
                        <p><b>Required Skills:</b> <?php echo htmlspecialchars($sjob['required_skills']); ?></p>

                        <a href="apply_job.php?job_id=<?php echo $sjob['job_id']; ?>" class="btn">
                            Apply This Job
                        </a>
                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="suggest-card">
                    <h2>No better jobs found</h2>
                    <p>You can go back and browse other jobs.</p>
                </div>

            <?php } ?>

        </div>

    <?php } ?>

</div>

</body>
</html>