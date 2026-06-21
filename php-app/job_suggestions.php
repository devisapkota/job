<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=job_suggestions.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

/* Get latest uploaded resume skills */
$resumeQuery = mysqli_query($conn, "
    SELECT extracted_skills 
    FROM resumes 
    WHERE user_id = '$user_id'
    ORDER BY resume_id DESC 
    LIMIT 1
");

$has_resume = ($resumeQuery && mysqli_num_rows($resumeQuery) > 0);

$user_skills = [];

if ($has_resume) {
    $resume = mysqli_fetch_assoc($resumeQuery);
    $raw_skills = strtolower($resume['extracted_skills'] ?? '');

    $user_skills = preg_split('/[,|\/]+/', $raw_skills);
    $user_skills = array_map('trim', $user_skills);
    $user_skills = array_filter($user_skills);
}

/* Fetch jobs and calculate match score */
$jobsQuery = mysqli_query($conn, "SELECT * FROM jobs");
$suggestions = [];

if ($has_resume && $jobsQuery) {

    while ($job = mysqli_fetch_assoc($jobsQuery)) {

        $required_skills_raw = strtolower($job['required_skills'] ?? '');

        $job_skills = preg_split('/[,|\/]+/', $required_skills_raw);
        $job_skills = array_map('trim', $job_skills);
        $job_skills = array_filter($job_skills);

        $matched = array_intersect($user_skills, $job_skills);

        $score = 0;

        if (count($job_skills) > 0) {
            $score = round((count($matched) / count($job_skills)) * 100, 2);
        }

        /* Small bonus if skill appears in job title */
        foreach ($user_skills as $skill) {
            if (!empty($skill) && strpos(strtolower($job['title']), $skill) !== false) {
                $score += 15;
            }
        }

        if ($score > 100) {
            $score = 100;
        }

        /*
        Only show resume-related jobs.
        Minimum match score required = 25%.
        */
        if ($score >= 25) {
            $job['match_score'] = $score;
            $job['matched_skills'] = implode(", ", $matched);
            $suggestions[] = $job;
        }
    }

    usort($suggestions, function($a, $b) {
        return $b['match_score'] <=> $a['match_score'];
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Recommendations | CareerPilot AI</title>
<link rel="stylesheet" href="style.css">

<style>
.jcard {
    cursor: pointer;
}

.match-badge {
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
}

.view-link {
    color: var(--blue);
    font-size: 0.75rem;
    font-weight: 800;
    text-decoration: none;
}

.view-link:hover {
    text-decoration: underline;
}

.matched-skills {
    font-size: 12px;
    color: var(--text3);
    background: #f8fafc;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 12px;
    border: 1px solid var(--border);
}
</style>
</head>

<body class="app-page">

<div class="app-layout">

    <?php include "sidebar.php"; ?>

    <main class="app-main">
        <div class="dash-content">

            <div class="welcome-banner">
                <div class="wb-left">
                    <h2>AI Job Recommendations</h2>
                    <p style="font-size:0.9rem; color:var(--text3);">
                        Personalized career opportunities based on your parsed resume skills.
                    </p>
                </div>
            </div>

            <?php if (!$has_resume): ?>

                <div style="text-align:center; padding:60px; background:white; border-radius:var(--r16); border:1px dashed var(--border);">
                    <div style="font-size:3rem; margin-bottom:20px;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <h3>No Resume Found</h3>
                    <p style="color:var(--text3); margin-bottom:24px;">
                        Please upload your resume in the AI Assistant chat to unlock personalized suggestions.
                    </p>
                    <a href="index.php" class="btn-primary" style="display:inline-flex; width:auto;">
                        Go to AI Assistant
                    </a>
                </div>

            <?php else: ?>

                <div class="section-head">
                    <h3>Top Matches for Your Profile</h3>
                    <span style="font-size:0.8rem; color:var(--text4);">
                        Based on: <?php echo htmlspecialchars(implode(", ", array_slice($user_skills, 0, 5))); ?>...
                    </span>
                </div>

                <div class="job-cards-row" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">

                    <?php if (count($suggestions) > 0): ?>

                        <?php foreach ($suggestions as $job): 
                            $job_id = intval($job['job_id']);
                            $match_score = floatval($job['match_score']);
                            $details_url = "job_details.php?job_id=" . $job_id . "&match_score=" . urlencode($match_score);

                            $badge_bg = $match_score >= 70 ? '#dcfce7' : '#eff6ff';
                            $badge_color = $match_score >= 70 ? '#166534' : '#1d4ed8';
                        ?>

                            <div class="jcard" onclick="location.href='<?php echo htmlspecialchars($details_url); ?>'">

                                <div class="jcard-top">
                                    <div class="jcard-icon">
                                        <?php if (intval($job['is_external'] ?? 0) == 1): ?>
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="2" y1="12" x2="22" y2="12"/>
                                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>

                                    <span class="match-badge" style="background:<?php echo $badge_bg; ?>; color:<?php echo $badge_color; ?>;">
                                        <?php echo htmlspecialchars(number_format($match_score, 2)); ?>% Match
                                    </span>
                                </div>

                                <h4><?php echo htmlspecialchars($job['title']); ?></h4>

                                <div class="jcard-company">
                                    <?php echo htmlspecialchars($job['company']); ?>
                                </div>

                                <div style="font-size: 0.8rem; color: var(--text3); margin-bottom: 12px;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px;">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <?php echo htmlspecialchars($job['location']); ?>
                                </div>

                                <?php if (!empty($job['matched_skills'])): ?>
                                    <div class="matched-skills">
                                        <b>Matched Skills:</b>
                                        <?php echo htmlspecialchars($job['matched_skills']); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="jcard-salary">
                                    <span>
                                        <?php 
                                        if (!empty($job['salary']) && $job['salary'] > 0) {
                                            echo 'Rs. ' . number_format($job['salary']);
                                        } else {
                                            echo "Negotiable";
                                        }
                                        ?>
                                    </span>

                                    <a href="<?php echo htmlspecialchars($details_url); ?>" 
                                       class="view-link"
                                       onclick="event.stopPropagation();">
                                        View & Apply →
                                    </a>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: white; border-radius: var(--r16);">
                            <h3>No suitable matching jobs found</h3>
                            <p style="color: var(--text3);">
                                No jobs currently match at least 25% of your resume skills. Try updating your resume with more relevant technical skills.
                            </p>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

        <footer class="app-footer">
            <div>© 2026 CareerPilot AI. Matches refreshed automatically.</div>
        </footer>
    </main>
</div>

</body>
</html>