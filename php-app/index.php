<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['name'] ?? "User";
$user_initial = strtoupper(substr($user_name, 0, 1));

$selected_job = null;

if (isset($_SESSION['selected_job_id'])) {
    $job_id = $_SESSION['selected_job_id'];
    $jobResult = mysqli_query($conn, "SELECT * FROM jobs WHERE job_id='$job_id'");
    $selected_job = mysqli_fetch_assoc($jobResult);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AI Job Recommendation Chatbot</title>

    <style>
        body{
            margin:0;
            font-family:'Segoe UI',sans-serif;
            background:#f4f8ff;
            color:#1e293b;
        }

        .topbar{
            background:white;
            padding:18px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 4px 20px rgba(0,0,0,0.05);
        }

        .logo{
            display:flex;
            align-items:center;
            gap:15px;
        }

        .logo-icon{
            width:55px;
            height:55px;
            background:linear-gradient(135deg,#2563eb,#3b82f6);
            color:white;
            border-radius:16px;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:28px;
        }

        .logo h2{
            color:#1e3a8a;
            margin:0;
        }

        .nav a{
            text-decoration:none;
            padding:11px 18px;
            border-radius:12px;
            font-weight:600;
            margin-left:10px;
        }

        .nav .jobs{
            background:#eff6ff;
            color:#2563eb;
        }

        .nav .logout{
            background:#ef4444;
            color:white;
        }

        .container{
            width:90%;
            max-width:1150px;
            margin:40px auto;
            display:grid;
            grid-template-columns:1.4fr .8fr;
            gap:25px;
        }

        .chat-card,
        .side-card{
            background:white;
            border-radius:24px;
            box-shadow:0 8px 25px rgba(0,0,0,.06);
            overflow:hidden;
        }

        .chat-header{
            background:linear-gradient(135deg,#2563eb,#3b82f6);
            color:white;
            padding:28px;
        }

        .chat-header h1{
            margin:0 0 8px;
        }

        .chat-header p{
            margin:0;
            color:#dbeafe;
        }

        .chat-box{
            height:430px;
            overflow-y:auto;
            background:#f8fafc;
            margin:24px;
            padding:20px;
            border-radius:20px;
            border:1px solid #e2e8f0;
        }

        .bot-msg,
        .user-msg{
            padding:15px 18px;
            border-radius:18px;
            margin-bottom:15px;
            line-height:1.6;
            max-width:88%;
        }

        .bot-msg{
            background:white;
            border:1px solid #e2e8f0;
        }

        .user-msg{
            background:#2563eb;
            color:white;
            margin-left:auto;
        }

        .input-row{
            display:flex;
            gap:12px;
            padding:0 24px 24px;
        }

        .input-row input{
            flex:1;
            padding:14px;
            border-radius:14px;
            border:1px solid #cbd5e1;
        }

        button{
            border:none;
            background:linear-gradient(90deg,#2563eb,#3b82f6);
            color:white;
            padding:14px 22px;
            border-radius:14px;
            font-weight:700;
            cursor:pointer;
        }

        .side-card{
            padding:24px;
            margin-bottom:25px;
        }

        .side-card h3{
            color:#1e3a8a;
            margin-bottom:10px;
        }

        .selected-job{
            border-left:6px solid #2563eb;
        }

        input[type="file"],
        .filter-input{
            width:100%;
            padding:14px;
            border-radius:14px;
            border:1px solid #cbd5e1;
            margin:10px 0;
            box-sizing:border-box;
        }

        .success-alert{
            background:#dcfce7;
            color:#166534;
            padding:16px;
            border-radius:14px;
            margin-bottom:20px;
            border:1px solid #bbf7d0;
            font-weight:600;
        }

        #toast{
            visibility:hidden;
            position:fixed;
            right:25px;
            bottom:25px;
            background:#0f172a;
            color:white;
            padding:14px 20px;
            border-radius:12px;
        }

        #toast.show{
            visibility:visible;
        }

        @media(max-width:900px){
            .container{
                grid-template-columns:1fr;
            }

            .topbar{
                flex-direction:column;
                gap:15px;
            }
        }
    </style>
</head>

<body>

<div class="topbar">
    <div class="logo">
        <div class="logo-icon">🤖</div>
        <div>
            <h2>AI Job Recommendation Chatbot</h2>
            <p>Welcome, <?php echo htmlspecialchars($user_name); ?></p>
        </div>
    </div>

    <div class="nav">
        <a href="user_dashboard.php" class="jobs">Available Jobs</a>
        <a href="my_applications.php" class="jobs">My Applications</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <div>

        <?php if(isset($_SESSION['chat_message'])) { ?>
            <div class="success-alert">
                <?php 
                    echo $_SESSION['chat_message']; 
                    unset($_SESSION['chat_message']);
                ?>
            </div>
        <?php } ?>

        <div class="chat-card">

            <div class="chat-header">
                <h1>Career AI Assistant</h1>
                <p>Upload your resume or enter skills to get recommendations.</p>
            </div>

            <div class="chat-box" id="chatBox">
                <div class="bot-msg">
                    👋 Hello! I can analyze your resume, calculate ATS score, check job match, and suggest better jobs.
                </div>

                <?php if ($selected_job) { ?>
                    <div class="bot-msg">
                        You are applying for <b><?php echo htmlspecialchars($selected_job['title']); ?></b>
                        at <b><?php echo htmlspecialchars($selected_job['company']); ?></b>.<br>
                        Please upload your resume to check whether this job is suitable for you.
                    </div>
                <?php } ?>
            </div>

            <div class="input-row">
                <input type="text" id="skillsInput" placeholder="Example: PHP, MySQL, HTML, CSS">
                <button id="sendBtn" onclick="sendSkills()">Send</button>
            </div>

        </div>

    </div>

    <div>

        <?php if ($selected_job) { ?>
            <div class="side-card selected-job">
                <h3>Selected Job</h3>
                <p><b><?php echo htmlspecialchars($selected_job['title']); ?></b></p>
                <p><?php echo htmlspecialchars($selected_job['company']); ?></p>
                <p>📍 <?php echo htmlspecialchars($selected_job['location']); ?></p>
                <p>💰 Rs. <?php echo htmlspecialchars($selected_job['salary']); ?></p>
                <p><b>Skills:</b> <?php echo htmlspecialchars($selected_job['required_skills']); ?></p>
            </div>
        <?php } ?>

        <div class="side-card">
            <h3>Upload Resume</h3>
            <p>Upload PDF resume. AI will extract skills and check job suitability.</p>

            <form id="resumeForm" enctype="multipart/form-data">
                <input type="file" name="resume" accept="application/pdf" required>
                <button type="submit" id="uploadBtn">Upload Resume</button>
            </form>
        </div>

        <div class="side-card">
            <h3>Job Filters</h3>
            <input type="text" id="location" class="filter-input" placeholder="Location">
            <input type="number" id="salary" class="filter-input" placeholder="Minimum Salary">
            <button onclick="sendSkills()">Apply Filter</button>
        </div>

    </div>

</div>

<div id="toast"></div>

<script>
function showToast(msg) {
    const toast = document.getElementById("toast");
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

function addMessage(message, type) {
    const chatBox = document.getElementById("chatBox");
    const div = document.createElement("div");
    div.className = type;
    div.innerHTML = message;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function sendSkills() {
    const skills = document.getElementById("skillsInput").value;
    const location = document.getElementById("location").value;
    const salary = document.getElementById("salary").value;

    if (skills.trim() === "") {
        showToast("Please enter your skills.");
        return;
    }

    addMessage(skills, "user-msg");

    fetch("chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "skills=" + encodeURIComponent(skills) +
              "&location=" + encodeURIComponent(location) +
              "&salary=" + encodeURIComponent(salary)
    })
    .then(response => response.text())
    .then(data => {
        addMessage(data, "bot-msg");
        document.getElementById("skillsInput").value = "";
    })
    .catch(() => {
        addMessage("Something went wrong.", "bot-msg");
    });
}

document.getElementById("resumeForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("upload_resume.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        addMessage(data, "bot-msg");
        this.reset();
    })
    .catch(() => {
        addMessage("Resume upload failed.", "bot-msg");
    });
});
</script>

</body>
</html>