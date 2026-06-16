<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$notification_id = isset($_GET['notification_id']) ? intval($_GET['notification_id']) : 0;

if ($notification_id > 0) {
    // Fetch notification
    $query = "SELECT job_id FROM admin_notifications WHERE notification_id = '$notification_id'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $job_id = $row['job_id'];
        
        // Mark as read
        mysqli_query($conn, "UPDATE admin_notifications SET is_read = 1 WHERE notification_id = '$notification_id'");
        
        // Redirect to admin_applications.php with the specific job_id
        header("Location: admin_applications.php?job_id=" . $job_id);
        exit;
    }
}

// Fallback to dashboard if something goes wrong
header("Location: admin_dashboard.php");
exit;
?>