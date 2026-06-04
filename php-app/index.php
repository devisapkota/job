<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AI Job Recommendation Chatbot</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>AI-Based Job Recommendation Chatbot</h1>

    <div class="chat-box" id="chatBox">
        <div class="bot-msg">
            Hello! Enter your skills or upload your resume to get job recommendations.
        </div>
    </div>

    <div class="input-area">
        <input type="text" id="skillsInput" placeholder="Example: Python, HTML, CSS, MySQL">
        <button onclick="sendSkills()">Send</button>
    </div>

    <form id="resumeForm" enctype="multipart/form-data">
        <input type="file" name="resume" accept="application/pdf" required>
        <button type="submit">Upload Resume</button>
    </form>

    <div class="filters">
        <input type="text" id="location" placeholder="Filter by location">
        <input type="number" id="salary" placeholder="Minimum salary">
        <button onclick="sendSkills()">Apply Filter</button>
    </div>
</div>

<script>
function addMessage(message, type) {
    const chatBox = document.getElementById("chatBox");
    const div = document.createElement("div");
    div.className = type;
    div.innerHTML = message;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function sendSkills() {
    let skills = document.getElementById("skillsInput").value;
    let location = document.getElementById("location").value;
    let salary = document.getElementById("salary").value;

    if (skills.trim() === "") {
        alert("Please enter your skills.");
        return;
    }

    addMessage(skills, "user-msg");

    fetch("chat.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "skills=" + encodeURIComponent(skills) +
              "&location=" + encodeURIComponent(location) +
              "&salary=" + encodeURIComponent(salary)
    })
    .then(response => response.text())
    .then(data => {
        addMessage(data, "bot-msg");
    });
}

document.getElementById("resumeForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("upload_resume.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        addMessage(data, "bot-msg");
    });
});
</script>

</body>
</html>