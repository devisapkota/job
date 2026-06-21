<?php
$conn = mysqli_connect("localhost", "root", "", "job_recommendation");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

/* ==============================
   eSewa Sandbox Configuration
   ============================== */

if (!defined('ESEWA_PRODUCT_CODE')) {
    define('ESEWA_PRODUCT_CODE', 'EPAYTEST');
}

if (!defined('ESEWA_SECRET_KEY')) {
    define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');
}

if (!defined('ESEWA_PAYMENT_URL')) {
    define('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
}

/*
Change this only if your folder path is different.
Your current path seems:
http://localhost/job-chatbot/php-app
*/

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/job-chatbot/php-app');
}

/*
Payment amounts.
Because ESEWA_TEST_MODE is true, payment amount will be Rs. 1 for demo.
*/

if (!defined('EXTERNAL_JOB_APPLY_FEE')) {
    define('EXTERNAL_JOB_APPLY_FEE', 50);
}

if (!defined('CHATBOT_ACCESS_FEE')) {
    define('CHATBOT_ACCESS_FEE', 100);
}

if (!defined('ESEWA_TEST_MODE')) {
    define('ESEWA_TEST_MODE', true);
}

/* ==============================
   eSewa Signature Function
   ============================== */

if (!function_exists('generateEsewaSignature')) {
    function generateEsewaSignature($totalAmount, $transactionUuid) {
        $message = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code=" . ESEWA_PRODUCT_CODE;

        return base64_encode(
            hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true)
        );
    }
}

/* ==============================
   eSewa Response Verification
   ============================== */

if (!function_exists('verifyEsewaResponse')) {
    function verifyEsewaResponse($encodedData) {
        $json = base64_decode($encodedData, true);

        if (!$json) {
            return false;
        }

        $data = json_decode($json, true);

        if (!is_array($data)) {
            return false;
        }

        $fields = explode(',', $data['signed_field_names'] ?? '');
        $parts = [];

        foreach ($fields as $field) {
            $field = trim($field);

            if (!isset($data[$field])) {
                return false;
            }

            $parts[] = $field . "=" . $data[$field];
        }

        $message = implode(',', $parts);

        $expectedSignature = base64_encode(
            hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true)
        );

        if (!hash_equals($expectedSignature, $data['signature'] ?? '')) {
            return false;
        }

        return $data;
    }
}