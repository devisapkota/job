<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $description = $_POST['description'];
    $skills = $_POST['skills'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];

    $query = "INSERT INTO jobs(title, company, description, required_skills, location, salary)
              VALUES('$title', '$company', '$description', '$skills', '$location', '$salary')";

    if (mysqli_query($conn, $query)) {
        echo "Job added successfully.";
    } else {
        echo "Error adding job.";
    }
}
?>

<h2>Add New Job</h2>

<form method="POST">
    <input type="text" name="title" placeholder="Job Title" required><br><br>
    <input type="text" name="company" placeholder="Company" required><br><br>
    <textarea name="description" placeholder="Job Description" required></textarea><br><br>
    <input type="text" name="skills" placeholder="Required Skills comma separated" required><br><br>
    <input type="text" name="location" placeholder="Location" required><br><br>
    <input type="number" name="salary" placeholder="Salary" required><br><br>
    <button type="submit">Add Job</button>
</form>