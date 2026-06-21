<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$transaction_uuid = isset($_GET['transaction_uuid']) 
    ? mysqli_real_escape_string($conn, $_GET['transaction_uuid']) 
    : '';

if ($transaction_uuid != '') {
    mysqli_query($conn, "
        UPDATE payments
        SET payment_status = 'Failed'
        WHERE transaction_uuid = '$transaction_uuid'
        AND user_id = '$user_id'
    ");
}

$_SESSION['error_message'] = "Payment failed or cancelled. Please try again.";
header("Location: pro_features.php");
exit;