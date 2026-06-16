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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Job | CareerPilot AI</title>
    <link rel="stylesheet" href="style.css">
    <style>
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
            font-size:32px;
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

        .cancel-btn{
            border:none;
            text-decoration:none;
            padding:14px 24px;
            border-radius:14px;
            font-weight:700;
            cursor:pointer;
            transition:0.3s;
            font-size:15px;
            background:#f1f5f9;
            color:#334155;
        }

        .cancel-btn:hover{
            background:#e2e8f0;
            transform:translateY(-2px);
        }

        .submit-btn{
            border:none;
            padding:14px 24px;
            border-radius:14px;
            font-weight:700;
            cursor:pointer;
            transition:0.3s;
            font-size:15px;
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

        @media(max-width:850px){
            .page-header{
                text-align:center;
                justify-content:center;
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
<body class="app-page">

<div class="app-layout">

    <?php include "admin_sidebar.php"; ?>

    <main class="app-main">
        <div class="dash-content">

            <div class="page-header">
                <div>
                    <h1>Post a New Job</h1>
                    <p>Fill in job details, required skills, salary and location.</p>
                </div>
                <div class="header-icon">
                    <svg width="38" height="38" fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                </div>
            </div>

            <div class="form-card">
                <?php if ($jobResult === "error") { ?>
                    <div class="error-msg">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Error adding job. Please try again.
                    </div>
                <?php } ?>

                <form method="POST" id="addJobForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="title" placeholder="Example: Frontend Developer" required>
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" name="company" placeholder="Example: Tech Nepal" required>
                        </div>
                        <div class="form-group full">
                            <label>Job Description</label>
                            <textarea name="description" placeholder="Describe the role, responsibilities and requirements..." required></textarea>
                        </div>
                        <div class="form-group full">
                            <label>Required Skills</label>
                            <input type="text" name="skills" placeholder="Example: Python, MySQL, HTML, CSS" required>
                            <div class="hint">Separate skills using commas. Example: PHP, MySQL, JavaScript</div>
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" placeholder="Example: Kathmandu" required>
                        </div>
                        <div class="form-group">
                            <label>Salary (Rs.)</label>
                            <input type="number" name="salary" placeholder="Example: 50000" required>
                        </div>
                    </div>
                    <div class="btn-row">
                        <a href="admin_dashboard.php" class="cancel-btn">Cancel</a>
                        <button type="submit" id="addBtn" class="submit-btn">Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById("addJobForm").addEventListener("submit", function() {
    const btn = document.getElementById("addBtn");
    btn.disabled = true;
    btn.innerHTML = 'Posting job...';
});
</script>

</body>
</html>
