<?php
$conn = mysqli_connect("localhost", "root", "", "job_recommendation");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>