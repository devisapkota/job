<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";
$admin_name = $_SESSION['name'] ?? "Admin";
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch unread notification count
$unread_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0");
$unread_data = mysqli_fetch_assoc($unread_query);
$unread_count = $unread_data['count'] ?? 0;

// Fetch latest 5 notifications
$latest_notifs = mysqli_query($conn, "SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 5");
?>
<div class="sidebar">
    <div class="sb-top">
        <div class="sb-brand">
            <div class="sb-brand-icon"><svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
            <span>CareerPilot Admin</span>
        </div>
        <div class="sb-user">
            <div class="sb-avatar" style="background: var(--dark);">A</div>
            <div>
                <div class="sb-user-name"><?php echo htmlspecialchars($admin_name); ?></div>
                <div class="sb-user-role">System Administrator</div>
            </div>
        </div>
    </div>
    <nav class="sb-nav">
        <a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
            <span class="nav-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span> Dashboard
        </a>
        <a href="admin_add_job.php" class="<?php echo ($current_page == 'admin_add_job.php') ? 'active' : ''; ?>">
            <span class="nav-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span> Add New Job
        </a>
        <a href="admin_applications.php" class="<?php echo ($current_page == 'admin_applications.php') ? 'active' : ''; ?>">
            <span class="nav-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span> Applications
        </a>
        
        <div class="sb-section-title" style="padding: 20px 20px 10px; font-size: 11px; text-transform: uppercase; color: var(--text4); font-weight: 700; letter-spacing: 0.5px;">
            Notifications <?php if($unread_count > 0): ?><span style="background: var(--red); color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-left: 5px;"><?php echo $unread_count; ?></span><?php endif; ?>
        </div>
        
        <div class="sb-notifications" style="max-height: 250px; overflow-y: auto; padding: 0 10px;">
            <?php if(mysqli_num_rows($latest_notifs) > 0): ?>
                <?php while($n = mysqli_fetch_assoc($latest_notifs)): ?>
                    <a href="admin_notification_redirect.php?notification_id=<?php echo $n['notification_id']; ?>" 
                       style="display: block; padding: 12px; border-radius: 8px; text-decoration: none; font-size: 12px; margin-bottom: 5px; background: <?php echo $n['is_read'] ? 'transparent' : 'rgba(37, 99, 235, 0.05)'; ?>; border: 1px solid <?php echo $n['is_read'] ? 'transparent' : 'rgba(37, 99, 235, 0.1)'; ?>; transition: 0.2s;">
                        <div style="color: <?php echo $n['is_read'] ? 'var(--text3)' : 'var(--blue)'; ?>; font-weight: <?php echo $n['is_read'] ? '400' : '600'; ?>; line-height: 1.4;">
                            <?php echo htmlspecialchars($n['message']); ?>
                        </div>
                        <div style="font-size: 10px; color: var(--text4); margin-top: 5px;">
                            <?php echo date("M j, g:i a", strtotime($n['created_at'])); ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="padding: 20px; text-align: center; color: var(--text4); font-size: 12px;">No notifications yet.</div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="sb-bottom">
        <div class="sb-nav-bottom">
            <a href="logout.php">
                <span class="nav-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span> Sign Out
            </a>
        </div>
    </div>
</div>
