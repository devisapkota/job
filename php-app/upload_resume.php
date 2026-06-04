<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'] ?? 1;

if (!isset($_FILES['resume'])) {
    echo "No resume uploaded.";
    exit;
}

$uploadDir = "uploads/";
$fileName = time() . "_" . basename($_FILES["resume"]["name"]);
$filePath = $uploadDir . $fileName;

if (move_uploaded_file($_FILES["resume"]["tmp_name"], $filePath)) {
    $fullPath = realpath($filePath);

    $data = [
        "file_path" => $fullPath
    ];

    $ch = curl_init("http://127.0.0.1:5000/parse-resume");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $extractedText = mysqli_real_escape_string($conn, $result['text']);
    $skills = $result['skills'];

    mysqli_query($conn, "
        INSERT INTO resumes(user_id, file_path, extracted_text)
        VALUES('$user_id', '$filePath', '$extractedText')
    ");

    echo "Resume uploaded successfully.<br>";
    echo "Extracted Skills: " . implode(", ", $skills);
    echo "<br>Please copy these skills and send them in the chatbot.";
} else {
    echo "Resume upload failed.";
}
?>