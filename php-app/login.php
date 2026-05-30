<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn,"
        SELECT * FROM users WHERE email='$email'
    ");

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
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

<title>Login | AI JobMatch</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;

    background:linear-gradient(135deg,#dbeafe,#eff6ff);

    display:flex;
    justify-content:center;
    align-items:center;

    padding:30px;
}

/* =========================
   MAIN WRAPPER
========================= */

.auth-wrapper{
    width:100%;
    max-width:1100px;

    background:white;

    border-radius:30px;

    overflow:hidden;

    display:grid;
    grid-template-columns:1fr 1fr;

    box-shadow:0 20px 50px rgba(0,0,0,0.1);
}

/* =========================
   LEFT PANEL
========================= */

.auth-left{
    background:linear-gradient(135deg,#2563eb,#3b82f6);

    color:white;

    padding:70px 50px;

    display:flex;
    flex-direction:column;
    justify-content:center;

    position:relative;
}

.auth-left::before{
    content:"";
    position:absolute;
    width:250px;
    height:250px;

    background:rgba(255,255,255,0.08);

    border-radius:50%;

    top:-60px;
    right:-60px;
}

.auth-left::after{
    content:"";
    position:absolute;
    width:180px;
    height:180px;

    background:rgba(255,255,255,0.08);

    border-radius:50%;

    bottom:-40px;
    left:-40px;
}

.brand{
    font-size:42px;
    font-weight:800;
    margin-bottom:18px;
    position:relative;
    z-index:1;
}

.brand span{
    color:#dbeafe;
}

.auth-left p{
    font-size:17px;
    line-height:1.8;
    margin-bottom:40px;
    color:#e0e7ff;
    position:relative;
    z-index:1;
}

/* FEATURES */

.auth-features{
    display:flex;
    flex-direction:column;
    gap:20px;
    position:relative;
    z-index:1;
}

.auth-feature{
    background:rgba(255,255,255,0.12);

    padding:18px 20px;

    border-radius:18px;

    display:flex;
    align-items:center;
    gap:15px;

    backdrop-filter:blur(8px);

    font-size:15px;
}

.auth-feature-icon{
    width:45px;
    height:45px;

    border-radius:12px;

    background:rgba(255,255,255,0.2);

    display:flex;
    justify-content:center;
    align-items:center;

    font-size:22px;
}

/* =========================
   RIGHT PANEL
========================= */

.auth-right{
    padding:70px 55px;

    display:flex;
    flex-direction:column;
    justify-content:center;
}

.auth-right h2{
    font-size:40px;
    color:#1e3a8a;
    margin-bottom:10px;
}

.auth-subtitle{
    color:#64748b;
    margin-bottom:35px;
    font-size:16px;
}

/* =========================
   ERROR MESSAGE
========================= */

.error-msg{
    background:#fee2e2;
    color:#b91c1c;

    padding:14px 18px;

    border-radius:12px;

    margin-bottom:25px;

    font-size:14px;
}

/* =========================
   FORM
========================= */

.form-group{
    margin-bottom:24px;
}

.form-group label{
    display:block;
    margin-bottom:10px;

    font-weight:600;
    color:#334155;
}

.form-group input{
    width:100%;

    padding:15px 18px;

    border:1px solid #cbd5e1;
    border-radius:14px;

    font-size:15px;

    transition:0.3s;
    outline:none;
}

.form-group input:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,0.1);
}

/* =========================
   BUTTON
========================= */

.btn-full{
    width:100%;

    padding:15px;

    border:none;
    border-radius:14px;

    background:linear-gradient(90deg,#2563eb,#3b82f6);

    color:white;

    font-size:16px;
    font-weight:600;

    cursor:pointer;

    transition:0.3s;
}

.btn-full:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(37,99,235,0.25);
}

/* =========================
   FOOTER
========================= */

.auth-footer{
    margin-top:25px;

    text-align:center;

    color:#64748b;
}

.auth-footer a{
    color:#2563eb;
    font-weight:600;
    text-decoration:none;
}

.auth-footer a:hover{
    text-decoration:underline;
}

/* =========================
   LOADING SPINNER
========================= */

.spinner{
    width:18px;
    height:18px;

    border:3px solid rgba(255,255,255,0.3);
    border-top:3px solid white;

    border-radius:50%;

    display:inline-block;

    animation:spin 1s linear infinite;

    margin-right:8px;
    vertical-align:middle;
}

@keyframes spin{
    100%{
        transform:rotate(360deg);
    }
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px){

    .auth-wrapper{
        grid-template-columns:1fr;
    }

    .auth-left{
        padding:50px 30px;
    }

    .auth-right{
        padding:50px 30px;
    }

    .brand{
        font-size:34px;
    }

    .auth-right h2{
        font-size:32px;
    }
}

</style>

</head>

<body>

<div class="auth-wrapper">

    <!-- LEFT PANEL -->

    <div class="auth-left">

        
        <p>
            Find the best jobs based on your skills,
            resume and career profile.
        </p>

        <div class="auth-features">

            <div class="auth-feature">
                <div class="auth-feature-icon"></div>
                AI-powered job recommendations
            </div>

            <div class="auth-feature">
                <div class="auth-feature-icon"></div>
                Resume parsing & skill extraction
            </div>

            <div class="auth-feature">
                <div class="auth-feature-icon"></div>
                Match score & skill gap analysis
            </div>

        </div>

    </div>

    <!-- RIGHT PANEL -->

    <div class="auth-right">

        <h2>Welcome Back</h2>

        <p class="auth-subtitle">
            Login to your account to continue
        </p>

        <?php if ($message != ""): ?>

            <div class="error-msg">
                 <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" id="loginForm">

            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button
                type="submit"
                id="loginBtn"
                class="btn-full"
            >
                Login
            </button>

        </form>

        <p class="auth-footer">
            Don't have an account?
            <a href="register.php">Register now</a>
        </p>

    </div>

</div>

<script>

document.getElementById("loginForm").addEventListener("submit", function(){

    const btn = document.getElementById("loginBtn");

    btn.disabled = true;

    btn.innerHTML =
        '<span class="spinner"></span>Logging in...';

});

</script>

</body>
</html>