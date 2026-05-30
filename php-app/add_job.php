<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')) {
    header("Location: login.php");
    exit;
}

$jobResult = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);

    $query = "INSERT INTO jobs(title, company, description, required_skills, location, salary)
              VALUES('$title', '$company', '$description', '$skills', '$location', '$salary')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Job posted successfully.";
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $jobResult = "error";
    }
}

$admin_name = $_SESSION['name'] ?? "Admin";
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Job | AI JobMatch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            min-height:100vh;
            background:#f4f8ff;
            color:#1e293b;
        }

        .topbar{
            width:100%;
            background:white;
            padding:18px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            box-shadow:0 4px 20px rgba(0,0,0,0.05);
            border-bottom:1px solid #e5e7eb;
            position:sticky;
            top:0;
            z-index:1000;
        }

        .topbar-left{
            display:flex;
            align-items:center;
            gap:18px;
        }

        .logo-box{
            width:60px;
            height:60px;
            border-radius:18px;
            background:linear-gradient(135deg,#2563eb,#3b82f6);
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:28px;
            color:white;
            box-shadow:0 8px 20px rgba(37,99,235,0.25);
        }

        .topbar-title{
            font-size:30px;
            color:#1e3a8a;
            margin-bottom:4px;
        }

        .topbar-subtitle{
            color:#64748b;
            font-size:14px;
        }

        .admin-badge{
            display:inline-block;
            background:#dbeafe;
            color:#1d4ed8;
            padding:4px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:700;
            margin-left:8px;
        }

        .topbar-right{
            display:flex;
            align-items:center;
            gap:18px;
            flex-wrap:wrap;
        }

        .welcome-admin{
            display:flex;
            align-items:center;
            gap:14px;
            background:#f8fafc;
            padding:10px 16px;
            border-radius:16px;
            border:1px solid #e2e8f0;
        }

        .admin-avatar{
            width:50px;
            height:50px;
            border-radius:50%;
            background:linear-gradient(135deg,#2563eb,#60a5fa);
            display:flex;
            justify-content:center;
            align-items:center;
            color:white;
            font-size:22px;
            font-weight:bold;
        }

        .welcome-text{
            color:#64748b;
            font-size:13px;
        }

        .welcome-admin h4{
            margin-top:3px;
            font-size:17px;
        }

        .top-btn{
            text-decoration:none;
            padding:12px 20px;
            border-radius:12px;
            font-weight:600;
            transition:0.3s;
        }

        .dashboard-btn{
            background:#eff6ff;
            color:#2563eb;
        }

        .dashboard-btn:hover{
            background:#dbeafe;
            transform:translateY(-2px);
        }

        .logout-btn{
            background:#ef4444;
            color:white;
        }

        .logout-btn:hover{
            background:#dc2626;
            transform:translateY(-2px);
        }

        .container{
            width:90%;
            max-width:1050px;
            margin:40px auto;
        }

        .page-header{
            background:white;
            padding:35px;
            border-radius:24px;
            box-shadow:0 8px 25px rgba(0,0,0,0.05);
            margin-bottom:30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:20px;
            flex-wrap:wrap;
        }

        .page-header h1{
            font-size:42px;
            color:#1e3a8a;
            margin-bottom:8px;
        }

        .page-header p{
            color:#64748b;
            font-size:16px;
        }

        .header-icon{
            background:linear-gradient(135deg,#2563eb,#3b82f6);
            color:white;
            padding:22px 28px;
            border-radius:20px;
            font-size:38px;
            box-shadow:0 10px 25px rgba(37,99,235,0.25);
        }

        .form-card{
            background:white;
            padding:35px;
            border-radius:24px;
            box-shadow:0 10px 30px rgba(0,0,0,0.06);
            border:1px solid #e2e8f0;
        }

        .success-msg,
        .error-msg{
            padding:15px 18px;
            border-radius:14px;
            margin-bottom:22px;
            font-weight:600;
        }

        .success-msg{
            background:#dcfce7;
            color:#166534;
            border:1px solid #bbf7d0;
        }

        .error-msg{
            background:#fee2e2;
            color:#991b1b;
            border:1px solid #fecaca;
        }

        .form-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:22px;
        }

        .form-group{
            margin-bottom:22px;
        }

        .form-group.full{
            grid-column:1 / -1;
        }

        label{
            display:block;
            font-weight:700;
            color:#334155;
            margin-bottom:8px;
            font-size:14px;
        }

        input,
        textarea{
            width:100%;
            padding:15px 16px;
            border-radius:14px;
            border:1px solid #cbd5e1;
            font-size:15px;
            outline:none;
            transition:0.3s;
            background:#f8fafc;
            color:#1e293b;
        }

        textarea{
            min-height:135px;
            resize:vertical;
        }

        input:focus,
        textarea:focus{
            background:white;
            border-color:#2563eb;
            box-shadow:0 0 0 4px rgba(37,99,235,0.12);
        }

        .hint{
            font-size:13px;
            color:#64748b;
            margin-top:6px;
        }

        .btn-row{
            display:flex;
            justify-content:flex-end;
            gap:14px;
            margin-top:10px;
            flex-wrap:wrap;
        }

        .cancel-btn,
        .submit-btn{
            border:none;
            text-decoration:none;
            padding:14px 24px;
            border-radius:14px;
            font-weight:700;
            cursor:pointer;
            transition:0.3s;
            font-size:15px;
        }

        .cancel-btn{
            background:#f1f5f9;
            color:#334155;
        }

        .cancel-btn:hover{
            background:#e2e8f0;
            transform:translateY(-2px);
        }

        .submit-btn{
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
        }

        .submit-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(37,99,235,0.25);
        }

        .submit-btn:disabled{
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

        @media(max-width:850px){
            .topbar{
                flex-direction:column;
                gap:20px;
                padding:20px;
            }

            .topbar-left{
                width:100%;
                justify-content:center;
                flex-direction:column;
                text-align:center;
            }

            .topbar-right{
                width:100%;
                justify-content:center;
            }

            .page-header{
                text-align:center;
                justify-content:center;
            }

            .page-header h1{
                font-size:32px;
            }

            .form-grid{
                grid-template-columns:1fr;
            }

            .btn-row{
                flex-direction:column;
            }

            .cancel-btn,
            .submit-btn{
                width:100%;
                text-align:center;
            }
        }
    </style>

</head>
<body>

<div class="topbar">

    <div class="topbar-left">

        <div class="logo-box">
            ➕
        </div>

        <div>
            <h2 class="topbar-title">
                Add New Job
                <span class="admin-badge">Admin</span>
            </h2>

            <p class="topbar-subtitle">
                Create a new job listing for job seekers
            </p>
        </div>

    </div>

    <div class="topbar-right">

        <div class="welcome-admin">
            <div class="admin-avatar">
                <?php echo $admin_initial; ?>
            </div>

            <div>
                <p class="welcome-text">Welcome</p>
                <h4><?php echo htmlspecialchars($admin_name); ?></h4>
            </div>
        </div>

        <a href="admin_dashboard.php" class="top-btn dashboard-btn">
            ⬅ Dashboard
        </a>

        <a href="logout.php" class="top-btn logout-btn">
            🚪 Logout
        </a>

    </div>

</div>

<div class="container">

    <div class="page-header">

        <div>
            <h1>Post a New Job</h1>
            <p>Fill in job details, required skills, salary and location.</p>
        </div>

        <div class="header-icon">
            💼
        </div>

    </div>

    <div class="form-card">

        <?php if ($jobResult === "success") { ?>

            <div class="success-msg">
                ✅ Job added successfully.
            </div>

        <?php } elseif ($jobResult === "error") { ?>

            <div class="error-msg">
                ⚠️ Error adding job. Please try again.
            </div>

        <?php } ?>

        <form method="POST" id="addJobForm">

            <div class="form-grid">

                <div class="form-group">
                    <label>Job Title</label>
                    <input 
                        type="text" 
                        name="title" 
                        placeholder="Example: Frontend Developer" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Company</label>
                    <input 
                        type="text" 
                        name="company" 
                        placeholder="Example: Tech Nepal" 
                        required
                    >
                </div>

                <div class="form-group full">
                    <label>Job Description</label>
                    <textarea 
                        name="description" 
                        placeholder="Describe the role, responsibilities and requirements..." 
                        required
                    ></textarea>
                </div>

                <div class="form-group full">
                    <label>Required Skills</label>
                    <input 
                        type="text" 
                        name="skills" 
                        placeholder="Example: Python, MySQL, HTML, CSS" 
                        required
                    >
                    <div class="hint">
                        Separate skills using commas. Example: PHP, MySQL, JavaScript
                    </div>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input 
                        type="text" 
                        name="location" 
                        placeholder="Example: Kathmandu" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Salary (Rs.)</label>
                    <input 
                        type="number" 
                        name="salary" 
                        placeholder="Example: 50000" 
                        required
                    >
                </div>

            </div>

            <div class="btn-row">

                <a href="admin_dashboard.php" class="cancel-btn">
                    Cancel
                </a>

                <button type="submit" id="addBtn" class="submit-btn">
                    Post Job
                </button>

            </div>

        </form>

    </div>

</div>

<script>
document.getElementById("addJobForm").addEventListener("submit", function() {
    const btn = document.getElementById("addBtn");
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>Posting job...';
});

const msg = document.querySelector(".success-msg, .error-msg");

if (msg) {
    setTimeout(() => {
        msg.style.opacity = "0";
        msg.style.transition = "opacity .5s";
    }, 4000);
}
</script>

</body>
</html>