<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        applications.application_id,
        applications.status,
        applications.applied_at,
        jobs.title,
        jobs.company,
        jobs.location,
        jobs.salary,
        jobs.required_skills,
        jobs.description
    FROM applications
    INNER JOIN jobs ON applications.job_id = jobs.job_id
    WHERE applications.user_id = '$user_id'
    ORDER BY applications.applied_at DESC
";

$result = mysqli_query($conn, $query);
$total_applications = mysqli_num_rows($result);
$user_initial = strtoupper(substr($_SESSION['name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | AI JobMatch</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
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

        .topbar-right{
            display:flex;
            align-items:center;
            gap:18px;
            flex-wrap:wrap;
        }

        .welcome-user{
            display:flex;
            align-items:center;
            gap:14px;
            background:#f8fafc;
            padding:10px 16px;
            border-radius:16px;
            border:1px solid #e2e8f0;
        }

        .user-avatar{
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

        .welcome-user h4{
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

        .jobs-btn{
            background:#eff6ff;
            color:#2563eb;
        }

        .jobs-btn:hover{
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
            max-width:1200px;
            margin:40px auto;
        }

        .page-header{
            background:white;
            border-radius:24px;
            padding:35px;
            margin-bottom:35px;
            box-shadow:0 8px 25px rgba(0,0,0,0.05);
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:25px;
            flex-wrap:wrap;
        }

        .page-header h1{
            font-size:42px;
            color:#1e3a8a;
            margin-bottom:10px;
        }

        .page-header p{
            color:#64748b;
            font-size:16px;
        }

        .stat-box{
            background:linear-gradient(135deg,#2563eb,#3b82f6);
            color:white;
            padding:22px 28px;
            border-radius:20px;
            text-align:center;
            min-width:170px;
            box-shadow:0 10px 25px rgba(37,99,235,0.25);
        }

        .stat-box h2{
            font-size:38px;
            margin-bottom:4px;
        }

        .stat-box span{
            font-size:14px;
            color:#dbeafe;
        }

        .application-list{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
            gap:25px;
        }

        .application-card{
            background:white;
            border-radius:22px;
            padding:26px;
            box-shadow:0 8px 25px rgba(0,0,0,0.06);
            border:1px solid #e2e8f0;
            transition:0.3s;
            position:relative;
            overflow:hidden;
        }

        .application-card:hover{
            transform:translateY(-6px);
            box-shadow:0 15px 35px rgba(37,99,235,0.15);
        }

        .application-card::before{
            content:"";
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:5px;
            background:linear-gradient(90deg,#16a34a,#22c55e);
        }

        .application-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:20px;
            margin-bottom:18px;
        }

        .application-header h2{
            color:#1e3a8a;
            margin-bottom:6px;
            font-size:27px;
        }

        .company{
            color:#64748b;
            font-size:15px;
        }

        .status-badge{
            padding:10px 16px;
            border-radius:999px;
            font-weight:700;
            white-space:nowrap;
            font-size:14px;
        }

        .status-applied{
            background:#dbeafe;
            color:#1d4ed8;
        }

        .status-reviewed{
            background:#fef3c7;
            color:#92400e;
        }

        .status-selected{
            background:#dcfce7;
            color:#15803d;
        }

        .status-rejected{
            background:#fee2e2;
            color:#b91c1c;
        }

        .job-meta{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin:18px 0;
        }

        .job-meta-item{
            background:#f8fafc;
            border:1px solid #e2e8f0;
            color:#334155;
            padding:9px 12px;
            border-radius:12px;
            font-size:14px;
        }

        .job-desc{
            color:#334155;
            line-height:1.7;
            margin:18px 0;
        }

        .applied-date{
            color:#64748b;
            font-size:14px;
            margin-top:18px;
            padding-top:16px;
            border-top:1px solid #e2e8f0;
        }

        .empty-box{
            background:white;
            padding:60px 30px;
            text-align:center;
            border-radius:24px;
            box-shadow:0 8px 25px rgba(0,0,0,0.05);
        }

        .empty-icon{
            width:90px;
            height:90px;
            background:#eff6ff;
            color:#2563eb;
            border-radius:50%;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:42px;
            margin:0 auto 22px;
        }

        .empty-box h3{
            color:#1e3a8a;
            font-size:30px;
            margin-bottom:10px;
        }

        .empty-box p{
            color:#64748b;
            margin-bottom:25px;
        }

        .browse-btn{
            display:inline-block;
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
            text-decoration:none;
            padding:13px 26px;
            border-radius:12px;
            font-weight:600;
            transition:0.3s;
        }

        .browse-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(37,99,235,0.25);
        }

        @media(max-width:900px){
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
                font-size:34px;
            }

            .application-header{
                flex-direction:column;
            }
        }
    </style>

</head>
<body>

<div class="topbar">

    <div class="topbar-left">

        <div class="logo-box">
            
        </div>

        <div>
            <h2 class="topbar-title">
                My Applications
            </h2>

            <p class="topbar-subtitle">
                Track all your job applications
            </p>
        </div>

    </div>

    <div class="topbar-right">

        <div class="welcome-user">

            <div class="user-avatar">
                <?php echo $user_initial; ?>
            </div>

            <div>
                <p class="welcome-text">Welcome</p>

                <h4>
                    <?php echo htmlspecialchars($_SESSION['name']); ?>
                </h4>
            </div>

        </div>

        <a href="user_dashboard.php" class="top-btn jobs-btn">
             Available Jobs
        </a>

        <a href="logout.php" class="top-btn logout-btn">
             Logout
        </a>

    </div>

</div>

<div class="container">

    <div class="page-header">

        <div>
            <h1>My Applications</h1>
            <p>Here you can see all jobs you have applied for.</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $total_applications; ?></h2>
            <span>Total Applications</span>
        </div>

    </div>

    <?php if ($total_applications > 0) { ?>

        <div class="application-list">

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <?php
                    $status_class = "status-applied";

                    if ($row['status'] == "Reviewed") {
                        $status_class = "status-reviewed";
                    } elseif ($row['status'] == "Selected") {
                        $status_class = "status-selected";
                    } elseif ($row['status'] == "Rejected") {
                        $status_class = "status-rejected";
                    }
                ?>

                <div class="application-card">

                    <div class="application-header">

                        <div>
                            <h2>
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h2>

                            <p class="company">
                                <?php echo htmlspecialchars($row['company']); ?>
                            </p>
                        </div>

                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>

                    </div>

                    <div class="job-meta">

                        <span class="job-meta-item">
                             <?php echo htmlspecialchars($row['location']); ?>
                        </span>

                        <span class="job-meta-item">
                             Rs. <?php echo htmlspecialchars($row['salary']); ?>
                        </span>

                        <span class="job-meta-item">
                            🛠 <?php echo htmlspecialchars($row['required_skills']); ?>
                        </span>

                    </div>

                    <p class="job-desc">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>

                    <p class="applied-date">
                         Applied on:
                        <?php echo date("d M Y, h:i A", strtotime($row['applied_at'])); ?>
                    </p>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="empty-box">

            <div class="empty-icon">
                
            </div>

            <h3>No Applications Yet</h3>

            <p>
                You have not applied for any job yet. Browse available jobs and start applying.
            </p>

            <a href="user_dashboard.php" class="browse-btn">
                Browse Jobs
            </a>

        </div>

    <?php } ?>

</div>

</body>
</html>