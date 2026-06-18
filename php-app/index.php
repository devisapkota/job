<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant | CareerPilot</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="app-page">

<div class="app-layout">

<?php include "sidebar.php"; ?>

<main class="app-main">
<div class="chat-page-main">

<header class="chat-topbar">
    <div class="chat-topbar-left">
        <div class="chat-bot-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg></div>
        <div>
            <div class="chat-bot-name">CareerPilot AI</div>
            <div class="chat-bot-status">
                <span class="status-dot"></span> Online Assistant
            </div>
        </div>
    </div>
</header>

<div class="chat-messages" id="chatBox">
    <div class="chat-date-label">Today</div>

    <div class="msg-bot">
        <div class="msg-bot-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg></div>
        <div class="msg-bot-bubble">
            Hello! I am your Career Assistant. I can analyze your resume, calculate ATS scores, and suggest matching jobs.
            <div class="msg-tags">
                <span class="msg-tag">#ATSScore</span>
                <span class="msg-tag">#ResumeParsing</span>
                <span class="msg-tag">#JobMatching</span>
            </div>
        </div>
    </div>

    <?php if ($selected_job) { ?>
        <div class="msg-bot">
            <div class="msg-bot-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg></div>
            <div class="msg-bot-bubble">
                You are interested in <b><?php echo htmlspecialchars($selected_job['title']); ?></b>
                at <b><?php echo htmlspecialchars($selected_job['company']); ?></b>.<br>
                Please upload your resume so I can check your compatibility.
            </div>
        </div>
    <?php } ?>

    <?php
    $chatHistory = mysqli_query($conn, "
        SELECT * FROM chat_messages
        WHERE user_id = '$user_id'
        ORDER BY message_id ASC
    ");

    if ($chatHistory) {
        while ($msg = mysqli_fetch_assoc($chatHistory)) {
            if ($msg['sender'] == 'user-msg') {
                echo "
                <div class='msg-user'>
                    <div class='msg-user-bubble'>" . $msg['message'] . "</div>
                    <div class='msg-user-avatar'>$user_initial</div>
                </div>";
            } else {
                echo "
                <div class='msg-bot'>
                    <div class='msg-bot-icon'><svg width='16' height='16' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><rect x='3' y='11' width='18' height='10' rx='2'/><circle cx='12' cy='5' r='2'/><path d='M12 7v4'/><line x1='8' y1='16' x2='8' y2='16'/><line x1='16' y1='16' x2='16' y2='16'/></svg></div>
                    <div class='msg-bot-bubble'>" . $msg['message'] . "</div>
                </div>";
            }
        }
    }
    ?>
</div>

<div class="chat-chips">
    <button class="chip" onclick="quickSend('Check my ATS score')">Check ATS Score</button>
    <button class="chip" onclick="quickSend('Suggest jobs for me')">Suggest Jobs</button>
    <button class="chip" onclick="quickSend('Improve my resume')">Resume Tips</button>
</div>

<div class="chat-input-bar">
    <div class="chat-input-inner">

        <label class="chat-attach" for="resumeFileInput" title="Upload Resume"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></label>

        <form id="resumeForm" style="display:none;">
            <input type="file" id="resumeFileInput" name="resume" accept="application/pdf">
        </form>

        <input type="text" id="skillsInput" placeholder="Ask anything or enter your skills...">

        <button class="chat-send" id="sendBtn" type="button"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></button>
    </div>

    <div class="chat-disclaimer">
        CareerPilot AI may provide suggestions based on parsed data.
    </div>
</div>

</div>
</main>
</div>

<div id="toast"></div>

<script>
function showToast(msg) {
    const toast = document.getElementById("toast");
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

function saveMessage(message, sender) {
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
            <div class="msg-user-avatar"><?php echo $user_initial; ?></div>
        `;
    } else {
        div.className = "msg-bot";
        div.innerHTML = `
            <div class="msg-bot-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg></div>
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

    addMessage(skills, "user-msg");
    skillsInput.value = "";

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

    if (!this.files || !this.files[0]) {
        return;
    }

    const fileInput = this;
    const fileName = fileInput.files[0].name;

    const formData = new FormData();
    formData.append("resume", fileInput.files[0]);

    addMessage("<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/></svg> " + fileName, "user-msg");

    fetch("upload_resume.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        addMessage("<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><polyline points=\'20 6 9 17 4 12\'/></svg> Resume uploaded successfully", "bot-msg");
        addMessage(data, "bot-msg");
        fileInput.value = "";
    })
    .catch(err => {
        console.error(err);
        addMessage("<svg width=\'16\' height=\'16\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' viewBox=\'0 0 24 24\' style=\'vertical-align:middle;margin-right:4px;\'><line x1=\'18\' y1=\'6\' x2=\'6\' y2=\'18\'/><line x1=\'6\' y1=\'6\' x2=\'18\' y2=\'18\'/></svg> Resume upload failed.", "bot-msg");
        fileInput.value = "";
    });
});
</script>

</body>
</html>