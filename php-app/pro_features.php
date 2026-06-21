<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=pro_features.php");
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

/* Check chatbot paid access */
$chatbotPaid = false;

$accessQuery = mysqli_query($conn, "
    SELECT *
    FROM chatbot_access
    WHERE user_id = '$user_id'
    AND is_paid = 1
    LIMIT 1
");

if ($accessQuery && mysqli_num_rows($accessQuery) > 0) {
    $chatbotPaid = true;
}

/* Count chatbot used messages */
$messageCount = 0;

$countQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total_messages
    FROM chat_messages
    WHERE user_id = '$user_id'
    AND sender = 'user'
");

if ($countQuery) {
    $countData = mysqli_fetch_assoc($countQuery);
    $messageCount = intval($countData['total_messages'] ?? 0);
}

/* Fetch payment history */
$paymentQuery = mysqli_query($conn, "
    SELECT 
        payments.*,
        jobs.title AS job_title,
        jobs.company AS company
    FROM payments
    LEFT JOIN jobs ON payments.job_id = jobs.job_id
    WHERE payments.user_id = '$user_id'
    ORDER BY payments.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Features | CareerPilot AI</title>

    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --text2: #334155;
            --text3: #64748b;
            --text4: #94a3b8;
            --line: #e2e8f0;
            --blue: #2563eb;
            --blue2: #1d4ed8;
            --blue-soft: #dbeafe;
            --green: #16a34a;
            --green-soft: #dcfce7;
            --red: #dc2626;
            --red-soft: #fee2e2;
            --yellow: #d97706;
            --yellow-soft: #fef3c7;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 18px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */
        .sidebar {
            width: 290px;
            background: #ffffff;
            border-right: 1px solid var(--line);
            padding: 22px 16px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
        }

        .sb-top {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .sb-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 28px;
            font-weight: 800;
            color: var(--blue2);
            padding: 4px 6px 10px;
        }

        .sb-brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--blue2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sb-user {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sb-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--blue2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sb-user-name {
            font-weight: 700;
            color: var(--text);
            font-size: 15px;
            margin-bottom: 4px;
        }

        .sb-user-role {
            font-size: 13px;
            color: var(--text3);
        }

        .sb-nav {
            display: flex;
            flex-direction: column;
            margin-top: 6px;
        }

        .sb-nav a,
        .sb-nav-bottom a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text2);
            padding: 13px 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .sb-nav a:hover,
        .sb-nav-bottom a:hover {
            background: #eff6ff;
            color: var(--blue2);
        }

        .sb-nav a.active {
            background: var(--blue2);
            color: white;
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
        }

        .sb-section-title {
            padding: 18px 14px 10px;
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text4);
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .notif-badge {
            background: var(--red);
            color: white;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10px;
            margin-left: 6px;
        }

        .sb-notifications {
            padding: 0 6px;
            max-height: 220px;
            overflow-y: auto;
        }

        .notif-item {
            display: block;
            text-decoration: none;
            margin-bottom: 8px;
            border-radius: 12px;
            padding: 12px;
            transition: 0.2s ease;
        }

        .notif-item.unread {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .notif-item.read {
            background: transparent;
            border: 1px solid transparent;
        }

        .notif-text {
            font-size: 12px;
            line-height: 1.45;
        }

        .notif-item.unread .notif-text {
            color: var(--blue2);
            font-weight: 700;
        }

        .notif-item.read .notif-text {
            color: var(--text3);
            font-weight: 500;
        }

        .notif-time {
            margin-top: 6px;
            font-size: 10px;
            color: var(--text4);
        }

        .sb-bottom {
            margin-top: 20px;
        }

        .sb-upgrade {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            color: white;
            padding: 14px 16px;
            border-radius: 14px;
            font-weight: 800;
            margin: 10px 6px 14px;
            transition: 0.2s ease;
        }

        .sb-upgrade:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .sb-nav-bottom {
            padding: 0 6px;
        }

        /* =========================
           MAIN CONTENT
        ========================= */
        .main-content {
            margin-left: 290px;
            width: calc(100% - 290px);
            padding: 34px;
        }

        .page-header {
            background: linear-gradient(135deg, #2563eb 0%, #0f172a 100%);
            color: white;
            padding: 42px 38px;
            border-radius: 28px;
            box-shadow: var(--shadow);
            margin-bottom: 28px;
        }

        .page-header h1 {
            font-size: 54px;
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .page-header p {
            font-size: 16px;
            color: #dbeafe;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 14px;
        }

        .alert-success {
            background: var(--green-soft);
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: var(--red-soft);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .pro-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 30px;
        }

        .pro-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 32px;
            box-shadow: var(--shadow);
        }

        .pro-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 22px;
        }

        .pro-card h2 {
            font-size: 28px;
            margin-bottom: 14px;
            font-weight: 800;
            color: var(--text);
        }

        .pro-card p {
            color: var(--text3);
            line-height: 1.75;
            font-size: 15px;
            margin-bottom: 14px;
        }

        .used-msg {
            font-size: 15px;
            color: var(--text2);
            margin-bottom: 16px;
            font-weight: 600;
        }

        .status {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .status-active {
            background: var(--green-soft);
            color: #166534;
        }

        .status-inactive {
            background: var(--red-soft);
            color: #991b1b;
        }

        .status-info {
            background: var(--yellow-soft);
            color: #92400e;
        }

        .amount {
            font-size: 30px;
            font-weight: 800;
            color: var(--blue2);
            margin: 18px 0 20px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            cursor: pointer;
            background: var(--blue2);
            color: white;
            padding: 14px 20px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 15px;
            transition: 0.2s ease;
        }

        .btn:hover {
            background: #1e40af;
            transform: translateY(-1px);
        }

        .btn-disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .info-box {
            background: #eff6ff;
            color: #1e40af;
            border-radius: 16px;
            padding: 16px;
            line-height: 1.7;
            font-size: 15px;
            border: 1px solid #dbeafe;
        }

        .history-section {
            background: white;
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .history-section h2 {
            font-size: 34px;
            margin-bottom: 20px;
            font-weight: 800;
            color: var(--text);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            color: var(--text2);
            text-align: left;
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--line);
        }

        td {
            padding: 14px 16px;
            font-size: 14px;
            color: var(--text3);
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .badge-completed {
            background: var(--green-soft);
            color: #166534;
        }

        .badge-pending {
            background: var(--yellow-soft);
            color: #92400e;
        }

        .badge-failed {
            background: var(--red-soft);
            color: #991b1b;
        }

        .empty {
            text-align: center;
            color: var(--text4);
            padding: 24px;
            font-size: 14px;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 1100px) {
            .pro-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 240px;
            }

            .main-content {
                margin-left: 240px;
                width: calc(100% - 240px);
                padding: 22px;
            }

            .page-header h1 {
                font-size: 40px;
            }
        }

        @media (max-width: 768px) {
            .app-layout {
                flex-direction: column;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--line);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 18px;
            }

            .page-header {
                padding: 28px 22px;
                border-radius: 20px;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .pro-card,
            .history-section {
                border-radius: 20px;
                padding: 22px;
            }
        }
    </style>
</head>
<body>

<div class="app-layout">

    <?php include "sidebar.php"; ?>

    <main class="main-content">

        <div class="page-header">
            <h1>Pro Features</h1>
            <p>Manage CareerPilot AI paid features, chatbot access, and payment history.</p>
        </div>

        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php } ?>

        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php } ?>

        <div class="pro-grid">

            <div class="pro-card">
                <div class="pro-icon">🤖</div>
                <h2>CareerPilot AI Chatbot Pro</h2>
                <p>You can use 5 free chatbot messages. After that, unlock unlimited CareerPilot AI chatbot support.</p>

                <div class="used-msg">
                    Used Messages: <strong><?php echo $messageCount; ?></strong> / 5 free messages
                </div>

                <?php if ($chatbotPaid) { ?>
                    <div class="status status-active">Active</div>
                    <div class="amount">Already Purchased</div>
                    <button class="btn btn-disabled" disabled>Chatbot Unlocked</button>
                <?php } else { ?>
                    <div class="status status-inactive">Not Active</div>
                    <div class="amount">NPR <?php echo ESEWA_TEST_MODE ? "1.00" : CHATBOT_ACCESS_FEE; ?></div>
                    <a href="payment_start.php?purpose=chatbot_access" class="btn">Pay to Unlock Chatbot</a>
                <?php } ?>
            </div>

            <div class="pro-card">
                <div class="pro-icon">💼</div>
                <h2>External Job Apply Access</h2>
                <p>Scraped/imported jobs from external platforms require payment before applying.</p>

                <div class="status status-info">Job Specific Payment</div>
                <div class="amount">NPR <?php echo ESEWA_TEST_MODE ? "1.00" : EXTERNAL_JOB_APPLY_FEE; ?></div>

                <div class="info-box">
                    External job payment is done only when you click <strong>Apply</strong> on a scraped/external job.
                    Please open the external job and click Apply to continue payment for that selected job.
                </div>
            </div>

        </div>

        <div class="history-section">
            <h2>Payment History</h2>

            <?php if ($paymentQuery && mysqli_num_rows($paymentQuery) > 0) { ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Purpose</th>
                                <th>Job</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction UUID</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($payment = mysqli_fetch_assoc($paymentQuery)) { 
                                $statusClass = "badge-pending";

                                if ($payment['payment_status'] == "Completed") {
                                    $statusClass = "badge-completed";
                                } elseif ($payment['payment_status'] == "Failed") {
                                    $statusClass = "badge-failed";
                                }

                                $purposeText = $payment['purpose'] == "chatbot_access"
                                    ? "Chatbot Access"
                                    : "External Job Apply";
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($purposeText); ?></td>
                                    <td>
                                        <?php
                                        if (!empty($payment['job_title'])) {
                                            echo htmlspecialchars($payment['job_title']) . "<br><small>" . htmlspecialchars($payment['company']) . "</small>";
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                    <td>NPR <?php echo htmlspecialchars($payment['amount']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($payment['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['transaction_uuid']); ?></td>
                                    <td><?php echo date("M d, Y h:i A", strtotime($payment['created_at'])); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="empty">No payment history found.</div>
            <?php } ?>
        </div>

    </main>
</div>

</body>
</html>