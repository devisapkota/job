<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=pro_features.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

$purpose = $_GET['purpose'] ?? '';
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : null;

if (!in_array($purpose, ['job_apply', 'chatbot_access'])) {
    die("Invalid payment purpose.");
}

/* Amount and validation */
if ($purpose == 'job_apply') {

    if (!$job_id) {
        $_SESSION['error_message'] = "Please select an external job before making payment.";
        header("Location: pro_features.php");
        exit;
    }

    $jobQuery = mysqli_query($conn, "
        SELECT *
        FROM jobs
        WHERE job_id = '$job_id'
        LIMIT 1
    ");

    if (!$jobQuery || mysqli_num_rows($jobQuery) == 0) {
        die("Job not found.");
    }

    $job = mysqli_fetch_assoc($jobQuery);

    if (intval($job['is_external']) != 1) {
        header("Location: apply_job.php?job_id=" . $job_id);
        exit;
    }

    $amount = ESEWA_TEST_MODE ? 1 : EXTERNAL_JOB_APPLY_FEE;

} else {

    $paidCheck = mysqli_query($conn, "
        SELECT *
        FROM chatbot_access
        WHERE user_id = '$user_id'
        AND is_paid = 1
        LIMIT 1
    ");

    if ($paidCheck && mysqli_num_rows($paidCheck) > 0) {
        $_SESSION['success_message'] = "You already have chatbot pro access.";
        header("Location: pro_features.php");
        exit;
    }

    $amount = ESEWA_TEST_MODE ? 1 : CHATBOT_ACCESS_FEE;
}

$amount = number_format((float)$amount, 2, '.', '');
$taxAmount = "0";
$serviceCharge = "0";
$deliveryCharge = "0";
$totalAmount = $amount;

$transaction_uuid = "CPAI" . $user_id . time();

$safe_purpose = mysqli_real_escape_string($conn, $purpose);
$safe_transaction_uuid = mysqli_real_escape_string($conn, $transaction_uuid);

$insertPayment = mysqli_query($conn, "
    INSERT INTO payments 
    (user_id, job_id, purpose, amount, transaction_uuid, payment_status)
    VALUES 
    ('$user_id', " . ($job_id ? "'$job_id'" : "NULL") . ", '$safe_purpose', '$amount', '$safe_transaction_uuid', 'Pending')
");

if (!$insertPayment) {
    die("Payment creation failed: " . mysqli_error($conn));
}

$signature = generateEsewaSignature($totalAmount, $transaction_uuid);

$success_url = BASE_URL . "/payment_success.php";
$failure_url = BASE_URL . "/payment_failure.php?transaction_uuid=" . urlencode($transaction_uuid);

$local_success_url = "payment_success.php?transaction_uuid=" . urlencode($transaction_uuid) . "&local_test=1";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment | CareerPilot AI</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8fafc;
    color: #0f172a;
}

.payment-box {
    max-width: 500px;
    margin: 70px auto;
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    text-align: center;
}

h2 {
    color: #1d4ed8;
    margin-bottom: 10px;
}

.amount {
    font-size: 34px;
    font-weight: 800;
    color: #111827;
    margin: 22px 0;
}

.btn {
    display: block;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    font-weight: 800;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    margin-top: 14px;
}

.esewa-btn {
    background: #60bb46;
    color: white;
}

.test-btn {
    background: #2563eb;
    color: white;
}

.back-btn {
    background: #e5e7eb;
    color: #111827;
}

.note {
    color: #64748b;
    font-size: 14px;
    margin-top: 18px;
    line-height: 1.5;
}
</style>
</head>

<body>

<div class="payment-box">
    <h2>CareerPilot AI Payment</h2>

    <?php if ($purpose == 'job_apply') { ?>
        <p>You need to pay to apply for this external job.</p>
    <?php } else { ?>
        <p>Unlock CareerPilot AI chatbot after 5 free messages.</p>
    <?php } ?>

    <div class="amount">NPR <?php echo htmlspecialchars($amount); ?></div>

    <form action="<?php echo ESEWA_PAYMENT_URL; ?>" method="POST">
        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
        <input type="hidden" name="tax_amount" value="<?php echo htmlspecialchars($taxAmount); ?>">
        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($totalAmount); ?>">
        <input type="hidden" name="transaction_uuid" value="<?php echo htmlspecialchars($transaction_uuid); ?>">
        <input type="hidden" name="product_code" value="<?php echo htmlspecialchars(ESEWA_PRODUCT_CODE); ?>">
        <input type="hidden" name="product_service_charge" value="<?php echo htmlspecialchars($serviceCharge); ?>">
        <input type="hidden" name="product_delivery_charge" value="<?php echo htmlspecialchars($deliveryCharge); ?>">
        <input type="hidden" name="success_url" value="<?php echo htmlspecialchars($success_url); ?>">
        <input type="hidden" name="failure_url" value="<?php echo htmlspecialchars($failure_url); ?>">
        <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
        <input type="hidden" name="signature" value="<?php echo htmlspecialchars($signature); ?>">

        <button type="submit" class="btn esewa-btn">Pay with eSewa Sandbox</button>
    </form>

    <a href="<?php echo htmlspecialchars($local_success_url); ?>" class="btn test-btn">
        Simulate Successful Payment
    </a>

    <a href="pro_features.php" class="btn back-btn">
        Back to Pro Features
    </a>

    <div class="note">
        For localhost demo, use Simulate Successful Payment.
    </div>
</div>

</body>
</html>