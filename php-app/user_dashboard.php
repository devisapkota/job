<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$location = $_GET['location'] ?? '';
$salary = $_GET['salary'] ?? '';

/* Fetch latest user notifications */
$notifQuery = mysqli_query($conn, "
    SELECT * FROM notifications
    WHERE user_id = '$user_id'
    ORDER BY created_at DESC
    LIMIT 5
");

$unreadQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS unread_count
    FROM notifications
    WHERE user_id = '$user_id'
    AND is_read = 0
");

$unreadData = mysqli_fetch_assoc($unreadQuery);
$unread_count = $unreadData['unread_count'] ?? 0;

/* Fetch jobs */
$query = "SELECT * FROM jobs WHERE is_external = 0";

if (!empty($location)) {
    $location_safe = mysqli_real_escape_string($conn, $location);
    $query .= " AND location LIKE '%$location_safe%'";
}

if (!empty($salary)) {
    $salary_safe = floatval($salary);
    $query .= " AND salary >= $salary_safe";
}

$query .= " ORDER BY job_id DESC";

$result = mysqli_query($conn, $query);
$total_jobs = mysqli_num_rows($result);

/* Mark notifications as read after loading dashboard */
mysqli_query($conn, "
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = '$user_id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs | CareerPilot</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Extra tweaks for the job search page specifically */
        .filter-section {
            background: white;
            padding: 24px;
            border-radius: var(--r16);
            margin-bottom: 28px;
            box-shadow: var(--s1);
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 15px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .filter-section { 
                grid-template-columns: 1fr; 
            }
        }

        /* Notification Section */
        .notification-box {
            background: white;
            border-radius: var(--r16);
            padding: 22px;
            margin-bottom: 28px;
            box-shadow: var(--s1);
            border: 1px solid #e5e7eb;
        }

        .notification-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .notification-head h3 {
            margin: 0;
            color: #1e3a8a;
            font-size: 20px;
        }

        .notif-count {
            background: #2563eb;
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 999px;
        }

        .notification-item {
            display: flex;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 14px;
            border-radius: 14px;
            margin-bottom: 12px;
        }

        .notification-item:last-child {
            margin-bottom: 0;
        }

        .notification-item.unread {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .notification-icon {
            font-size: 20px;
            line-height: 1.4;
        }

        .notification-item p {
            margin: 0;
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
        }

        .notification-item small {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
        }

        .no-notification {
            color: #64748b;
            font-size: 14px;
            background: #f8fafc;
            padding: 14px;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
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
                        <h2>Find Your Next Career Opportunity</h2>
                        <div class="wb-ai-msg">
                            <span class="wb-ai-icon">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            </span>
                            <span>
                                I've found <b><?php echo $total_jobs; ?></b> jobs that might match your profile. Use filters to narrow down.
                            </span>
                        </div>
                    </div>

                    <div class="wb-stats">
                        <div class="wb-stat">
                            <div class="wb-stat-val"><?php echo $total_jobs; ?></div>
                            <div class="wb-stat-label">Live Jobs</div>
                        </div>
                    </div>
                </div>

                <!-- USER NOTIFICATION SECTION -->
                <div class="notification-box">
                    <div class="notification-head">
                        <h3>Application Notifications</h3>

                        <?php if ($unread_count > 0) { ?>
                            <span class="notif-count">
                                <?php echo $unread_count; ?> New
                            </span>
                        <?php } ?>
                    </div>

                    <?php if ($notifQuery && mysqli_num_rows($notifQuery) > 0) { ?>

                        <?php while ($notif = mysqli_fetch_assoc($notifQuery)) { ?>
                            <div class="notification-item <?php echo ($notif['is_read'] == 0) ? 'unread' : ''; ?>">
                                <div class="notification-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
                                <div>
                                    <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                    <small>
                                        <?php echo date("F j, Y, g:i A", strtotime($notif['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        <?php } ?>

                    <?php } else { ?>

                        <div class="no-notification">
                            No notifications yet. Application status updates from admin will appear here.
                        </div>

                    <?php } ?>
                </div>

                <form method="GET" class="filter-section">
                    <div>
                        <label class="form-label">Location</label>
                        <input type="text" name="location" placeholder="City or Region" value="<?php echo htmlspecialchars($location); ?>">
                    </div>

                    <div>
                        <label class="form-label">Min Salary (Rs.)</label>
                        <input type="number" name="salary" placeholder="Min Amount" value="<?php echo htmlspecialchars($salary); ?>">
                    </div>

                    <button type="submit" class="btn-primary" style="width: auto; height: 44px; padding: 0 25px;">
                        Filter
                    </button>

                    <a href="user_dashboard.php" class="btn-outline" style="height: 44px;">
                        Reset
                    </a>
                </form>

                <div class="section-head">
                    <h3>All Job Listings</h3>
                    <span>Showing <?php echo $total_jobs; ?> positions</span>
                </div>

                <div class="job-cards-row" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                    <?php if ($total_jobs > 0) { ?>

                        <?php while ($job = mysqli_fetch_assoc($result)) { ?>

                            <div class="jcard" onclick="location.href='apply_job.php?job_id=<?php echo $job['job_id']; ?>'">
                                <div class="jcard-top">
                                    <div class="jcard-icon">
                                        <svg width="18" height="18" fill="none" stroke="#1d4ed8" stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                        </svg>
                                    </div>

                                    <span class="match-badge">Admin Posted</span>
                                </div>

                                <h4><?php echo htmlspecialchars($job['title']); ?></h4>

                                <div class="jcard-company">
                                    <?php echo htmlspecialchars($job['company']); ?>
                                </div>

                                <div style="font-size: 0.8rem; color: var(--text3); margin-bottom: 12px;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:3px;">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <?php echo htmlspecialchars($job['location']); ?>
                                </div>

                                <div class="jcard-salary">
                                    <span>
                                        Rs. <?php echo ($job['salary'] > 0) ? htmlspecialchars($job['salary']) : "Negotiable"; ?>
                                    </span>

                                    <span class="jcard-bm">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: white; border-radius: var(--r16);">
                            <div style="font-size: 3rem; margin-bottom: 20px;">
                                <svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                            </div>

                            <h3>No jobs found matching your criteria.</h3>
                            <p style="color: var(--text3);">Try adjusting your filters or search keywords.</p>
                        </div>

                    <?php } ?>
                </div>

            </div>

            <footer class="app-footer">
                <div>© 2026 CareerPilot AI. All rights reserved.</div>
                <div>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Contact Support</a>
                </div>
            </footer>
        </main>
    </div>

</body>
</html>