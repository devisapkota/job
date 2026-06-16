<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$message = $_POST['message'] ?? '';
$sender = $_POST['sender'] ?? '';

if ($message == '' || $sender == '') {
    exit;
}

$message = mysqli_real_escape_string($conn, $message);
$sender = mysqli_real_escape_string($conn, $sender);

mysqli_query($conn, "
    INSERT INTO chat_messages(user_id, sender, message)
    VALUES('$user_id', '$sender', '$message')
");
?>