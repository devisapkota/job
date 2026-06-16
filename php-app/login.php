<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php"); // Redirect to chat/ai assistant
            }
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | CareerPilot AI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-icon"><svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
            <div class="login-logo-text">CareerPilot AI</div>
        </div>

        <h1 class="login-title">Welcome Back</h1>
        <p class="login-sub">Sign in to continue your career journey.</p>

        <?php if ($message != ""): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-row">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <span class="iicon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                    <input type="email" name="email" placeholder="name@company.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="pw-row">
                    <label class="form-label">Password</label>
                    <a href="#">Forgot?</a>
                </div>
                <div class="input-wrap">
                    <span class="iicon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <span class="iright" onclick="togglePassword()"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                </div>
            </div>

            <div class="check-row">
                <input type="checkbox" id="remember" name="remember">
            </div>

            <button type="submit" class="btn-primary" id="loginBtn">
                Sign In
            </button>
        </form>

        <div class="login-footer-bar">
            Don't have an account? <a href="register.php">Create a New account </a>
        </div>
    </div>

    <footer class="page-footer">
        <div>© 2026 CareerPilot AI</div>
        <div>
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Help</a>
        </div>
    </footer>

    <script>
    function togglePassword() {
        const pw = document.getElementById("password");
        pw.type = pw.type === "password" ? "text" : "password";
    }

    document.getElementById("loginForm").addEventListener("submit", function() {
        const btn = document.getElementById("loginBtn");
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Signing in...';
    });
    </script>
</body>
</html>
