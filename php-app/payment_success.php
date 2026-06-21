<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$transaction_uuid = null;
$transaction_code = null;

/* Local simulation */
if (isset($_GET['local_test']) && isset($_GET['transaction_uuid'])) {
    $transaction_uuid = mysqli_real_escape_string($conn, $_GET['transaction_uuid']);
    $transaction_code = "LOCALTEST" . time();
} 
/* Real eSewa response */
elseif (isset($_GET['data'])) {
    $eSewaData = verifyEsewaResponse($_GET['data']);

    if (!$eSewaData) {
        die("eSewa verification failed.");
    }

    if (($eSewaData['status'] ?? '') !== 'COMPLETE') {
        die("Payment not completed.");
    }

    $transaction_uuid = mysqli_real_escape_string($conn, $eSewaData['transaction_uuid']);
    $transaction_code = mysqli_real_escape_string($conn, $eSewaData['transaction_code'] ?? '');
} else {
    die("Invalid payment response.");
}

$paymentQuery = mysqli_query($conn, "
    SELECT *
    FROM payments
    WHERE transaction_uuid = '$transaction_uuid'
    AND user_id = '$user_id'
    AND payment_status = 'Pending'
    LIMIT 1
");

if (!$paymentQuery || mysqli_num_rows($paymentQuery) == 0) {
    die("Payment record not found or already completed.");
}

$payment = mysqli_fetch_assoc($paymentQuery);

$payment_id = intval($payment['payment_id']);
$purpose = $payment['purpose'];
$job_id = !empty($payment['job_id']) ? intval($payment['job_id']) : null;

mysqli_query($conn, "
    UPDATE payments
    SET 
        payment_status = 'Completed',
        transaction_code = '$transaction_code'
    WHERE payment_id = '$payment_id'
");

/* Chatbot access unlock */
if ($purpose == 'chatbot_access') {

    $checkAccess = mysqli_query($conn, "
        SELECT *
        FROM chatbot_access
        WHERE user_id = '$user_id'
        LIMIT 1
    ");

    if ($checkAccess && mysqli_num_rows($checkAccess) > 0) {
        mysqli_query($conn, "
            UPDATE chatbot_access
            SET is_paid = 1, paid_at = NOW()
            WHERE user_id = '$user_id'
        ");
    } else {
        mysqli_query($conn, "
            INSERT INTO chatbot_access
            (user_id, is_paid, paid_at)
            VALUES
            ('$user_id', 1, NOW())
        ");
    }

    $_SESSION['success_message'] = "Payment successful. Chatbot Pro Access is now active.";
    header("Location: pro_features.php");
    exit;
}

/* External job apply payment */
if ($purpose == 'job_apply') {
    $_SESSION['success_message'] = "Payment successful. You can now apply for this external job.";

    if ($job_id) {
        header("Location: apply_job.php?job_id=" . $job_id);
        exit;
    }

    header("Location: pro_features.php");
    exit;
}

header("Location: pro_features.php");
exit;