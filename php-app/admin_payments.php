<?php
session_start();
require_once "db.php";

/* Admin protection */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

$admin_name = $_SESSION['name'] ?? "Admin";
$admin_initial = strtoupper(substr($admin_name, 0, 1));

/* Filters */
$status_filter = $_GET['status'] ?? '';
$purpose_filter = $_GET['purpose'] ?? '';

$where = "WHERE 1=1";

if ($status_filter != '') {
    $safe_status = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND payments.payment_status = '$safe_status'";
}

if ($purpose_filter != '') {
    $safe_purpose = mysqli_real_escape_string($conn, $purpose_filter);
    $where .= " AND payments.purpose = '$safe_purpose'";
}

/* Fetch payment records */
$paymentQuery = mysqli_query($conn, "
    SELECT 
        payments.*,
        users.name AS user_name,
        users.email AS user_email,
        jobs.title AS job_title,
        jobs.company AS job_company
    FROM payments
    LEFT JOIN users ON payments.user_id = users.user_id
    LEFT JOIN jobs ON payments.job_id = jobs.job_id
    $where
    ORDER BY payments.created_at DESC
");

/* Summary data */
$totalPaymentsRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payments");
$totalPaymentsCount = mysqli_fetch_assoc($totalPaymentsRes)['total'] ?? 0;

$completedRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'Completed'");
$completedCount = mysqli_fetch_assoc($completedRes)['total'] ?? 0;

$pendingRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'Pending'");
$pendingCount = mysqli_fetch_assoc($pendingRes)['total'] ?? 0;

$failedRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'Failed'");
$failedCount = mysqli_fetch_assoc($failedRes)['total'] ?? 0;

$revenueRes = mysqli_query($conn, "SELECT SUM(amount) AS revenue FROM payments WHERE payment_status = 'Completed'");
$revenue = mysqli_fetch_assoc($revenueRes)['revenue'] ?? 0;

/* Latest admin notifications */
$admin_notifications = mysqli_query($conn, "
    SELECT *
    FROM admin_notifications
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Status | Admin | CareerPilot AI</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
    --blue: #2563eb;
    --blue-dark: #1d4ed8;
    --dark: #0f172a;
    --text: #0f172a;
    --text2: #334155;
    --text3: #64748b;
    --text4: #94a3b8;
    --border: #e2e8f0;
    --bg: #f8fafc;
    --white: #ffffff;
    --green: #16a34a;
    --green-soft: #dcfce7;
    --red: #dc2626;
    --red-soft: #fee2e2;
    --yellow: #d97706;
    --yellow-soft: #fef3c7;
    --radius: 18px;
    --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
}

.admin-layout {
    display: flex;
    min-height: 100vh;
}

/* =========================
   ADMIN SIDEBAR
========================= */

.admin-sidebar {
    width: 280px;
    background: var(--white);
    border-right: 1px solid var(--border);
    padding: 22px 16px;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.admin-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 22px;
    font-weight: 800;
    color: var(--blue-dark);
    margin-bottom: 24px;
}

.admin-brand-icon {
    width: 42px;
    height: 42px;
    background: var(--blue-dark);
    color: white;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid var(--border);
    padding: 15px;
    border-radius: 16px;
    margin-bottom: 22px;
}

.admin-avatar {
    width: 46px;
    height: 46px;
    background: var(--blue-dark);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.admin-name {
    font-weight: 800;
    color: var(--text);
    font-size: 15px;
}

.admin-role {
    font-size: 13px;
    color: var(--text3);
    margin-top: 3px;
}

.admin-nav {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.admin-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text2);
    text-decoration: none;
    padding: 13px 14px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    transition: 0.2s;
}

.admin-nav a:hover {
    background: #eff6ff;
    color: var(--blue-dark);
}

.admin-nav a.active {
    background: var(--blue-dark);
    color: white;
}

.sidebar-section-title {
    padding: 18px 14px 10px;
    font-size: 11px;
    color: var(--text4);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.admin-notifs {
    max-height: 230px;
    overflow-y: auto;
    padding: 0 6px;
}

.admin-notif-item {
    display: block;
    text-decoration: none;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 8px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: var(--blue-dark);
    font-size: 12px;
    line-height: 1.45;
}

.admin-notif-time {
    font-size: 10px;
    color: var(--text4);
    margin-top: 6px;
}

.admin-bottom {
    margin-top: 20px;
}

.signout {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #dc2626 !important;
    text-decoration: none;
    padding: 13px 14px;
    border-radius: 12px;
    font-weight: 800;
}

.signout:hover {
    background: #fee2e2;
}

/* =========================
   MAIN CONTENT
========================= */

.main-content {
    margin-left: 280px;
    width: calc(100% - 280px);
    padding: 34px;
}

.page-header {
    background: linear-gradient(135deg, #2563eb, #0f172a);
    color: white;
    padding: 36px;
    border-radius: 28px;
    box-shadow: var(--shadow);
    margin-bottom: 26px;
}

.page-header h1 {
    font-size: 38px;
    font-weight: 900;
    margin-bottom: 10px;
}

.page-header p {
    color: #dbeafe;
    font-size: 16px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(150px, 1fr));
    gap: 18px;
    margin-bottom: 26px;
}

.summary-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 24px;
    box-shadow: var(--shadow);
}

.summary-card h3 {
    color: var(--text3);
    font-size: 14px;
    margin-bottom: 14px;
}

.summary-card .number {
    font-size: 34px;
    font-weight: 900;
    color: var(--blue-dark);
}

.filter-box {
    background: white;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 22px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 26px;
    box-shadow: var(--shadow);
}

select {
    padding: 13px 16px;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    font-size: 15px;
    background: white;
    min-width: 160px;
}

button,
.btn {
    padding: 13px 18px;
    border-radius: 12px;
    border: none;
    background: var(--blue);
    color: white;
    font-weight: 800;
    cursor: pointer;
    text-decoration: none;
}

.clear-btn {
    background: #64748b;
}

.table-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 28px;
    box-shadow: var(--shadow);
}

