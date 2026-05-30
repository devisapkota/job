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

$location = $_GET['location'] ?? '';
$salary = $_GET['salary'] ?? '';

$query = "SELECT * FROM jobs WHERE 1";

if (!empty($location)) {
    $location_safe = mysqli_real_escape_string($conn, $location);
    $query .= " AND location LIKE '%$location_safe%'";
}

if (!empty($salary)) {
    $salary_safe = floatval($salary);
    $query .= " AND salary >= $salary_safe";
}

$query .= " ORDER BY job_id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | AI Job Recommendation</title>

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

        /* =========================
           TOPBAR
        ========================= */

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

        .application-btn{
            background:#eff6ff;
            color:#2563eb;
        }

        .application-btn:hover{
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

        /* =========================
           CONTAINER
        ========================= */

        .container{
            width:90%;
            max-width:1200px;
            margin:40px auto;
        }

        .dashboard-title{
            text-align:center;
            margin-bottom:35px;
        }

        .dashboard-title h1{
            font-size:42px;
            color:#1e3a8a;
            margin-bottom:10px;
        }

        .dashboard-title p{
            color:#64748b;
            font-size:16px;
        }

        /* =========================
           FILTER BOX
        ========================= */

        .filter-box{
            background:white;
            padding:24px;
            border-radius:20px;

            display:flex;
            gap:15px;
            flex-wrap:wrap;
            align-items:center;

            box-shadow:0 8px 25px rgba(0,0,0,0.05);

            margin-bottom:35px;
        }

        .filter-box input{
            flex:1;
            min-width:220px;

            padding:14px 16px;
            border-radius:12px;
            border:1px solid #cbd5e1;

            font-size:15px;
            outline:none;

            transition:0.3s;
        }

        .filter-box input:focus{
            border-color:#2563eb;
            box-shadow:0 0 0 4px rgba(37,99,235,0.1);
        }

        .filter-box button{
            padding:14px 22px;
            border:none;
            border-radius:12px;

            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;

            font-size:15px;
            font-weight:600;
            cursor:pointer;

            transition:0.3s;
        }

        .filter-box button:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(37,99,235,0.25);
        }

        .clear-btn{
            text-decoration:none;
            color:#ef4444;
            font-weight:600;
        }

        /* =========================
           JOB LIST
        ========================= */

        .job-list{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
            gap:25px;
        }

        /* =========================
           JOB CARD
        ========================= */

        .job-card{
            background:white;
            border-radius:22px;
            padding:26px;

            box-shadow:0 8px 25px rgba(0,0,0,0.06);

            border:1px solid #e2e8f0;

            transition:0.3s;

            position:relative;
            overflow:hidden;
        }

        .job-card:hover{
            transform:translateY(-6px);
            box-shadow:0 15px 35px rgba(37,99,235,0.15);
        }

        .job-card::before{
            content:"";
            position:absolute;
            top:0;
            left:0;

            width:100%;
            height:5px;

            background:linear-gradient(90deg,#2563eb,#60a5fa);
        }

        .job-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:20px;

            margin-bottom:20px;
        }

        .job-header h2{
            color:#1e3a8a;
            margin-bottom:6px;
            font-size:28px;
        }

        .company{
            color:#64748b;
            font-size:15px;
        }

        .salary{
            background:#dcfce7;
            color:#15803d;

            padding:10px 16px;
            border-radius:12px;

            font-weight:700;
            white-space:nowrap;
        }

        .job-card p{
            margin-bottom:14px;
            line-height:1.7;
            color:#334155;
        }

        .job-card strong{
            color:#0f172a;
        }

        /* =========================
           BUTTONS
        ========================= */

        .job-actions{
            margin-top:20px;
        }

        .apply-btn{
            display:inline-block;

            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;

            text-decoration:none;

            padding:12px 24px;
            border-radius:12px;

            font-weight:600;

            transition:0.3s;
        }

        .apply-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(37,99,235,0.25);
        }

        /* =========================
           NO JOB
        ========================= */

        .no-job{
            background:white;
            padding:50px;
            text-align:center;
            border-radius:20px;

            grid-column:1/-1;

            box-shadow:0 8px 25px rgba(0,0,0,0.05);

            color:#64748b;
            font-size:20px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

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

            .dashboard-title h1{
                font-size:34px;
            }

            .job-header{
                flex-direction:column;
            }

            .filter-box{
                flex-direction:column;
                align-items:stretch;
            }

            .filter-box button{
                width:100%;
            }
        }

    </style>

</head>
<body>

<!-- TOPBAR -->

<div class="topbar">

    <div class="topbar-left">

        <div class="logo-box">
            
        </div>

        <div>
            <h2 class="topbar-title">
                AI Job Recommendation System
            </h2>

            <p class="topbar-subtitle">
                Smart career matching platform
            </p>
        </div>

    </div>

    <div class="topbar-right">

        <div class="welcome-user">

            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
            </div>

            <div>
                <p class="welcome-text">Welcome</p>

                <h4>
                    <?php echo htmlspecialchars($_SESSION['name']); ?>
                </h4>
            </div>

        </div>

        <a href="my_applications.php" class="top-btn application-btn">
             My Applications
        </a>

        <a href="logout.php" class="top-btn logout-btn">
             Logout
        </a>

    </div>

</div>

<!-- MAIN CONTAINER -->

<div class="container">

    <div class="dashboard-title">

        <h1>Available Jobs</h1>

        <p>
            Search jobs, filter by location or salary, and apply easily.
        </p>

    </div>

    <!-- FILTER -->

    <form method="GET" class="filter-box">

        <input
            type="text"
            name="location"
            placeholder="Search by location"
            value="<?php echo htmlspecialchars($location); ?>"
        >

        <input
            type="number"
            name="salary"
            placeholder="Minimum salary"
            value="<?php echo htmlspecialchars($salary); ?>"
        >

        <button type="submit">
            Filter Jobs
        </button>

        <a href="user_dashboard.php" class="clear-btn">
            Clear
        </a>

    </form>

    <!-- JOB LIST -->

    <div class="job-list">

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <?php while ($job = mysqli_fetch_assoc($result)) { ?>

                <div class="job-card">

                    <div class="job-header">

                        <div>

                            <h2>
                                <?php echo htmlspecialchars($job['title']); ?>
                            </h2>

                            <p class="company">
                                <?php echo htmlspecialchars($job['company']); ?>
                            </p>

                        </div>

                        <div class="salary">
                            Rs. <?php echo htmlspecialchars($job['salary']); ?>
                        </div>

                    </div>

                    <p>
                        <strong> Location:</strong>
                        <?php echo htmlspecialchars($job['location']); ?>
                    </p>

                    <p>
                        <strong> Required Skills:</strong>
                        <?php echo htmlspecialchars($job['required_skills']); ?>
                    </p>

                    <p>
                        <strong> Description:</strong>
                        <?php echo htmlspecialchars($job['description']); ?>
                    </p>

                    <div class="job-actions">

                        <a
                            class="apply-btn"
                            href="apply_job.php?job_id=<?php echo $job['job_id']; ?>"
                        >
                            Apply Now
                        </a>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="no-job">
                No jobs found.
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>