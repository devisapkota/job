<?php
session_start();
include "db.php";

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$user_id = $_SESSION['user_id'] ?? 0;

if ($job_id == 0) {
    header("Location: index.php");
    exit;
}

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Job not found.";
    exit;
}

$job = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['title']); ?> | CareerPilot AI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .job-details-card {
            background: white;
            border-radius: var(--r16);
            padding: 32px;
            box-shadow: var(--s1);
            margin: 20px;
        }
        .job-header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .job-header h1 { font-size: 1.8rem; color: var(--text); margin-bottom: 5px; }
        .job-header .company-name { font-size: 1.1rem; color: var(--blue); font-weight: 600; }
        
        .job-info-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--text2);
        }
        .info-item svg { color: var(--text3); }

        .job-section { margin-bottom: 25px; }
        .job-section h3 { font-size: 1.1rem; margin-bottom: 12px; color: var(--text); }
        .job-section p { line-height: 1.7; color: var(--text2); }
        
        .skills-tags { display: flex; flex-wrap: wrap; gap: 8px; }
        .skill-tag {
            background: var(--blue-light);
            color: var(--blue);
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .apply-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
    </style>
</head>

<body class="app-page">

<div class="app-layout">

<?php include "sidebar.php"; ?>

<main class="app-main">
    <div class="job-details-card">
        <div class="job-header">
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <div class="company-name"><?php echo htmlspecialchars($job['company']); ?></div>
        </div>

        <div class="job-info-row">
            <div class="info-item">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo htmlspecialchars($job['location']); ?>
            </div>
            <div class="info-item">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                Rs. <?php echo ($job['salary'] > 0) ? htmlspecialchars($job['salary']) : "Negotiable"; ?>
            </div>
        </div>

        <div class="job-section">
            <h3>Required Skills</h3>
            <div class="skills-tags">
                <?php 
                $skills = explode(",", $job['required_skills']);
                foreach ($skills as $skill) {
                    if (trim($skill) != "") {
                        echo '<span class="skill-tag">' . htmlspecialchars(trim($skill)) . '</span>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="job-section">
            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
        </div>

        <div class="apply-actions">
            <?php if ($user_id): ?>
                <a href="apply_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn-primary" style="display:inline-block; width:auto; padding: 12px 30px;">Apply for this Job</a>
            <?php else: ?>
                <a href="login.php?redirect=apply_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn-primary" style="display:inline-block; width:auto; padding: 12px 30px;">Login to Apply</a>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>

</body>
</html>