.table-card h2 {
    font-size: 28px;
    margin-bottom: 20px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1050px;
}

th {
    background: #f1f5f9;
    color: var(--text2);
    padding: 15px;
    text-align: left;
    font-size: 14px;
}

td {
    padding: 15px;
    border-bottom: 1px solid var(--border);
    color: #475569;
    font-size: 14px;
    vertical-align: top;
}

.badge {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
}

.badge-completed {
    background: var(--green-soft);
    color: #166534;
}

.badge-pending {
    background: var(--yellow-soft);
    color: #92400e;
}

.badge-failed {
    background: var(--red-soft);
    color: #991b1b;
}

.purpose {
    color: var(--blue-dark);
    font-weight: 900;
}

.empty {
    text-align: center;
    padding: 40px;
    color: var(--text3);
}

@media (max-width: 1100px) {
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .admin-sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .admin-layout {
        flex-direction: column;
    }

    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 18px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="admin-layout">

    <aside class="admin-sidebar">

        <div>
            <div class="admin-brand">
                <div class="admin-brand-icon">🛠</div>
                <span>CareerPilot Admin</span>
            </div>

            <div class="admin-profile">
                <div class="admin-avatar"><?php echo htmlspecialchars($admin_initial); ?></div>
                <div>
                    <div class="admin-name"><?php echo htmlspecialchars($admin_name); ?></div>
                    <div class="admin-role">System Administrator</div>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="admin_dashboard.php" class="<?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>">
                    <span>▦</span> Dashboard
                </a>

                <a href="admin_add_job.php" class="<?php echo $current_page == 'admin_add_job.php' ? 'active' : ''; ?>">
                    <span>＋</span> Add New Job
                </a>

                <a href="admin_applications.php" class="<?php echo $current_page == 'admin_applications.php' ? 'active' : ''; ?>">
                    <span>📄</span> Applications
                </a>

                <a href="admin_payments.php" class="<?php echo $current_page == 'admin_payments.php' ? 'active' : ''; ?>">
                    <span>💳</span> Payments
                </a>
            </nav>

            <div class="sidebar-section-title">Notifications</div>

            <div class="admin-notifs">
                <?php if ($admin_notifications && mysqli_num_rows($admin_notifications) > 0): ?>
                    <?php while ($n = mysqli_fetch_assoc($admin_notifications)): ?>
                        <a href="admin_applications.php" class="admin-notif-item">
                            <?php echo htmlspecialchars($n['message']); ?>
                            <div class="admin-notif-time">
                                <?php echo date("M d, h:i A", strtotime($n['created_at'])); ?>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align:center; color:var(--text4); font-size:12px; padding:18px;">
                        No notifications yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-bottom">
            <a href="logout.php" class="signout">
                <span>↪</span> Sign Out
            </a>
        </div>

    </aside>

    <main class="main-content">

        <div class="page-header">
            <h1>Payment Status</h1>
            <p>View chatbot access payments and external job application payments.</p>
        </div>

        <div class="summary-grid">

            <div class="summary-card">
                <h3>Total Payments</h3>
                <div class="number"><?php echo $totalPaymentsCount; ?></div>
            </div>

            <div class="summary-card">
                <h3>Completed</h3>
                <div class="number"><?php echo $completedCount; ?></div>
            </div>

            <div class="summary-card">
                <h3>Pending</h3>
                <div class="number"><?php echo $pendingCount; ?></div>
            </div>

            <div class="summary-card">
                <h3>Failed</h3>
                <div class="number"><?php echo $failedCount; ?></div>
            </div>

            <div class="summary-card">
                <h3>Total Revenue</h3>
                <div class="number">Rs. <?php echo number_format((float)$revenue, 2); ?></div>
            </div>

        </div>

        <form method="GET" class="filter-box">

            <select name="status">
                <option value="">All Status</option>
                <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="Failed" <?php echo $status_filter == 'Failed' ? 'selected' : ''; ?>>Failed</option>
            </select>

            <select name="purpose">
                <option value="">All Purpose</option>
                <option value="chatbot_access" <?php echo $purpose_filter == 'chatbot_access' ? 'selected' : ''; ?>>Chatbot Access</option>
                <option value="job_apply" <?php echo $purpose_filter == 'job_apply' ? 'selected' : ''; ?>>External Job Apply</option>
            </select>

            <button type="submit">Filter</button>
            <a href="admin_payments.php" class="btn clear-btn">Clear</a>

        </form>

        <div class="table-card">
            <h2>Payment Records</h2>

            <?php if ($paymentQuery && mysqli_num_rows($paymentQuery) > 0): ?>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>User</th>
                                <th>Purpose</th>
                                <th>Job</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction UUID</th>
                                <th>Transaction Code</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php 
                        $sn = 1;
                        while ($payment = mysqli_fetch_assoc($paymentQuery)): 

                            $statusClass = "badge-pending";

                            if ($payment['payment_status'] == "Completed") {
                                $statusClass = "badge-completed";
                            } elseif ($payment['payment_status'] == "Failed") {
                                $statusClass = "badge-failed";
                            }

                            $purposeText = $payment['purpose'] == "chatbot_access"
                                ? "Chatbot Access"
                                : "External Job Apply";
                        ?>

                            <tr>
                                <td><?php echo $sn++; ?></td>

                                <td>
                                    <strong><?php echo htmlspecialchars($payment['user_name'] ?? 'Unknown User'); ?></strong><br>
                                    <small><?php echo htmlspecialchars($payment['user_email'] ?? 'No email'); ?></small>
                                </td>

                                <td>
                                    <span class="purpose">
                                        <?php echo htmlspecialchars($purposeText); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($payment['job_title'])): ?>
                                        <strong><?php echo htmlspecialchars($payment['job_title']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($payment['job_company']); ?></small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    Rs. <?php echo number_format((float)$payment['amount'], 2); ?>
                                </td>

                                <td>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($payment['payment_status']); ?>
                                    </span>
                                </td>

                                <td><?php echo htmlspecialchars($payment['transaction_uuid']); ?></td>

                                <td>
                                    <?php echo !empty($payment['transaction_code']) 
                                        ? htmlspecialchars($payment['transaction_code']) 
                                        : '-'; 
                                    ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y h:i A", strtotime($payment['created_at'])); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="empty">
                    No payment records found.
                </div>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>