<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$notification_id = isset($_GET['notification_id']) ? intval($_GET['notification_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($notification_id > 0) {
    // Mark as read and verify it belongs to the user
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE notification_id = '$notification_id' AND user_id = '$user_id'");
}

// Redirect to my_applications.php
header("Location: my_applications.php");
exit;
?>