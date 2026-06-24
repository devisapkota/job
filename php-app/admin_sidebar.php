<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

$admin_name = $_SESSION['name'] ?? "Admin";
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$current_page = basename($_SERVER['PHP_SELF']);

$unread_count = 0;
$latest_notifs = false;

$unread_query = mysqli_query($conn, "
    SELECT COUNT(*) AS count 
    FROM admin_notifications 
    WHERE is_read = 0
");

if ($unread_query) {
    $unread_data = mysqli_fetch_assoc($unread_query);
    $unread_count = intval($unread_data['count'] ?? 0);
}

$latest_notifs = mysqli_query($conn, "
    SELECT * 
    FROM admin_notifications 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>

<style>
:root {
    --admin-sidebar-width: 280px;
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
    --red: #dc2626;
    --red-soft: #fee2e2;
}

/* IMPORTANT:
   Add this class to main content of every admin page:
   <main class="admin-main">
*/
.admin-main,
.app-main,
.main-content {
    margin-left: var(--admin-sidebar-width);
    width: calc(100% - var(--admin-sidebar-width));
    min-height: 100vh;
    padding: 34px;
    background: var(--bg);
}

/* Sidebar */
.sidebar {
    width: var(--admin-sidebar-width);
    background: var(--white);
    border-right: 1px solid var(--border);
    padding: 22px 16px;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    z-index: 999;
}

/* Hide ugly scrollbar but keep scrolling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 20px;
}

/* Brand */
.sb-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--blue-dark);
    font-size: 24px;
    font-weight: 900;
    margin-bottom: 26px;
    line-height: 1.2;
}

.sb-brand-icon {
    width: 44px;
    height: 44px;
    background: var(--blue-dark);
    color: white;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Profile */
.sb-user {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid var(--border);
    padding: 16px;
    border-radius: 18px;
    margin-bottom: 24px;
}

.sb-avatar {
    width: 48px;
    height: 48px;
    background: var(--dark);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    flex-shrink: 0;
}

.sb-user-name {
    font-weight: 900;
    color: var(--text);
    font-size: 15px;
    word-break: break-word;
}

.sb-user-role {
    font-size: 13px;
    color: var(--text3);
    margin-top: 4px;
}

/* Navigation */
.sb-nav,
.sb-nav-bottom {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.sb-nav a,
.sb-nav-bottom a {
    display: flex;
    align-items: center;
    gap: 11px;
    color: var(--text2);
    text-decoration: none;
    padding: 14px 15px;
    border-radius: 13px;
    font-size: 15px;
    font-weight: 800;
    transition: 0.2s ease;
    white-space: nowrap;
}

.sb-nav a:hover,
.sb-nav-bottom a:hover {
    background: #eff6ff;
    color: var(--blue-dark);
}

.sb-nav a.active {
    background: var(--blue-dark);
    color: white;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
}

.nav-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    font-size: 16px;
}

/* Notifications */
.sb-section-title {
    padding: 22px 14px 10px;
    font-size: 11px;
    text-transform: uppercase;
    color: var(--text4);
    font-weight: 900;
    letter-spacing: 0.08em;
}

.notif-count {
    background: var(--red);
    color: white;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 10px;
    margin-left: 6px;
}

.sb-notifications {
    max-height: 260px;
    overflow-y: auto;
    padding: 0 6px;
}

.admin-notif {
    display: block;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 12px;
    margin-bottom: 8px;
    transition: 0.2s;
}

.admin-notif.unread {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
}

.admin-notif.read {
    background: transparent;
    border: 1px solid transparent;
}

.admin-notif-text {
    line-height: 1.45;
}

.admin-notif.unread .admin-notif-text {
    color: var(--blue-dark);
    font-weight: 800;
}

.admin-notif.read .admin-notif-text {
    color: var(--text3);
    font-weight: 500;
}

.admin-notif-time {
    font-size: 10px;
    color: var(--text4);
    margin-top: 6px;
}

/* Bottom */
.sb-bottom {
    margin-top: 24px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
}

.signout-link {
    color: #dc2626 !important;
}

.signout-link:hover {
    background: var(--red-soft) !important;
}

/* Mobile */
@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        border-right: none;
        border-bottom: 1px solid var(--border);
    }

    .admin-main,
    .app-main,
    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 18px;
    }
}
</style>

<div class="sidebar">

    <div>
        <div class="sb-top">

            <div class="sb-brand">
                <div class="sb-brand-icon">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </div>
                <span>CareerPilot<br>Admin</span>
            </div>

            <div class="sb-user">
                <div class="sb-avatar">
                    <?php echo htmlspecialchars($admin_initial); ?>
                </div>

                <div>
                    <div class="sb-user-name">
                        <?php echo htmlspecialchars($admin_name); ?>
                    </div>
                    <div class="sb-user-role">System Administrator</div>
                </div>
            </div>

        </div>

        <nav class="sb-nav">

            <a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
                <span class="nav-icon">▦</span>
                Dashboard
            </a>

            <a href="admin_add_job.php" class="<?php echo ($current_page == 'admin_add_job.php') ? 'active' : ''; ?>">
                <span class="nav-icon">＋</span>
                Add New Job
            </a>

            <a href="admin_applications.php" class="<?php echo ($current_page == 'admin_applications.php') ? 'active' : ''; ?>">
                <span class="nav-icon">📄</span>
                Applications
            </a>

            <a href="admin_payments.php" class="<?php echo ($current_page == 'admin_payments.php') ? 'active' : ''; ?>">
                <span class="nav-icon">💳</span>
                Payments
            </a>

            <div class="sb-section-title">
                Notifications
                <?php if ($unread_count > 0): ?>
                    <span class="notif-count"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </div>

            <div class="sb-notifications">
                <?php if ($latest_notifs && mysqli_num_rows($latest_notifs) > 0): ?>
                    <?php while ($n = mysqli_fetch_assoc($latest_notifs)): ?>

                        <a href="admin_notification_redirect.php?notification_id=<?php echo intval($n['notification_id']); ?>" 
                           class="admin-notif <?php echo intval($n['is_read']) == 0 ? 'unread' : 'read'; ?>">

                            <div class="admin-notif-text">
                                <?php echo htmlspecialchars($n['message']); ?>
                            </div>

                            <div class="admin-notif-time">
                                <?php echo date("M j, g:i A", strtotime($n['created_at'])); ?>
                            </div>

                        </a>

                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="padding: 20px; text-align: center; color: var(--text4); font-size: 12px;">
                        No notifications yet.
                    </div>
                <?php endif; ?>
            </div>

        </nav>
    </div>

    <div class="sb-bottom">
        <div class="sb-nav-bottom">
            <a href="logout.php" class="signout-link">
                <span class="nav-icon">↪</span>
                Sign Out
            </a>
        </div>
    </div>

</div>