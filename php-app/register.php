<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Force every new registration to be Job Seeker only
    $role = "user";

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $message = "Email already exists.";
    } else {

        mysqli_query($conn,"
            INSERT INTO users(name,email,password,role)
            VALUES('$name','$email','$password','$role')
        ");

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
    <title>Register | AI JobMatch</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            min-height:100vh;
            background:linear-gradient(135deg,#0f172a,#2563eb);
            display:flex;
            justify-content:center;
            align-items:center;
            padding:30px;
        }

        .register-wrapper{
            width:1000px;
            min-height:620px;
            background:white;
            border-radius:28px;
            overflow:hidden;
            display:grid;
            grid-template-columns:1fr 1fr;
            box-shadow:0 30px 70px rgba(0,0,0,0.28);
        }

        .register-left{
            background:linear-gradient(135deg,#2563eb,#1e3a8a);
            color:white;
            padding:55px 45px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .register-left h2{
            font-size:38px;
            line-height:1.2;
            margin-bottom:18px;
        }

        .register-left p{
            color:#dbeafe;
            line-height:1.7;
            font-size:16px;
            margin-bottom:28px;
        }

        .feature-list{
            display:grid;
            gap:14px;
        }

        .feature-list div{
            background:rgba(255,255,255,0.14);
            padding:13px 15px;
            border-radius:14px;
        }

        .register-right{
            padding:50px 45px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .register-right h2{
            font-size:34px;
            color:#111827;
            margin-bottom:6px;
        }

        .subtitle{
            color:#64748b;
            margin-bottom:26px;
        }

        .error-msg{
            background:#fee2e2;
            color:#991b1b;
            padding:13px;
            border-radius:12px;
            margin-bottom:18px;
            font-size:14px;
        }

        .form-group{
            margin-bottom:17px;
        }

        .form-group label{
            display:block;
            font-weight:700;
            color:#374151;
            font-size:14px;
            margin-bottom:7px;
        }

        .form-group input{
            width:100%;
            padding:14px;
            border:1px solid #d1d5db;
            border-radius:13px;
            font-size:15px;
            background:#f9fafb;
            outline:none;
            transition:0.3s;
        }

        .form-group input:focus{
            border-color:#2563eb;
            background:white;
            box-shadow:0 0 0 4px rgba(37,99,235,0.12);
        }

        .readonly-input{
            background:#e2e8f0 !important;
            color:#475569;
            cursor:not-allowed;
            font-weight:600;
        }

        .register-btn{
            width:100%;
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
            border:none;
            padding:15px;
            border-radius:13px;
            font-size:16px;
            font-weight:700;
            cursor:pointer;
            margin-top:6px;
            transition:0.3s;
        }

        .register-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 12px 25px rgba(37,99,235,0.25);
        }

        .register-btn:disabled{
            background:#93c5fd;
            cursor:not-allowed;
        }

        .spinner{
            width:15px;
            height:15px;
            border:2px solid rgba(255,255,255,0.4);
            border-top-color:white;
            display:inline-block;
            border-radius:50%;
            animation:spin 0.7s linear infinite;
            margin-right:7px;
            vertical-align:middle;
        }

        @keyframes spin{
            to{
                transform:rotate(360deg);
            }
        }

        .login-text{
            text-align:center;
            margin-top:24px;
            color:#64748b;
        }

        .login-text a{
            color:#2563eb;
            font-weight:700;
            text-decoration:none;
        }

        .login-text a:hover{
            text-decoration:underline;
        }

        @media(max-width:850px){
            .register-wrapper{
                width:95%;
                grid-template-columns:1fr;
            }

            .register-left{
                padding:35px;
                text-align:center;
            }

            .register-right{
                padding:35px;
            }
        }
    </style>

</head>
<body>

<div class="register-wrapper">

    <div class="register-left">

        <h2>Start your smart career journey</h2>

        <p>
            Create your job seeker account, upload your resume, get AI-based job recommendations,
            and discover your missing skills.
        </p>

        <div class="feature-list">
            <div>✅ AI-based job recommendation</div>
            <div>✅ Resume skill extraction</div>
            <div>✅ ATS resume analysis</div>
            <div>✅ Skill gap detection</div>
        </div>

    </div>

    <div class="register-right">

        <h2>Create Account</h2>
        <p class="subtitle">Register as a Job Seeker</p>

        <?php if ($message != "") { ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <form method="POST" id="registerForm">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a strong password" required>
            </div>

            <div class="form-group">
                <label>Account Type</label>
                <input type="text" value="Job Seeker" class="readonly-input" readonly>
            </div>

            <button type="submit" id="registerBtn" class="register-btn">
                Create Account
            </button>

        </form>

        <p class="login-text">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>

</div>

<script>
document.getElementById("registerForm").addEventListener("submit", function() {
    const btn = document.getElementById("registerBtn");
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>Creating account...';
});
</script>

</body>
</html>