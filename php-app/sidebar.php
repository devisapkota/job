<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['name'] ?? "Guest";
$user_initial = $user_id ? strtoupper(substr($user_name, 0, 1)) : "G";
$user_role = $_SESSION['role'] ?? "guest";

$unread_count = 0;
$latest_notifs = false;

if ($user_id > 0) {
    $safe_user_id = intval($user_id);

    $unread_query = mysqli_query($conn, "
        SELECT COUNT(*) AS count 
        FROM notifications 
        WHERE user_id = '$safe_user_id' 
        AND is_read = 0
    ");

    if ($unread_query) {
        $unread_data = mysqli_fetch_assoc($unread_query);
        $unread_count = intval($unread_data['count'] ?? 0);
    }

    $latest_notifs = mysqli_query($conn, "
        SELECT * 
        FROM notifications 
        WHERE user_id = '$safe_user_id' 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
}
?>

<div class="sidebar">
    <div class="sb-top">
        <div class="sb-brand">
            <div class="sb-brand-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <span>CareerPilot AI</span>
        </div>

        <div class="sb-user">
            <div class="sb-avatar"><?php echo htmlspecialchars($user_initial); ?></div>
            <div>
                <div class="sb-user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="sb-user-role"><?php echo htmlspecialchars(ucfirst($user_role)); ?> Account</div>
            </div>
        </div>
    </div>

    <nav class="sb-nav">
        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <span class="nav-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </span>
            <?php echo $user_id ? 'AI Assistant' : 'Home'; ?>
        </a>

        <?php if ($user_id): ?>

            <a href="my_applications.php" class="<?php echo $current_page == 'my_applications.php' ? 'active' : ''; ?>">
                <span class="nav-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </span>
                My Applications
            </a>

            <a href="job_suggestions.php" class="<?php echo $current_page == 'job_suggestions.php' ? 'active' : ''; ?>">
                <span class="nav-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </span>
                AI Suggestions
            </a>

            <a href="pro_features.php" class="<?php echo $current_page == 'pro_features.php' ? 'active' : ''; ?>">
                <span class="nav-icon">⭐</span>
                Pro Features
            </a>

            <div class="sb-section-title" style="padding: 20px 20px 10px; font-size: 11px; text-transform: uppercase; color: var(--text4); font-weight: 700; letter-spacing: 0.5px;">
                Notifications 
                <?php if ($unread_count > 0): ?>
                    <span style="background: var(--red); color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-left: 5px;">
                        <?php echo $unread_count; ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="sb-notifications" style="max-height: 200px; overflow-y: auto; padding: 0 10px;">
                <?php if ($latest_notifs && mysqli_num_rows($latest_notifs) > 0): ?>
                    <?php while ($n = mysqli_fetch_assoc($latest_notifs)): ?>
                        <a href="user_notification_redirect.php?notification_id=<?php echo intval($n['notification_id']); ?>" 
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
                    <div style="padding: 20px; text-align: center; color: var(--text4); font-size: 12px;">
                        No notifications yet.
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>

            <a href="login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">
                <span class="nav-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </span>
                Login
            </a>

            <a href="register.php" class="<?php echo $current_page == 'register.php' ? 'active' : ''; ?>">
                <span class="nav-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </span>
                Register
            </a>

        <?php endif; ?>
    </nav>

    <div class="sb-bottom">
        <?php if ($user_id): ?>
            <div class="sb-nav-bottom">
                <a href="logout.php">
                    <span class="nav-icon">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </span>
                    Sign Out
                </a>
            </div>
        <?php else: ?>
            <div style="padding: 10px; text-align: center; font-size: 11px; color: var(--text4);">
                Login to access more features.
            </div>
        <?php endif; ?>
    </div>
</div>