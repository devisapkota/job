<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
}

$result = mysqli_query($conn, "SELECT * FROM jobs");
?>

<h2>Admin Dashboard</h2>
<a href="add_job.php">Add New Job</a>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Company</th>
        <th>Skills</th>
        <th>Location</th>
        <th>Salary</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['company']; ?></td>
        <td><?php echo $row['required_skills']; ?></td>
        <td><?php echo $row['location']; ?></td>
        <td><?php echo $row['salary']; ?></td>
    </tr>
    <?php } ?>
</table>