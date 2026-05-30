<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')) {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY job_id DESC");
$total_jobs = mysqli_num_rows($result);
$admin_name = $_SESSION['name'] ?? "Admin";
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AI JobMatch</title>

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

        .add-btn{
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
        }

        .add-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(37,99,235,0.25);
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
            width:92%;
            max-width:1250px;
            margin:40px auto;
        }

        .page-header{
            background:white;
            border-radius:24px;
            padding:35px;
            margin-bottom:30px;
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
            padding:22px 32px;
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
            color:#dbeafe;
            font-size:14px;
        }

        .table-card{
            background:white;
            border-radius:24px;
            overflow:hidden;
            box-shadow:0 10px 30px rgba(0,0,0,0.06);
            border:1px solid #e2e8f0;
        }

        .table-top{
            padding:22px 26px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            border-bottom:1px solid #e2e8f0;
            flex-wrap:wrap;
            gap:15px;
        }

        .table-top h2{
            color:#1e3a8a;
            font-size:24px;
        }

        .table-top p{
            color:#64748b;
            font-size:14px;
        }

        .table-wrap{
            overflow-x:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
            min-width:950px;
        }

        thead{
            background:#1e3a8a;
            color:white;
        }

        th{
            padding:16px;
            text-align:left;
            font-size:14px;
            white-space:nowrap;
        }

        td{
            padding:16px;
            border-bottom:1px solid #e2e8f0;
            color:#334155;
            vertical-align:top;
        }

        tbody tr{
            transition:0.2s;
        }

        tbody tr:hover{
            background:#f8fafc;
        }

        .job-title{
            color:#1e3a8a;
            font-weight:700;
            font-size:16px;
        }

        .skill-pill{
            display:inline-block;
            background:#eff6ff;
            color:#1d4ed8;
            padding:6px 10px;
            border-radius:999px;
            font-size:13px;
            margin:2px;
        }

        .salary-pill{
            background:#dcfce7;
            color:#15803d;
            padding:8px 12px;
            border-radius:999px;
            font-weight:700;
            white-space:nowrap;
            display:inline-block;
        }

        .action-box{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

        .action-btn{
            text-decoration:none;
            padding:9px 13px;
            border-radius:10px;
            font-size:13px;
            font-weight:700;
            transition:0.2s;
            display:inline-block;
        }

        .edit-btn{
            background:#dbeafe;
            color:#1d4ed8;
        }

        .edit-btn:hover{
            background:#bfdbfe;
        }

        .delete-btn{
            background:#fee2e2;
            color:#dc2626;
        }

        .delete-btn:hover{
            background:#fecaca;
        }

        .empty-row{
            text-align:center;
            padding:40px;
            color:#64748b;
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
        }
    </style>

</head>
<body>

<div class="topbar">

    <div class="topbar-left">

        <div class="logo-box">
            🛠
        </div>

        <div>
            <h2 class="topbar-title">
                AI JobMatch
                <span class="admin-badge">Admin</span>
            </h2>

            <p class="topbar-subtitle">
                Manage job listings and opportunities
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

        <a href="add_job.php" class="top-btn add-btn">
            + Add New Job
        </a>

        <a href="logout.php" class="top-btn logout-btn">
             Logout
        </a>

    </div>

</div>

<div class="container">

    <div class="page-header">

        <div>
            <h1>Job Listings</h1>
            <p>View, edit and delete all posted jobs from here.</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $total_jobs; ?></h2>
            <span>Total Jobs</span>
        </div>

    </div>

    <div class="table-card">

        <div class="table-top">
            <div>
                <h2>All Posted Jobs</h2>
                <p>Admin can manage job records below.</p>
            </div>

            <a href="add_job.php" class="top-btn add-btn">
                + Add Job
            </a>
        </div>

        <div class="table-wrap">

            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Required Skills</th>
                        <th>Location</th>
                        <th>Salary</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($total_jobs > 0) { ?>

                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                            <tr>
                                <td>
                                    <span class="job-title">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['company']); ?>
                                </td>

                                <td>
                                    <?php
                                        $skills = explode(",", $row['required_skills']);

                                        foreach ($skills as $skill) {
                                            echo "<span class='skill-pill'>" . htmlspecialchars(trim($skill)) . "</span>";
                                        }
                                    ?>
                                </td>

                                <td>
                                     <?php echo htmlspecialchars($row['location']); ?>
                                </td>

                                <td>
                                    <span class="salary-pill">
                                        Rs. <?php echo htmlspecialchars($row['salary']); ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="action-box">

                                        <a 
                                            href="edit_job.php?job_id=<?php echo $row['job_id']; ?>" 
                                            class="action-btn edit-btn"
                                        >
                                             Edit
                                        </a>

                                        <a 
                                            href="delete_job.php?job_id=<?php echo $row['job_id']; ?>" 
                                            class="action-btn delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this job?');"
                                        >
                                            Delete
                                        </a>

                                    </div>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="6" class="empty-row">
                                No jobs found. Click Add Job to create one.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
</html>