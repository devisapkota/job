<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* Get latest uploaded resume skills */
$resumeQuery = mysqli_query($conn, "
    SELECT extracted_skills 
    FROM resumes 
    WHERE user_id = '$user_id'
    ORDER BY resume_id DESC 
    LIMIT 1
");

$has_resume = mysqli_num_rows($resumeQuery) > 0;
$user_skills = [];
if ($has_resume) {
    $resume = mysqli_fetch_assoc($resumeQuery);
    $user_skills = array_map('trim', array_map('strtolower', explode(",", $resume['extracted_skills'])));
}

/* Fetch and match ALL jobs */
$jobsQuery = mysqli_query($conn, "SELECT * FROM jobs");
$suggestions = [];

if ($has_resume) {
    while ($job = mysqli_fetch_assoc($jobsQuery)) {
        $job_skills = array_map('trim', array_map('strtolower', explode(",", $job['required_skills'] ?? '')));
        
        $matched = array_intersect($user_skills, $job_skills);
        $score = 0;
        
        if (count($job_skills) > 0) {
            $score = round((count($matched) / count($job_skills)) * 100, 2);
        }

        // Title matching bonus
        foreach ($user_skills as $us) {
            if (!empty($us) && strpos(strtolower($job['title']), $us) !== false) {
                $score += 15;
            }
        }

        if ($score > 100) $score = 100;

        if ($score > 0) {
            $job['match_score'] = $score;
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
    <title>AI Recommendations | CareerPilot</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-page">

    <div class="app-layout">
        
        <?php include "sidebar.php"; ?>

        <main class="app-main">
            <div class="dash-content">
                
                <div class="welcome-banner">
                    <div class="wb-left">
                        <h2>AI Job Recommendations</h2>
                        <p style="font-size:0.9rem; color:var(--text3);">Personalized career opportunities based on your parsed skills.</p>
                    </div>
                </div>

                <?php if (!$has_resume): ?>
                    <div style="text-align:center; padding:60px; background:white; border-radius:var(--r16); border:1px dashed var(--border);">
                        <div style="font-size:3rem; margin-bottom:20px;"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                        <h3>No Resume Found</h3>
                        <p style="color:var(--text3); margin-bottom:24px;">Please upload your resume in the AI Assistant chat to unlock personalized suggestions.</p>
                        <a href="index.php" class="btn-primary" style="display:inline-flex; width:auto;">Go to AI Assistant</a>
                    </div>
                <?php else: ?>

                    <div class="section-head">
                        <h3>Top Matches for Your Profile</h3>
                        <span style="font-size:0.8rem; color:var(--text4);">Based on: <?php echo htmlspecialchars(implode(", ", array_slice($user_skills, 0, 5))); ?>...</span>
                    </div>

                    <div class="job-cards-row" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                        <?php if (count($suggestions) > 0): ?>
                            <?php foreach ($suggestions as $job): ?>
                                <div class="jcard" onclick="location.href='apply_job.php?job_id=<?php echo $job['job_id']; ?>'">
                                    <div class="jcard-top">
                                        <div class="jcard-icon">
                                            <?php if ($job['is_external'] ?? 0): ?>
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                            <?php else: ?>
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                            <?php endif; ?>
                                        </div>
                                        <span class="match-badge" style="background:<?php echo $job['match_score'] > 70 ? '#dcfce7' : '#eff6ff'; ?>; color:<?php echo $job['match_score'] > 70 ? '#166534' : '#1d4ed8'; ?>;">
                                            <?php echo $job['match_score']; ?>% Match
                                        </span>
                                    </div>
                                    <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                                    <div class="jcard-company"><?php echo htmlspecialchars($job['company']); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text3); margin-bottom: 12px;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> <?php echo htmlspecialchars($job['location']); ?>
                                    </div>
                                    <div class="jcard-salary">
                                        <span>
                                            <?php echo ($job['salary'] > 0) ? 'Rs. ' . number_format($job['salary']) : "Negotiable"; ?>
                                        </span>
                                        <span style="color:var(--blue); font-size:0.75rem; font-weight:700;">View Details →</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: white; border-radius: var(--r16);">
                                <h3>No high-matching jobs found</h3>
                                <p style="color: var(--text3);">We're constantly adding new jobs. Try refining your skills in the meantime!</p>
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
