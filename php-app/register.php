<?php
session_start();
require_once "db.php";

$error = "";
$success = "";

/* If already logged in, redirect based on role */
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

/* Handle registration form */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($name == "" || $email == "" || $password == "" || $confirm_password == "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Password and confirm password do not match.";
    } else {

        /* Check duplicate email */
        $checkStmt = $conn->prepare("
            SELECT user_id 
            FROM users 
            WHERE email = ? 
            LIMIT 1
        ");

        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "This email is already registered. Please login instead.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $insertStmt = $conn->prepare("
                INSERT INTO users 
                (name, email, password, role)
                VALUES 
                (?, ?, ?, ?)
            ");

            $insertStmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($insertStmt->execute()) {
                $_SESSION['success_message'] = "Registration successful. Please login to continue.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | CareerPilot AI</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: linear-gradient(135deg, #eff6ff, #f8fafc);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.auth-box {
    width: 100%;
    max-width: 460px;
    background: white;
    border-radius: 24px;
    padding: 36px;
    box-shadow: 0 15px 40px rgba(15, 23, 42, 0.10);
    border: 1px solid #e5e7eb;
}

.brand {
    text-align: center;
    margin-bottom: 28px;
}

.brand-icon {
    width: 58px;
    height: 58px;
    background: #2563eb;
    color: white;
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 14px;
}

.brand h1 {
    color: #1d4ed8;
    font-size: 30px;
    margin-bottom: 6px;
}

.brand p {
    color: #64748b;
    font-size: 14px;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: 700;
    color: #334155;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 14px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    font-size: 15px;
    outline: none;
}

input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #dbeafe;
}

.btn {
    width: 100%;
    background: #2563eb;
    color: white;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-weight: 800;
    font-size: 16px;
    cursor: pointer;
    margin-top: 8px;
}

.btn:hover {
    background: #1d4ed8;
}

.alert {
    padding: 13px 15px;
    border-radius: 12px;
    margin-bottom: 18px;
    font-weight: 600;
    font-size: 14px;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.auth-link {
    text-align: center;
    margin-top: 22px;
    color: #64748b;
    font-size: 14px;
}

.auth-link a {
    color: #2563eb;
    font-weight: 800;
    text-decoration: none;
}

.auth-link a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="auth-box">

    <div class="brand">
        <div class="brand-icon">🤖</div>
        <h1>CareerPilot AI</h1>
        <p>Create your account to access AI job recommendations.</p>
    </div>

    <?php if ($error != "") { ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form method="POST" action="register.php">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email address" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Create password" required>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm password" required>
        </div>

        <button type="submit" class="btn">Register</button>

    </form>

    <div class="auth-link">
        Already have an account?
        <a href="login.php">Login here</a>
    </div>

</div>

</body>
</html>