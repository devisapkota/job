<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=my_applications.php");
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        applications.application_id,
        applications.status,
        applications.applied_at,
        applications.match_score,
        jobs.title,
        jobs.company,
        jobs.location,
        jobs.salary,
        jobs.required_skills,
        jobs.description,
        resumes.file_name,
        resumes.file_path,
        resumes.extracted_skills
    FROM applications
    INNER JOIN jobs ON applications.job_id = jobs.job_id
    LEFT JOIN resumes ON applications.resume_id = resumes.resume_id
    WHERE applications.user_id = '$user_id'
    ORDER BY applications.applied_at DESC
";

$result = mysqli_query($conn, $query);
$total_applications = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Applications | CareerPilot</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="style.css">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", sans-serif;
    background: #f4f8ff;
    color: #1e293b;
}

.app-main {
    padding: 30px;
}

/* Page Header */
.page-header {
    background: white;
    padding: 28px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,.06);
    margin-bottom: 20px;
}

.page-header h2 {
    margin: 0;
    color: #1e3a8a;
    font-size: 28px;
}

.page-header p {
    color: #64748b;
    margin-bottom: 0;
}

/* Notification Alerts */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 15px 18px;
    border-radius: 14px;
    margin-bottom: 22px;
    font-size: 15px;
    font-weight: 600;
    box-shadow: 0 8px 20px rgba(0,0,0,.06);
    animation: slideDown .35s ease;
    position: relative;
}

.alert-success {
    background: #ecfdf5;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-warning {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-icon {
    font-size: 18px;
}

.alert-message {
    line-height: 1.5;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-out {
    opacity: 0;
    transform: translateY(-8px);
    transition: all .4s ease;
}

/* Application Card */
.app-card {
    background: white;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 8px 25px rgba(0,0,0,.06);
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
}

.app-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 12px;
}

.app-top h3 {
    margin: 0;
    color: #111827;
    font-size: 22px;
}

.company {
    color: #64748b;
    font-size: 14px;
    margin-top: 5px;
}

/* Status Badges */
.status {
    padding: 7px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    white-space: nowrap;
}

.status-applied {
    background: #eff6ff;
    color: #1d4ed8;
}

.status-under-review {
    background: #fef3c7;
    color: #92400e;
}

