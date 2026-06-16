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
        <div class="chat-bot-icon">🤖</div>
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
        <div class="msg-bot-icon">🤖</div>
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
            <div class="msg-bot-icon">🤖</div>
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
                    <div class='msg-bot-icon'>🤖</div>
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

        <label class="chat-attach" for="resumeFileInput" title="Upload Resume">📎</label>

        <form id="resumeForm" style="display:none;">
            <input type="file" id="resumeFileInput" name="resume" accept="application/pdf">
        </form>

        <input type="text" id="skillsInput" placeholder="Ask anything or enter your skills...">

        <button class="chat-send" id="sendBtn" type="button">➤</button>
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
            <div class="msg-bot-icon">🤖</div>
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

    addMessage("📄 " + fileName, "user-msg");

    fetch("upload_resume.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        addMessage("✅ Resume uploaded successfully", "bot-msg");
        addMessage(data, "bot-msg");
        fileInput.value = "";
    })
    .catch(err => {
        console.error(err);
        addMessage("❌ Resume upload failed.", "bot-msg");
        fileInput.value = "";
    });
});
</script>

</body>
</html>