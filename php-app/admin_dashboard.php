<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$search_query = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($conn, $search);
    $search_query = " WHERE title LIKE '%$s%' OR company LIKE '%$s%' OR required_skills LIKE '%$s%' ";
}

$result = mysqli_query($conn, "SELECT * FROM jobs $search_query ORDER BY job_id DESC");
$total_jobs = mysqli_num_rows($result);
$admin_name = $_SESSION['name'] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | CareerPilot AI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table-card {
            background: white;
            border-radius: var(--r16);
            padding: 0;
            overflow: hidden;
            box-shadow: var(--s1);
            border: 1px solid var(--border);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--bg2); padding: 16px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: var(--text3); border-bottom: 1px solid var(--border); }
        td { padding: 16px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        tr:last-child td { border-bottom: none; }
        .action-links { display: flex; gap: 10px; }
        
        /* Ensure primary buttons are visible and white on blue */
        .btn-primary-admin {
            background: var(--blue);
            color: #ffffff !important;
            padding: 10px 24px;
            border-radius: var(--r8);
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: 0.2s;
        }
        .btn-primary-admin:hover {
            background: var(--blue-hover);
        }

        .admin-search-area {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
    </style>
</head>
<body class="app-page">

    <div class="app-layout">
        
        <?php include "admin_sidebar.php"; ?>

        <main class="app-main">
            <div class="dash-content">
                
                <div class="welcome-banner" style="background: var(--dark); color: white;">
                    <div class="wb-left">
                        <h2 style="color: white;">Admin Control Center</h2>
                        <p style="opacity: 0.8; font-size: 0.9rem;">Manage job listings, monitor applications, and oversee platform growth.</p>
                    </div>
                    <div class="wb-stats">
                        <div class="wb-stat">
                            <div class="wb-stat-val" style="color: white;"><?php echo $total_jobs; ?></div>
                            <div class="wb-stat-label" style="color: rgba(255,255,255,0.6);">Total Jobs</div>
                        </div>
                    </div>
                </div>

                <div class="section-head">
                    <h3>Active Job Listings</h3>
                    <a href="admin_add_job.php" class="btn-primary-admin">+ Post New Job</a>
                </div>

                <form method="GET" class="admin-search-area">
                    <div class="input-wrap" style="flex: 1; margin-bottom: 0;">
                        <span class="iicon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                        <input type="text" name="search" placeholder="Search by title, company or skills..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0 25px; height: 46px;">Search</button>
                    <?php if(!empty($search)): ?>
                        <a href="admin_dashboard.php" class="btn-outline" style="height: 46px; display: flex; align-items: center; padding: 0 20px;">Clear</a>
                    <?php endif; ?>
                </form>

                <?php if (isset($_SESSION['admin_msg'])): ?>
                    <div class="alert alert-success">
                        <?php 
                            echo $_SESSION['admin_msg']; 
                            unset($_SESSION['admin_msg']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="admin-table-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Company/Source</th>
                                <th>Location</th>
                                <th>Salary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_jobs > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text);"><?php echo htmlspecialchars($row['title']); ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text4);"><?php echo htmlspecialchars($row['required_skills']); ?></div>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($row['company']); ?>
                                            <?php if(isset($row['is_external']) && $row['is_external']): ?>
                                                <span style="font-size: 0.6rem; background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 4px; font-weight: bold; margin-left: 4px;">Scraped</span>
                                            <?php else: ?>
                                                <span style="font-size: 0.6rem; background: #f0fdf4; color: #166534; padding: 2px 6px; border-radius: 4px; font-weight: bold; margin-left: 4px;">Internal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td>Rs. <?php echo htmlspecialchars($row['salary']); ?></td>
                                        <td>
                                            <div class="action-links">
                                                <a href="edit_job.php?job_id=<?php echo $row['job_id']; ?>" class="btn-sm btn-sm-outline" style="text-decoration:none;">Edit</a>
                                                <a href="delete_job.php?job_id=<?php echo $row['job_id']; ?>" class="btn-sm" style="background:#fee2e2; color:#dc2626; text-decoration:none;" onclick="return confirm('Delete this job?');">Delete</a>
                                                <a href="admin_applications.php?job_id=<?php echo $row['job_id']; ?>">View Applications</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text4);">No jobs found. <?php echo !empty($search) ? "Try a different search term." : "Start by adding one."; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
