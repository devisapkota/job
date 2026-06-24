<?php
session_start();
require_once "db.php";

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['name'] ?? "Guest";
$user_initial = $user_id ? strtoupper(substr($user_name, 0, 1)) : "G";

$selected_job = null;

if (isset($_SESSION['selected_job_id'])) {
    $job_id = intval($_SESSION['selected_job_id']);

    $jobResult = mysqli_query($conn, "
        SELECT *
        FROM jobs
        WHERE job_id = '$job_id'
        LIMIT 1
    ");

    if ($jobResult && mysqli_num_rows($jobResult) > 0) {
        $selected_job = mysqli_fetch_assoc($jobResult);
    }
}

/* Fetch jobs for the landing page */
$job_query = "
    SELECT *
    FROM jobs
    WHERE is_external = 0
    ORDER BY job_id DESC
    LIMIT 6
";

$job_result = mysqli_query($conn, $job_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CareerPilot AI | Find Your Dream Job</title>

<link rel="stylesheet" href="style.css">

<style>
.landing-hero {
    padding: 40px 32px;
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    color: white;
    border-radius: var(--r16);
    margin: 20px;
    text-align: center;
}

.landing-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.landing-hero p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 20px;
}

.jobs-section {
    padding: 20px 32px;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.chat-container {
    margin: 20px;
    border: 1px solid var(--border);
    border-radius: var(--r16);
    overflow: hidden;
    background: white;
    height: 600px;
    display: flex;
    flex-direction: column;
}

.app-main {
    height: auto;
    overflow-y: auto;
}

.chat-page-main {
    height: 100%;
}

.alert-box {
    margin: 20px;
    padding: 14px 16px;
    border-radius: 12px;
    font-weight: 600;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.apply-info {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    padding: 12px;
    border-radius: 10px;
    margin-top: 12px;
    line-height: 1.6;
    font-size: 14px;
}

.upload-hint {
    font-size: 12px;
    color: var(--text3);
    text-align: center;
    margin-top: 5px;
}
</style>
</head>

<body class="app-page">

<div class="app-layout">

<?php include "sidebar.php"; ?>

<main class="app-main">

    <div class="landing-hero">
        <h1>Welcome to CareerPilot AI</h1>
        <p>Your AI-powered companion for a smarter career journey.</p>

        <?php if (!$user_id): ?>
            <a href="register.php" class="btn-primary" style="display:inline-block; width:auto; margin-right:8px;">
                Register
            </a>

            <a href="login.php" class="btn-primary" style="display:inline-block; width:auto;">
                Login
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['error_message'])) { ?>
        <div class="alert-box alert-error">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php } ?>

    <?php if (isset($_SESSION['success_message'])) { ?>
        <div class="alert-box alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php } ?>

    <div class="jobs-section">
        <div class="section-head">
            <h3>Available Jobs</h3>
            <a href="user_dashboard.php">View All Jobs</a>
        </div>

        <div class="jobs-grid">

            <?php if ($job_result && mysqli_num_rows($job_result) > 0): ?>

                <?php while ($job = mysqli_fetch_assoc($job_result)): ?>

                    <div class="jcard">

                        <div class="jcard-top">
                            <div class="jcard-icon">
                                <svg width="18" height="18" fill="none" stroke="#1d4ed8" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                </svg>
                            </div>

                            <span class="match-badge">New</span>
                        </div>

                        <h4><?php echo htmlspecialchars($job['title']); ?></h4>

                        <div class="jcard-company">
                            <?php echo htmlspecialchars($job['company']); ?>
                        </div>

                        <div style="font-size: 0.8rem; color: var(--text3); margin-bottom: 8px;">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:3px;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <?php echo htmlspecialchars($job['location']); ?>
                        </div>

                        <div style="font-size: 0.8rem; color: var(--text2); margin-bottom: 12px; height: 3.2em; overflow: hidden;">
                            <?php echo htmlspecialchars(substr($job['description'] ?? '', 0, 100)) . '...'; ?>
                        </div>

                        <div class="jcard-salary">
                            <span>
                                <?php 
                                if (!empty($job['salary']) && $job['salary'] > 0) {
                                    echo "Rs. " . htmlspecialchars($job['salary']);
                                } else {
                                    echo "Negotiable";
                                }
                                ?>
                            </span>
                        </div>

                        <?php if ($user_id): ?>
                            <button 
                                onclick="location.href='apply_job.php?job_id=<?php echo intval($job['job_id']); ?>'" 
                                class="btn-primary" 
                                style="margin-top:15px; padding: 8px 15px; font-size: 0.85rem;">
                                View & Apply
                            </button>
                        <?php else: ?>
                            <button 
                                onclick="location.href='login.php?redirect=<?php echo urlencode('apply_job.php?job_id=' . intval($job['job_id'])); ?>'" 
                                class="btn-primary" 
                                style="margin-top:15px; padding: 8px 15px; font-size: 0.85rem;">
                                Login to Apply
                            </button>
                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div style="grid-column:1/-1; background:white; padding:40px; border-radius:var(--r16); text-align:center;">
                    <h3>No jobs available</h3>
                    <p style="color:var(--text3); margin-top:8px;">
                        Please check again later.
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="chat-container" id="chatSection">
        <div class="chat-page-main">

            <header class="chat-topbar">
                <div class="chat-topbar-left">
                    <div class="chat-bot-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="10" rx="2"/>
                            <circle cx="12" cy="5" r="2"/>
                            <path d="M12 7v4"/>
                            <line x1="8" y1="16" x2="8" y2="16"/>
                            <line x1="16" y1="16" x2="16" y2="16"/>
                        </svg>
                    </div>

                    <div>
                        <div class="chat-bot-name">CareerPilot AI Assistant</div>
                        <div class="chat-bot-status">
                            <span class="status-dot"></span> Online
                        </div>
                    </div>
                </div>
            </header>

            <div class="chat-messages" id="chatBox">

                <div class="chat-date-label">Today</div>

                <div class="msg-bot">
                    <div class="msg-bot-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="10" rx="2"/>
                            <circle cx="12" cy="5" r="2"/>
                            <path d="M12 7v4"/>
                            <line x1="8" y1="16" x2="8" y2="16"/>
                            <line x1="16" y1="16" x2="16" y2="16"/>
                        </svg>
                    </div>

                    <div class="msg-bot-bubble">
                        Hello! I am CareerPilot AI. I can help you find jobs, analyze your resume, calculate ATS score, and check job match score.

                        <?php if (!$user_id): ?>
                            <br><br>
                            <b>Note:</b> Please 
                            <a href="login.php" style="color:var(--blue); font-weight:600;">login</a> 
                            to upload resume and apply for jobs.
                        <?php else: ?>
                            <div class="apply-info">
                                To apply for any job, first upload your resume. CareerPilot AI will check your ATS score and job match score.
                                If your match score is below <b>25%</b>, you cannot apply for that job.
                            </div>
                        <?php endif; ?>

                        <div class="msg-tags">
                            <span class="msg-tag">#ATSScore</span>
                            <span class="msg-tag">#JobMatching</span>
                            <span class="msg-tag">#ResumeRequired</span>
                        </div>
                    </div>
                </div>

                <?php if ($selected_job) { ?>
                    <div class="msg-bot">
                        <div class="msg-bot-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="10" rx="2"/>
                                <circle cx="12" cy="5" r="2"/>
                                <path d="M12 7v4"/>
                                <line x1="8" y1="16" x2="8" y2="16"/>
                                <line x1="16" y1="16" x2="16" y2="16"/>
                            </svg>
                        </div>

                        <div class="msg-bot-bubble">
                            You selected 
                            <b><?php echo htmlspecialchars($selected_job['title']); ?></b>
                            at 
                            <b><?php echo htmlspecialchars($selected_job['company']); ?></b>.

                            <br><br>

                            <?php if ($user_id): ?>
                                Please upload your resume using the attachment button below.
                                CareerPilot AI will analyze your resume, check your ATS score, and calculate your match score for this job.

                                <br><br>

                                <b>Apply Rule:</b> If your match score is below <b>25%</b>, you cannot apply for this job.
                            <?php else: ?>
                                Please 
                                <a href="login.php?redirect=apply_job.php?job_id=<?php echo intval($selected_job['job_id']); ?>" style="color:var(--blue); font-weight:600;">
                                    login
                                </a> 
                                to upload resume and apply.
                            <?php endif; ?>
                        </div>
                    </div>
                <?php } ?>

                <?php
                if ($user_id) {
                    $chatHistory = mysqli_query($conn, "
                        SELECT *
                        FROM chat_messages
                        WHERE user_id = '$user_id'
                        ORDER BY message_id ASC
                    ");

                    if ($chatHistory) {
                        while ($msg = mysqli_fetch_assoc($chatHistory)) {

                            $sender = $msg['sender'];

                            if ($sender == 'user' || $sender == 'user-msg') {
                                echo "
                                <div class='msg-user'>
                                    <div class='msg-user-bubble'>" . htmlspecialchars($msg['message']) . "</div>
                                    <div class='msg-user-avatar'>" . htmlspecialchars($user_initial) . "</div>
                                </div>";
                            } else {
                                echo "
                                <div class='msg-bot'>
                                    <div class='msg-bot-icon'>
                                        <svg width='16' height='16' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'>
                                            <rect x='3' y='11' width='18' height='10' rx='2'/>
                                            <circle cx='12' cy='5' r='2'/>
                                            <path d='M12 7v4'/>
                                            <line x1='8' y1='16' x2='8' y2='16'/>
                                            <line x1='16' y1='16' x2='16' y2='16'/>
                                        </svg>
                                    </div>
                                    <div class='msg-bot-bubble'>" . $msg['message'] . "</div>
                                </div>";
                            }
                        }
                    }
                }
                ?>

            </div>

            <div class="chat-chips">
                <button class="chip" onclick="quickSend('Check my ATS score')">Check ATS Score</button>
                <button class="chip" onclick="quickSend('Suggest jobs for me')">Suggest Jobs</button>
            </div>

            <div class="chat-input-bar">
                <div class="chat-input-inner">

                    <label class="chat-attach" for="resumeFileInput" title="Upload Resume">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                        </svg>
                    </label>

                    <form id="resumeForm" style="display:none;">
                        <input type="file" id="resumeFileInput" name="resume" accept="application/pdf">
                    </form>

                    <input type="text" id="skillsInput" placeholder="Ask anything or enter your skills...">

                    <button class="chat-send" id="sendBtn" type="button">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </button>

                </div>

                <div class="chat-disclaimer">
                    CareerPilot AI Assistant
                </div>

                <?php if ($user_id): ?>
                    <div class="upload-hint">
                        Click the paperclip icon to upload your resume PDF.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</main>
</div>

<div id="toast"></div>

<script>
const isLoggedIn = <?php echo $user_id ? 'true' : 'false'; ?>;

function escapeHTML(str) {
    return str.replace(/[&<>"']/g, function(match) {
        const escape = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return escape[match];
    });
}

function showToast(msg) {
    const toast = document.getElementById("toast");
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

function saveMessage(message, sender) {
    if (!isLoggedIn) return;

    fetch("save_chat.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message) +
              "&sender=" + encodeURIComponent(sender)
    });
}

function addMessage(message, type, save = true) {
    const chatBox = document.getElementById("chatBox");
    const div = document.createElement("div");

    if (type === "user-msg") {
        div.className = "msg-user";
        div.innerHTML = `
            <div class="msg-user-bubble">${message}</div>
            <div class="msg-user-avatar"><?php echo htmlspecialchars($user_initial); ?></div>
        `;
    } else {
        div.className = "msg-bot";
        div.innerHTML = `
            <div class="msg-bot-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="10" rx="2"/>
                    <circle cx="12" cy="5" r="2"/>
                    <path d="M12 7v4"/>
                    <line x1="8" y1="16" x2="8" y2="16"/>
                    <line x1="16" y1="16" x2="16" y2="16"/>
                </svg>
            </div>
            <div class="msg-bot-bubble">${message}</div>
        `;
    }

    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;

    if (save) {
        saveMessage(message, type);
    }
}

function quickSend(text) {
    if (!isLoggedIn) {
        addMessage(text, "user-msg", false);

        setTimeout(() => {
            addMessage("Please <a href='login.php' style='color:var(--blue); font-weight:600;'>login</a> to use this feature.", "bot-msg", false);
        }, 500);

        return;
    }

    document.getElementById("skillsInput").value = text;
    sendSkills();
}

function sendSkills() {
    const skillsInput = document.getElementById("skillsInput");
    const skills = skillsInput.value.trim();

    if (skills === "") {
        showToast("Please enter a message.");
        return;
    }

    addMessage(escapeHTML(skills), "user-msg");
    skillsInput.value = "";

    if (!isLoggedIn) {
        setTimeout(() => {
            addMessage("Please <a href='login.php' style='color:var(--blue); font-weight:600;'>login</a> to interact with CareerPilot AI.", "bot-msg", false);
        }, 500);

        return;
    }

    fetch("chat.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "skills=" + encodeURIComponent(skills)
    })
    .then(res => res.text())
    .then(data => {
        addMessage(data, "bot-msg");
    })
    .catch(err => {
        console.error(err);
        addMessage("Sorry, I encountered an error. Please try again.", "bot-msg");
    });
}

document.getElementById("sendBtn").addEventListener("click", sendSkills);

document.getElementById("skillsInput").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        sendSkills();
    }
});

document.getElementById("resumeFileInput").addEventListener("change", function() {
    if (!isLoggedIn) {
        showToast("Please login to upload your resume.");
        return;
    }

    if (!this.files || !this.files[0]) {
        return;
    }

    const fileInput = this;
    const fileName = fileInput.files[0].name;

    const formData = new FormData();
    formData.append("resume", fileInput.files[0]);

    const cleanFileMessage = "Uploaded resume: " + fileName;

    addMessage(escapeHTML(cleanFileMessage), "user-msg");

    fetch("upload_resume.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        addMessage("Resume uploaded successfully.", "bot-msg");
        addMessage(data, "bot-msg");
        fileInput.value = "";
    })
    .catch(err => {
        console.error(err);
        addMessage("Resume upload failed.", "bot-msg");
        fileInput.value = "";
    });
});
</script>

</body>
</html>