.status-shortlisted {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-interview-scheduled {
    background: #ede9fe;
    color: #6d28d9;
}

.status-selected {
    background: #dcfce7;
    color: #166534;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

/* Description */
.desc {
    color: #475569;
    font-size: 14px;
    line-height: 1.6;
    margin-top: 12px;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin: 18px 0;
}

.info-box {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 14px;
}

.info-box b {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 6px;
}

.info-box span,
.info-box a {
    color: #111827;
    font-size: 14px;
    text-decoration: none;
    word-break: break-word;
}

.info-box a:hover {
    color: #2563eb;
    text-decoration: underline;
}

.match-score {
    font-weight: 800;
    color: #2563eb !important;
}

/* Date */
.date {
    color: #64748b;
    font-size: 13px;
    margin-top: 16px;
}

/* Empty State */
.empty-box {
    background: white;
    text-align: center;
    padding: 70px 25px;
    border-radius: 20px;
    border: 1px dashed #cbd5e1;
    box-shadow: 0 8px 25px rgba(0,0,0,.04);
}

.empty-box h3 {
    color: #1e3a8a;
    margin-bottom: 8px;
}

.empty-box p {
    color: #64748b;
}

.btn-primary {
    display: inline-block;
    background: #2563eb;
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    margin-top: 14px;
}

.btn-primary:hover {
    background: #1d4ed8;
}

/* Responsive */
@media (max-width: 768px) {
    .app-main {
        padding: 18px;
    }

    .page-header {
        padding: 22px;
    }

    .page-header h2 {
        font-size: 24px;
    }

    .app-top {
        flex-direction: column;
    }

    .status {
        align-self: flex-start;
    }

    .app-card {
        padding: 20px;
    }
}
</style>
</head>

<body class="app-page">

<div class="app-layout">

<?php include "sidebar.php"; ?>

<main class="app-main">

    <div class="page-header">
        <h2>Application Tracker</h2>
        <p>You have <b><?php echo $total_applications; ?></b> active applications.</p>
    </div>

    <!-- SUCCESS / WARNING / ERROR NOTIFICATION -->
    <?php if (isset($_SESSION['success_message'])) { ?>
        <div class="alert alert-success" id="flashAlert">
            <div class="alert-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="alert-message">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php } ?>

    <?php if (isset($_SESSION['warning_message'])) { ?>
        <div class="alert alert-warning" id="flashAlert">
            <div class="alert-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <div class="alert-message">
                <?php echo htmlspecialchars($_SESSION['warning_message']); ?>
            </div>
        </div>
        <?php unset($_SESSION['warning_message']); ?>
    <?php } ?>

    <?php if (isset($_SESSION['error_message'])) { ?>
        <div class="alert alert-error" id="flashAlert">
            <div class="alert-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>
            <div class="alert-message">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php } ?>

    <?php if ($total_applications > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { 
            $status = !empty($row['status']) ? $row['status'] : 'Applied';
            $status_class = "status-" . strtolower(str_replace(" ", "-", $status));
        ?>

        <div class="app-card">

            <div class="app-top">
                <div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <div class="company">
                        <?php echo htmlspecialchars($row['company']); ?> • 
                        <?php echo htmlspecialchars($row['location']); ?>
                    </div>
                </div>

                <span class="status <?php echo htmlspecialchars($status_class); ?>">
                    <?php echo htmlspecialchars($status); ?>
                </span>
            </div>

            <div class="desc">
                <?php 
                if (!empty($row['description'])) {
                    echo htmlspecialchars(substr($row['description'], 0, 220)) . "...";
                } else {
                    echo "No description available.";
                }
                ?>
            </div>

            <div class="info-grid">

                <div class="info-box">
                    <b>Salary</b>
                    <span>
                        <?php 
                        if (empty($row['salary']) || $row['salary'] == 0 || $row['salary'] == "0.00") {
                            echo "Negotiable / Not Disclosed";
                        } else {
                            echo "Rs. " . htmlspecialchars($row['salary']);
                        }
                        ?>
                    </span>
                </div>

                <div class="info-box">
                    <b>Match Score</b>
                    <span class="match-score">
                        <?php echo htmlspecialchars($row['match_score'] ?? 0); ?>%
                    </span>
                </div>

                <div class="info-box">
                    <b>Resume Used</b>
                    <span>
                        <?php if (!empty($row['file_path'])) { ?>
                            <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">
                                <?php echo htmlspecialchars($row['file_name']); ?>
                            </a>
                        <?php } else { ?>
                            No resume attached
                        <?php } ?>
                    </span>
                </div>

                <div class="info-box">
                    <b>Extracted Skills</b>
                    <span>
                        <?php 
                        if (!empty($row['extracted_skills'])) {
                            echo htmlspecialchars($row['extracted_skills']);
                        } else {
                            echo "Not available";
                        }
                        ?>
                    </span>
                </div>

                <div class="info-box">
                    <b>Required Skills</b>
                    <span>
                        <?php 
                        if (!empty($row['required_skills'])) {
                            echo htmlspecialchars($row['required_skills']);
                        } else {
                            echo "Not specified";
                        }
                        ?>
                    </span>
                </div>

            </div>

            <div class="date">
                Applied on <?php echo date("F j, Y", strtotime($row['applied_at'])); ?>
            </div>

        </div>

        <?php } ?>

    <?php } else { ?>

        <div class="empty-box">
            <h3>No applications yet.</h3>
            <p>Start your journey by applying to jobs that match your skills.</p>
            <a href="user_dashboard.php" class="btn-primary">Browse Jobs</a>
        </div>

    <?php } ?>

</main>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.getElementById("flashAlert");

    if (alertBox) {
        setTimeout(function () {
            alertBox.classList.add("fade-out");

            setTimeout(function () {
                alertBox.style.display = "none";
            }, 400);

        }, 5000);
    }
});
</script>

</body>
</html>