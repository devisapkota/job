<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

if (!isset($conn)) {
    die("Database connection failed. Please check db.php and make sure it creates the variable \$conn.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: user_dashboard.php");
    exit;
}

/* Create notifications table automatically */
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        application_id INT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

/* Update application status and notify user */
if (isset($_POST['update_status'])) {

    $application_id = intval($_POST['application_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $allowed_statuses = [
        "Applied",
        "Under Review",
        "Shortlisted",
        "Interview Scheduled",
        "Selected",
        "Rejected"
    ];

    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error_message'] = "Invalid status selected.";
        header("Location: admin_applications.php");
        exit;
    }

    $appQuery = mysqli_query($conn, "
        SELECT 
            applications.application_id,
            applications.user_id,
            applications.status AS old_status,
            jobs.title,
            jobs.company
        FROM applications
        INNER JOIN jobs ON applications.job_id = jobs.job_id
        WHERE applications.application_id = '$application_id'
    ");

    if (!$appQuery) {
        $_SESSION['error_message'] = "Application fetch failed: " . mysqli_error($conn);
        header("Location: admin_applications.php");
        exit;
    }

    if (mysqli_num_rows($appQuery) == 0) {
        $_SESSION['error_message'] = "Application not found.";
        header("Location: admin_applications.php");
        exit;
    }

    $appData = mysqli_fetch_assoc($appQuery);

    $app_user_id = intval($appData['user_id']);
    $job_title = $appData['title'];
    $company = $appData['company'];

    $update = mysqli_query($conn, "
        UPDATE applications 
        SET status = '$new_status'
        WHERE application_id = '$application_id'
    ");

    if (!$update) {
        $_SESSION['error_message'] = "Failed to update status: " . mysqli_error($conn);
        header("Location: admin_applications.php");
        exit;
    }

    if ($new_status == "Applied") {
        $message = "Your application for $job_title at $company has been marked as Applied.";
    } elseif ($new_status == "Under Review") {
        $message = "Your application for $job_title at $company is now Under Review.";
    } elseif ($new_status == "Shortlisted") {
        $message = "Congratulations! You have been shortlisted for $job_title at $company.";
    } elseif ($new_status == "Interview Scheduled") {
        $message = "Your interview has been scheduled for $job_title at $company.";
    } elseif ($new_status == "Selected") {
        $message = "Congratulations! You have been selected for $job_title at $company.";
    } elseif ($new_status == "Rejected") {
        $message = "Your application for $job_title at $company was not selected this time.";
    } else {
        $message = "Your application status for $job_title at $company has been updated.";
    }

    $safe_message = mysqli_real_escape_string($conn, $message);

    $insertNotif = mysqli_query($conn, "
        INSERT INTO notifications 
        (user_id, application_id, message, is_read, created_at)
        VALUES 
        ('$app_user_id', '$application_id', '$safe_message', 0, NOW())
    ");

    if (!$insertNotif) {
        $_SESSION['error_message'] = "Status updated, but notification failed: " . mysqli_error($conn);
        header("Location: admin_applications.php");
        exit;
    }

    $_SESSION['success_message'] = "Application status updated successfully and user notification has been sent.";
    header("Location: admin_applications.php");
    exit;
}

/* Fetch all applications */
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$filter_query = "";
$page_title = "Application Management";

if ($job_id > 0) {
    $filter_query = " WHERE applications.job_id = '$job_id' ";
    
    // Fetch job details for the heading
    $job_info_query = mysqli_query($conn, "SELECT title, company FROM jobs WHERE job_id = '$job_id'");
    if ($job_info_query && mysqli_num_rows($job_info_query) > 0) {
        $job_info = mysqli_fetch_assoc($job_info_query);
        $page_title = "Applications for: " . htmlspecialchars($job_info['title']) . " at " . htmlspecialchars($job_info['company']);
    }
}

$query = "
    SELECT 
        applications.application_id,
        applications.user_id,
        applications.job_id,
        applications.resume_id,
        applications.match_score,
        applications.status,
        applications.applied_at,

        users.name AS applicant_name,
        users.email AS applicant_email,

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
    INNER JOIN users ON applications.user_id = users.user_id
    INNER JOIN jobs ON applications.job_id = jobs.job_id
    LEFT JOIN resumes ON applications.resume_id = resumes.resume_id
    $filter_query
    ORDER BY applications.applied_at DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Application query failed: " . mysqli_error($conn));
}

$total_applications = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Applications | CareerPilot AI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header {
    background: #ffffff;
    padding: 32px;
    border-radius: 22px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    margin-bottom: 24px;
}

.page-header h2 {
    color: #1e3a8a;
    font-size: 32px;
    margin-bottom: 8px;
}

.page-header p {
    color: #64748b;
    font-size: 16px;
}

.alert {
    padding: 15px 18px;
    border-radius: 14px;
    margin-bottom: 22px;
    font-size: 15px;
    font-weight: 700;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.alert-success {
    background: #ecfdf5;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* APPLICATION CARD */
.application-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 26px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    margin-bottom: 22px;
    border: 1px solid #e5e7eb;
}

.application-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 20px;
}

.application-top h3 {
    color: #020617;
    font-size: 24px;
    margin-bottom: 6px;
}

.sub-text {
    color: #64748b;
    font-size: 15px;
}

.status-badge {
    padding: 10px 18px;
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

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 14px;
    margin: 18px 0;
}

.info-box {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    min-height: 90px;
}

.info-box b {
    display: block;
    font-size: 13px;
    color: #64748b;
    margin-bottom: 8px;
}

.info-box span,
.info-box a {
    color: #111827;
    font-size: 15px;
    text-decoration: none;
    word-break: break-word;
}

.info-box a:hover {
    color: #2563eb;
    text-decoration: underline;
}

.match-score {
    color: #2563eb !important;
    font-weight: 800;
}

.description {
    color: #475569;
    font-size: 15px;
    line-height: 1.6;
    background: #f8fafc;
    border-radius: 14px;
    padding: 16px;
    border: 1px solid #e5e7eb;
    margin-top: 18px;
}

.status-form {
    margin-top: 20px;
    background: #f8fafc;
    padding: 16px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
}

.status-form label {
    display: block;
    font-weight: 800;
    color: #334155;
    font-size: 15px;
    margin-bottom: 10px;
}

.status-action-row {
    display: flex;
    gap: 12px;
    align-items: center;
}

.status-form select {
    flex: 1;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    outline: none;
    font-size: 15px;
    background: #ffffff;
}

.status-form button {
    border: none;
    background: #2563eb;
    color: #ffffff;
    padding: 13px 20px;
    border-radius: 10px;
    font-weight: 800;
    cursor: pointer;
    font-size: 14px;
}

.status-form button:hover {
    background: #1d4ed8;
}

.empty-box {
    background: #ffffff;
    text-align: center;
    padding: 70px 25px;
    border-radius: 20px;
    border: 1px dashed #cbd5e1;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04);
}

.empty-box h3 {
    color: #1e3a8a;
    margin-bottom: 8px;
    font-size: 22px;
}

.empty-box p {
    color: #64748b;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .application-top {
        flex-direction: column;
    }

    .status-badge {
        align-self: flex-start;
    }

    .page-header h2 {
        font-size: 26px;
    }

    .status-action-row {
        flex-direction: column;
        align-items: stretch;
    }

    .status-form button {
        width: 100%;
    }
}
</style>
</head>

<body class="app-page">

<div class="app-layout">

    <?php include "admin_sidebar.php"; ?>

    <main class="app-main">

        <div class="page-header">
            <h2><?php echo $page_title; ?></h2>
            <p>Total applications received: <b><?php echo $total_applications; ?></b></p>
        </div>

        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php } ?>

        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php } ?>

        <?php if ($total_applications > 0) { ?>

            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $status = !empty($row['status']) ? $row['status'] : "Applied";
                $status_class = "status-" . strtolower(str_replace(" ", "-", $status));
            ?>

                <div class="application-card">

                    <div class="application-top">
                        <div>
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <div class="sub-text">
                                <?php echo htmlspecialchars($row['company']); ?> • 
                                <?php echo htmlspecialchars($row['location']); ?>
                            </div>
                        </div>

                        <span class="status-badge <?php echo htmlspecialchars($status_class); ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </div>

                    <div class="info-grid">

                        <div class="info-box">
                            <b>Applicant Name</b>
                            <span><?php echo htmlspecialchars($row['applicant_name']); ?></span>
                        </div>

                        <div class="info-box">
                            <b>Applicant Email</b>
                            <span><?php echo htmlspecialchars($row['applicant_email']); ?></span>
                        </div>

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
                            <b>Resume</b>
                            <span>
                                <?php if (!empty($row['file_path'])) { ?>
                                    <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">
                                        View Resume - <?php echo htmlspecialchars($row['file_name']); ?>
                                    </a>
                                <?php } else { ?>
                                    No resume attached
                                <?php } ?>
                            </span>
                        </div>

                        <div class="info-box">
                            <b>Application Date</b>
                            <span>
                                <?php 
                                if (!empty($row['applied_at'])) {
                                    echo date("F j, Y", strtotime($row['applied_at']));
                                } else {
                                    echo "Not available";
                                }
                                ?>
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

                    <div class="description">
                        <b>Job Description:</b><br>
                        <?php 
                        if (!empty($row['description'])) {
                            echo htmlspecialchars(substr($row['description'], 0, 300)) . "...";
                        } else {
                            echo "No description available.";
                        }
                        ?>
                    </div>

                    <form method="POST" class="status-form">
                        <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($row['application_id']); ?>">

                        <label>Update Status:</label>

                        <div class="status-action-row">
                            <select name="status" required>
                                <option value="Applied" <?php if ($status == "Applied") echo "selected"; ?>>Applied</option>
                                <option value="Under Review" <?php if ($status == "Under Review") echo "selected"; ?>>Under Review</option>
                                <option value="Shortlisted" <?php if ($status == "Shortlisted") echo "selected"; ?>>Shortlisted</option>
                                <option value="Interview Scheduled" <?php if ($status == "Interview Scheduled") echo "selected"; ?>>Interview Scheduled</option>
                                <option value="Selected" <?php if ($status == "Selected") echo "selected"; ?>>Selected</option>
                                <option value="Rejected" <?php if ($status == "Rejected") echo "selected"; ?>>Rejected</option>
                            </select>

                            <button type="submit" name="update_status">Save Status</button>
                        </div>
                    </form>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="empty-box">
                <h3>No applications received yet.</h3>
                <p>Applications submitted by job seekers will appear here.</p>
            </div>

        <?php } ?>

    </main>

</div>

</body>
</html>