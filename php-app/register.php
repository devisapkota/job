<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $role = "user";

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $message = "Email already exists.";
    } else {
        mysqli_query($conn,"INSERT INTO users(name,email,password,role) VALUES('$name','$email','$password','$role')");
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join CareerPilot AI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="register-page">

    <header class="reg-topbar">
        <div class="reg-topbar-brand">
            <div class="brand-icon"><svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
            CareerPilot AI
        </div>
        <div class="reg-topbar-right">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </header>

    <div class="reg-body">
        <div class="reg-card">
            <h1 class="reg-title">Create your account</h1>
            <p class="reg-sub">Join thousands of job seekers using AI to land their dream jobs.</p>

            <?php if ($message != ""): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm">
                <div class="form-row">
                    <label class="form-label">Full Name</label>
                    <div class="input-wrap">
                        <span class="iicon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                        <input type="text" name="name" placeholder="name" required>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <span class="iicon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                        <input type="email" name="email" placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <span class="iicon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <input type="password" name="password" placeholder="Min. 8 characters" required>
                    </div>
                </div>

                

                <button type="submit" class="btn-primary" id="regBtn">
                    Get Started for Free
                </button>
            </form>

            
        </div>
    </div>

    <footer class="page-footer">
        <div>© 2026 CareerPilot AI. All rights reserved.</div>
        <div>
            <a href="#">Help Center</a>
            <a href="#">Security</a>
            <a href="#">Status</a>
        </div>
    </footer>

    <script>
    document.getElementById("registerForm").addEventListener("submit", function() {
        const btn = document.getElementById("regBtn");
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Creating account...';
    });
    </script>
</body>
</html>
